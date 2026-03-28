<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Pages\Auth\VerifyEmail;
use App\Mail\GeneralFeeReceiptMail;
use App\Models\ClassRegistration;
use App\Models\Fee;
use App\Models\Transaction;
use App\Services\KkiapayService;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GeneralFees extends Page
{
    protected static ?string $slug = 'frais-generaux';

    protected static ?string $navigationLabel = 'Frais Généraux';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected string $view = 'filament.portal.pages.general-fees';

    public ?string $selectedFeeId = null;

    public bool $showModal = false;

    public string $phoneNumber = '';

    public bool $processingPayment = false;

    public int $pendingAmount = 0;

    public ?string $pendingFeeId = null;

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

        $this->registrationAcademicYear = $accepted?->transaction?->fee?->academic_year ?? '';
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if (! $user?->verified) {
            return null;
        }

        $accepted = ClassRegistration::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->with('transaction.fee')
            ->latest()
            ->first();

        if (! $accepted) {
            return null;
        }

        $academicYear = $accepted->transaction?->fee?->academic_year;

        if (! $academicYear) {
            return null;
        }

        $feeIds = Fee::where('type', 'App\Models\GeneralFee')
            ->where('grade_id', $accepted->grade_id)
            ->where('academic_year', $academicYear)
            ->pluck('id');

        if ($feeIds->isEmpty()) {
            return null;
        }

        $paidFeeIds = Transaction::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereIn('fee_id', $feeIds)
            ->pluck('fee_id');

        $unpaid = $feeIds->diff($paidFeeIds)->count();

        return $unpaid > 0 ? (string) $unpaid : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public function openPayModal(string $feeId): void
    {
        $paidFeeIds = $this->getPaidFeeIds();

        if (in_array($feeId, $paidFeeIds)) {
            Notification::make()
                ->title('Frais déjà réglé')
                ->warning()
                ->send();
            return;
        }

        $this->selectedFeeId     = $feeId;
        $this->phoneNumber       = '';
        $this->processingPayment = false;
        $this->showModal         = true;
    }

    public function closeModal(): void
    {
        $this->showModal         = false;
        $this->processingPayment = false;
        $this->phoneNumber       = '';
        $this->selectedFeeId     = null;
    }

    public function initiatePayment(): void
    {
        $this->validate(['phoneNumber' => 'required|min:8|max:20']);

        if (! $this->selectedFeeId) {
            Notification::make()
                ->title('Aucun frais sélectionné')
                ->danger()
                ->send();
            return;
        }

        $fee = Fee::find($this->selectedFeeId);

        if (! $fee) {
            Notification::make()
                ->title('Frais introuvable')
                ->danger()
                ->send();
            return;
        }

        $paidFeeIds = $this->getPaidFeeIds();

        if (in_array($fee->id, $paidFeeIds)) {
            Notification::make()
                ->title('Ce frais est déjà réglé')
                ->warning()
                ->send();
            return;
        }

        $fine                    = $this->computeFine($fee);
        $this->pendingFeeId      = $fee->id;
        $this->pendingAmount     = (int) $fee->total_amount + $fine;
        $this->processingPayment = true;

        $this->dispatch('open-kkiapay-widget',
            amount: $this->pendingAmount,
            phone:  $this->phoneNumber,
        );
    }

    public function handleKkiapaySuccess(string $kkiapayRef): void
    {
        if (! $this->pendingFeeId) {
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

        $user = auth()->user();
        $fee  = Fee::with('grade')->find($this->pendingFeeId);

        $transaction = Transaction::create([
            'user_id'           => $user->id,
            'fee_id'            => $fee->id,
            'amount'            => $this->pendingAmount,
            'date'              => now()->toDateString(),
            'status'            => 'completed',
            'kkiapay_reference' => $kkiapayRef,
            'phone_number'      => $this->phoneNumber,
        ]);

        $pdf     = Pdf::loadView('pdf.general-fee-receipt', [
            'transaction' => $transaction,
            'fee'         => $fee,
            'user'        => $user,
        ]);
        $pdfPath = 'receipts/' . $user->id . '/general-fee-' . $kkiapayRef . '.pdf';
        Storage::disk('supabase')->put($pdfPath, $pdf->output());

        Mail::to($user->email)->queue(new GeneralFeeReceiptMail($transaction, $fee, $user, $pdfPath));

        $this->showModal         = false;
        $this->selectedFeeId     = null;
        $this->phoneNumber       = '';
        $this->pendingFeeId      = null;
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

    public function computeFine(Fee $fee): int
    {
        if (! $fee->due_before || $fee->due_before->isFuture() || ! $fee->late_fine_per_week) {
            return 0;
        }

        $weeksPast = (int) ceil($fee->due_before->startOfDay()->diffInDays(now()->startOfDay()) / 7);

        return max(0, $weeksPast) * (int) $fee->late_fine_per_week;
    }

    protected function getPaidFeeIds(): array
    {
        return Transaction::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->whereNotNull('fee_id')
            ->pluck('fee_id')
            ->toArray();
    }

    protected function getViewData(): array
    {
        $acceptedRegistration = ClassRegistration::where('user_id', auth()->id())
            ->where('status', 'accepted')
            ->with(['grade', 'transaction.fee'])
            ->latest()
            ->first();

        $fees = collect();

        if ($acceptedRegistration && $this->registrationAcademicYear) {
            $fees = Fee::where('type', 'App\Models\GeneralFee')
                ->where('grade_id', $acceptedRegistration->grade_id)
                ->where('academic_year', $this->registrationAcademicYear)
                ->with('grade')
                ->orderBy('due_before')
                ->get();
        }

        $paidFeeIds = $fees->isNotEmpty()
            ? Transaction::where('user_id', auth()->id())
                ->where('status', 'completed')
                ->whereIn('fee_id', $fees->pluck('id'))
                ->pluck('fee_id')
                ->toArray()
            : [];

        $selectedFee = $this->selectedFeeId
            ? $fees->firstWhere('id', $this->selectedFeeId)
            : null;

        return compact('acceptedRegistration', 'fees', 'paidFeeIds', 'selectedFee');
    }

    public function getTitle(): string
    {
        return 'Frais Généraux';
    }
}