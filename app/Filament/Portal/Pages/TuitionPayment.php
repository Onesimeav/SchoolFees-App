<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Pages\Auth\VerifyEmail;
use App\Mail\TuitionReceiptMail;
use App\Models\ClassRegistration;
use App\Models\Fee;
use App\Models\Installment;
use App\Models\Transaction;
use App\Services\KkiapayService;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TuitionPayment extends Page
{
    protected static ?string $slug = 'scolarite';

    protected static ?string $navigationLabel = 'Mes Frais de Scolarité';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected string $view = 'filament.portal.pages.tuition-payment';

    public ?string $tuitionFeeId = null;

    public ?string $acceptedGradeId = null;

    public array $selectedIds = [];

    public bool $payAll = false;

    public bool $showModal = false;

    public string $phoneNumber = '';

    public bool $processingPayment = false;

    public int $pendingAmount = 0;

    public array $pendingIds = [];

    public string $registrationAcademicYear = '';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user?->verified) {
            $this->redirect(VerifyEmail::getUrl());
            return;
        }

        $accepted = ClassRegistration::where('user_id', auth()->id())
            ->where('status', 'accepted')
            ->with('transaction.fee')
            ->latest()
            ->first();

        if (! $accepted) {
            return;
        }

        $this->acceptedGradeId            = $accepted->grade_id;
        $this->registrationAcademicYear   = $accepted->transaction?->fee?->academic_year ?? '';

        if (! $this->registrationAcademicYear) {
            return;
        }

        $fee = Fee::where('type', 'App\Models\TuitionFee')
            ->where('grade_id', $accepted->grade_id)
            ->where('academic_year', $this->registrationAcademicYear)
            ->latest()
            ->first();

        $this->tuitionFeeId = $fee?->id;
    }

    public function openModal(bool $all = false): void
    {
        $this->payAll = $all;

        if (! $all && empty($this->selectedIds)) {
            Notification::make()
                ->title('Sélectionnez au moins un versement')
                ->warning()
                ->send();
            return;
        }

        $this->phoneNumber       = '';
        $this->processingPayment = false;
        $this->showModal         = true;
    }

    public function closeModal(): void
    {
        $this->showModal         = false;
        $this->processingPayment = false;
        $this->phoneNumber       = '';
    }

    public function initiatePayment(): void
    {
        $this->validate(['phoneNumber' => 'required|min:8|max:20']);

        if (! $this->tuitionFeeId) {
            Notification::make()
                ->title('Aucun frais de scolarité trouvé')
                ->danger()
                ->send();
            return;
        }

        $fee    = Fee::find($this->tuitionFeeId);
        $unpaid = $this->getUnpaidInstallments($fee);

        if ($unpaid->isEmpty()) {
            Notification::make()
                ->title('Tous les versements sont déjà réglés')
                ->warning()
                ->send();
            return;
        }

        $toPay = $this->payAll
            ? $unpaid
            : $unpaid->filter(fn ($i) => in_array($i->id, $this->selectedIds))->values();

        if ($toPay->isEmpty()) {
            Notification::make()
                ->title('Sélectionnez au moins un versement non payé')
                ->warning()
                ->send();
            return;
        }

        $total = 0;
        foreach ($toPay as $inst) {
            $total += (int) $inst->amount + $this->computeFine($inst, $fee->late_fine_per_week);
        }

        $this->pendingIds        = $toPay->pluck('id')->toArray();
        $this->pendingAmount     = $total;
        $this->processingPayment = true;

        $this->dispatch('open-kkiapay-widget',
            amount: $this->pendingAmount,
            phone:  $this->phoneNumber,
        );
    }

    public function handleKkiapaySuccess(string $kkiapayRef): void
    {
        if (! $this->tuitionFeeId || empty($this->pendingIds)) {
            return;
        }

        if (! app(KkiapayService::class)->verify($kkiapayRef)) {
            Notification::make()
                ->title('Paiement non vérifié')
                ->body('La transaction n\'a pas pu être vérifiée. Veuillez contacter l\'administration.')
                ->danger()
                ->send();
            $this->processingPayment = false;
            return;
        }

        $user         = auth()->user();
        $fee          = Fee::with('grade')->find($this->tuitionFeeId);
        $installments = Installment::whereIn('id', $this->pendingIds)->get()->keyBy('id');
        $created      = collect();

        foreach ($this->pendingIds as $id) {
            $inst = $installments[$id];
            $fine = $this->computeFine($inst, $fee->late_fine_per_week);

            $created->push(Transaction::create([
                'user_id'           => $user->id,
                'fee_id'            => $fee->id,
                'installment_id'    => $inst->id,
                'amount'            => (int) $inst->amount + $fine,
                'date'              => now()->toDateString(),
                'status'            => 'completed',
                'kkiapay_reference' => $kkiapayRef,
                'phone_number'      => $this->phoneNumber,
            ]));
        }

        $pdf     = Pdf::loadView('pdf.tuition-receipt', [
            'transactions' => $created,
            'installments' => $installments,
            'fee'          => $fee,
            'user'         => $user,
        ]);
        $pdfPath = 'receipts/' . $user->id . '/tuition-' . $kkiapayRef . '.pdf';
        Storage::disk('supabase')->put($pdfPath, $pdf->output());

        Mail::to($user->email)->queue(new TuitionReceiptMail($created, $fee, $user, $pdfPath));

        $this->showModal         = false;
        $this->selectedIds       = [];
        $this->payAll            = false;
        $this->phoneNumber       = '';
        $this->pendingIds        = [];
        $this->pendingAmount     = 0;
        $this->processingPayment = false;

        Notification::make()
            ->title('Paiement enregistré !')
            ->body('Un reçu vous a été envoyé par email.')
            ->success()
            ->send();
    }

    public function handleKkiapayFailure(): void
    {
        $this->processingPayment = false;

        Notification::make()
            ->title('Paiement échoué')
            ->body('Le paiement n\'a pas abouti. Veuillez réessayer.')
            ->danger()
            ->send();
    }

    protected function getUnpaidInstallments(Fee $fee): \Illuminate\Support\Collection
    {
        $paidIds = Transaction::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->whereNotNull('installment_id')
            ->where('fee_id', $fee->id)
            ->pluck('installment_id')
            ->toArray();

        return Installment::where('tuition_fee_id', $fee->id)
            ->whereNotIn('id', $paidIds)
            ->orderBy('number')
            ->get();
    }

    protected function computeFine(Installment $inst, ?float $lateFinePer): int
    {
        if (! $lateFinePer || $inst->due_date->isFuture()) {
            return 0;
        }

        $weeksPast = (int) ceil($inst->due_date->startOfDay()->diffInDays(now()->startOfDay()) / 7);

        return max(0, $weeksPast) * (int) $lateFinePer;
    }

    protected function getViewData(): array
    {
        $acceptedRegistration = ClassRegistration::where('user_id', auth()->id())
            ->where('status', 'accepted')
            ->with(['grade', 'transaction.fee'])
            ->latest()
            ->first();

        $tuitionFee = $this->tuitionFeeId
            ? Fee::with(['installments' => fn ($q) => $q->orderBy('number'), 'grade'])
                ->find($this->tuitionFeeId)
            : null;

        $paidInstallmentIds = $this->tuitionFeeId
            ? Transaction::where('user_id', auth()->id())
                ->where('status', 'completed')
                ->whereNotNull('installment_id')
                ->where('fee_id', $this->tuitionFeeId)
                ->pluck('installment_id')
                ->toArray()
            : [];

        // Pre-compute fines and totals for the blade
        $fines         = [];
        $computedTotal = 0;

        if ($tuitionFee) {
            foreach ($tuitionFee->installments as $inst) {
                $fines[$inst->id] = in_array($inst->id, $paidInstallmentIds)
                    ? 0
                    : $this->computeFine($inst, $tuitionFee->late_fine_per_week);
            }

            $toCompute = $this->payAll
                ? $tuitionFee->installments->filter(fn ($i) => ! in_array($i->id, $paidInstallmentIds))
                : $tuitionFee->installments->filter(fn ($i) => in_array($i->id, $this->selectedIds) && ! in_array($i->id, $paidInstallmentIds));

            foreach ($toCompute as $inst) {
                $computedTotal += (int) $inst->amount + ($fines[$inst->id] ?? 0);
            }
        }

        return compact('acceptedRegistration', 'tuitionFee', 'paidInstallmentIds', 'fines', 'computedTotal');
    }

    public function getTitle(): string
    {
        return 'Mes Frais de Scolarité';
    }
}