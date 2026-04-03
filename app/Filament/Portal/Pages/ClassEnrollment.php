<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Pages\Auth\VerifyEmail;
use App\Filament\Portal\Pages\TuitionPayment;
use App\Mail\RegistrationReceiptMail;
use App\Models\ClassRegistration;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Transaction;
use App\Services\KkiapayService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
class ClassEnrollment extends Page
{
    protected static ?string $slug = 'inscription';

    protected static ?string $navigationLabel = 'Mon Inscription';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected string $view = 'filament.portal.pages.class-enrollment';

    public ?ClassRegistration $currentRegistration = null;

    public ?string $selectedGradeId = null;

    public bool $showModal = false;

    public string $phoneNumber = '';

    public string $nextAcademicYear = '';

    public ?string $pendingFeeId = null;

    public int $pendingAmount = 0;

    public bool $processingPayment = false;

    public string $selectedGradeName = '';

    public int $selectedFeeAmount = 0;

    public ?string $selectedFeeDueBefore = null;

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user?->verified) {
            $this->redirect(VerifyEmail::getUrl());
            return;
        }

        $year = now()->year;
        $this->nextAcademicYear = $year . '-' . ($year + 1);

        $this->loadRegistration();
    }

    protected function loadRegistration(): void
    {
        $this->currentRegistration = ClassRegistration::where('user_id', auth()->id())
            ->with(['grade', 'transaction.fee', 'user'])
            ->latest()
            ->first();
    }

    public function openModal(string $gradeId): void
    {
        if ($this->currentRegistration && in_array($this->currentRegistration->status, ['pending', 'accepted'])) {
            Notification::make()
                ->title('Inscription déjà enregistrée')
                ->body('Vous avez déjà une inscription en cours.')
                ->warning()
                ->send();
            return;
        }

        $grade = Grade::find($gradeId);

        $fee = Fee::where('type', 'App\Models\RegistrationFee')
            ->where('grade_id', $gradeId)
            ->where('academic_year', $this->nextAcademicYear)
            ->latest()
            ->first();

        $this->selectedGradeId      = $gradeId;
        $this->selectedGradeName    = $grade?->name ?? '';
        $this->selectedFeeAmount    = (int) ($fee?->total_amount ?? 0);
        $this->selectedFeeDueBefore = $fee?->due_before?->format('d/m/Y');
        $this->phoneNumber          = '';
        $this->pendingFeeId         = null;
        $this->pendingAmount        = 0;
        $this->processingPayment    = false;
        $this->showModal            = true;
    }

    public function closeModal(): void
    {
        $this->showModal         = false;
        $this->selectedGradeId   = null;
        $this->phoneNumber       = '';
        $this->pendingFeeId      = null;
        $this->pendingAmount     = 0;
        $this->processingPayment = false;
    }

    public function initiatePayment(): void
    {
        if ($this->currentRegistration && in_array($this->currentRegistration->status, ['pending', 'accepted'])) {
            Notification::make()
                ->title('Inscription déjà enregistrée')
                ->body('Vous avez déjà une inscription active.')
                ->warning()
                ->send();
            $this->showModal = false;
            return;
        }

        if (! $this->selectedGradeId) {
            return;
        }

        $this->validate(['phoneNumber' => 'required|min:8|max:20']);

        $fee = Fee::where('type', 'App\Models\RegistrationFee')
            ->where('grade_id', $this->selectedGradeId)
            ->where('academic_year', $this->nextAcademicYear)
            ->latest()
            ->first();

        if (! $fee) {
            Notification::make()
                ->title('Frais introuvables')
                ->body('Aucun frais d\'inscription pour l\'année ' . $this->nextAcademicYear . ' n\'est associé à cette classe.')
                ->danger()
                ->send();
            return;
        }

        $this->pendingFeeId      = $fee->id;
        $this->pendingAmount     = (int) $fee->total_amount;
        $this->processingPayment = true;

        $this->dispatch('open-kkiapay-widget',
            amount: $this->pendingAmount,
            phone:  $this->phoneNumber,
        );
    }

    public function handleKkiapaySuccess(string $kkiapayRef): void
    {
        if ($this->currentRegistration && in_array($this->currentRegistration->status, ['pending', 'accepted'])) {
            return;
        }

        if (! $this->pendingFeeId || ! $this->selectedGradeId) {
            return;
        }

        if (! app(KkiapayService::class)->verify($kkiapayRef)) {
            Notification::make()
                ->title('Paiement non vérifié')
                ->body('La transaction n\'a pas pu être vérifiée. Veuillez contacter l\'administration.')
                ->danger()
                ->send();
            return;
        }

        $user = auth()->user();

        $transaction = Transaction::create([
            'user_id'           => $user->id,
            'fee_id'            => $this->pendingFeeId,
            'amount'            => $this->pendingAmount,
            'date'              => now()->toDateString(),
            'status'            => 'completed',
            'kkiapay_reference' => $kkiapayRef,
            'phone_number'      => $this->phoneNumber,
        ]);

        $this->currentRegistration = ClassRegistration::create([
            'user_id'        => $user->id,
            'grade_id'       => $this->selectedGradeId,
            'status'         => 'pending',
            'transaction_id' => $transaction->id,
        ]);

        $this->currentRegistration->load(['grade', 'transaction.fee', 'user']);

        $pdf      = Pdf::loadView('pdf.registration-receipt', ['registration' => $this->currentRegistration]);
        $filename = 'receipts/' . $user->id . '/' . $this->currentRegistration->id . '.pdf';
        Storage::disk('supabase')->put($filename, $pdf->output());

        Mail::to($user->email)->queue(new RegistrationReceiptMail($this->currentRegistration, $filename));

        $this->showModal       = false;
        $this->selectedGradeId = null;
        $this->phoneNumber     = '';
        $this->pendingFeeId    = null;
        $this->pendingAmount   = 0;

        Notification::make()
            ->title('Paiement et inscription enregistrés !')
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

    /**
     * Injected into the blade view on every render — never stored in the wire
     * snapshot, so the ->latest() ordering on registrationFees is always preserved.
     */
    protected function getViewData(): array
    {
        $academicYear = $this->nextAcademicYear;

        return [
            'grades' => Grade::whereHas('registrationFees', fn ($q) => $q->where('academic_year', $academicYear))
                ->with(['registrationFees' => fn ($q) => $q->where('academic_year', $academicYear)->latest()])
                ->orderBy('name')
                ->get(),
            'tuitionUrl' => TuitionPayment::getUrl(),
        ];
    }

    public function getTitle(): string
    {
        return 'Mon Inscription';
    }
}