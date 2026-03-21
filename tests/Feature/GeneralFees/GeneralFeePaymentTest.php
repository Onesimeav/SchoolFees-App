<?php

namespace Tests\Feature\GeneralFees;

use App\Filament\Portal\Pages\Dashboard;
use App\Filament\Portal\Pages\GeneralFees;
use App\Mail\GeneralFeeReceiptMail;
use App\Models\ClassRegistration;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Transaction;
use App\Models\User;
use App\Services\KkiapayService;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GeneralFeePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected Grade $grade;
    protected Fee $generalFee;
    protected ClassRegistration $accepted;
    protected string $registrationAcademicYear;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'accountant']);
        Role::create(['name' => 'secretary']);
        Role::create(['name' => 'employee']);
        Role::create(['name' => 'parent_student']);

        $this->registrationAcademicYear = now()->year . '-' . (now()->year + 1);

        $this->student = User::factory()->create(['verified' => true]);
        $this->student->assignRole('parent_student');

        $this->grade = Grade::factory()->create(['name' => 'Terminal']);

        // Registration fee so the grade is associated
        $regFee = Fee::create([
            'type'          => 'App\Models\RegistrationFee',
            'title'         => 'Frais inscription Terminal',
            'grade_id'      => $this->grade->id,
            'total_amount'  => 5000,
            'academic_year' => $this->registrationAcademicYear,
            'due_before'    => now()->addMonths(1),
        ]);

        // General fee for the grade (current academic year)
        $this->generalFee = Fee::create([
            'type'               => 'App\Models\GeneralFee',
            'title'              => 'Frais de sortie scolaire',
            'grade_id'           => $this->grade->id,
            'total_amount'       => 15000,
            'academic_year'      => $this->registrationAcademicYear,
            'due_before'         => now()->addMonths(1),
            'late_fine_per_week' => 1000,
        ]);

        // Accept the student's registration
        $regTransaction = Transaction::create([
            'user_id'      => $this->student->id,
            'fee_id'       => $regFee->id,
            'amount'       => 5000,
            'date'         => now()->toDateString(),
            'status'       => 'completed',
            'phone_number' => '97000000',
        ]);

        $this->accepted = ClassRegistration::create([
            'user_id'        => $this->student->id,
            'grade_id'       => $this->grade->id,
            'status'         => 'accepted',
            'transaction_id' => $regTransaction->id,
        ]);

        Filament::setCurrentPanel(Filament::getPanel('portal'));
        Storage::fake('supabase');
    }

    private function bindSuccessfulKkiapay(): void
    {
        $mock = $this->createMock(KkiapayService::class);
        $mock->method('verify')->willReturn(true);
        app()->instance(KkiapayService::class, $mock);
    }

    private function bindFailedKkiapay(): void
    {
        $mock = $this->createMock(KkiapayService::class);
        $mock->method('verify')->willReturn(false);
        app()->instance(KkiapayService::class, $mock);
    }

    /** @test */
    public function accepted_student_can_view_general_fees_page(): void
    {
        $this->actingAs($this->student);

        Livewire::test(GeneralFees::class)
            ->assertSee('Terminal')
            ->assertSee('Frais de sortie scolaire')
            ->assertSee('15');
    }

    /** @test */
    public function pending_student_sees_not_accepted_state(): void
    {
        $pending = User::factory()->create(['verified' => true]);
        $pending->assignRole('parent_student');

        $regFee = Fee::where('type', 'App\Models\RegistrationFee')->first();

        $tx = Transaction::create([
            'user_id'      => $pending->id,
            'fee_id'       => $regFee->id,
            'amount'       => 5000,
            'date'         => now()->toDateString(),
            'status'       => 'completed',
            'phone_number' => '97000000',
        ]);

        ClassRegistration::create([
            'user_id'        => $pending->id,
            'grade_id'       => $this->grade->id,
            'status'         => 'pending',
            'transaction_id' => $tx->id,
        ]);

        $this->actingAs($pending);

        Livewire::test(GeneralFees::class)
            ->assertSee('Inscription requise');
    }

    /** @test */
    public function loads_fees_only_for_registration_grade_and_year(): void
    {
        // Second fee for same grade + same year — should also appear
        Fee::create([
            'type'          => 'App\Models\GeneralFee',
            'title'         => 'Frais de tenue scolaire',
            'grade_id'      => $this->grade->id,
            'total_amount'  => 10000,
            'academic_year' => $this->registrationAcademicYear,
            'due_before'    => now()->addMonths(2),
        ]);

        // Fee for a different grade — should not appear
        $otherGrade = Grade::factory()->create(['name' => 'Seconde']);
        Fee::create([
            'type'          => 'App\Models\GeneralFee',
            'title'         => 'Frais autre classe',
            'grade_id'      => $otherGrade->id,
            'total_amount'  => 8000,
            'academic_year' => $this->registrationAcademicYear,
            'due_before'    => now()->addMonths(1),
        ]);

        // Fee for correct grade but a different year — should not appear
        $otherYear = (now()->year - 1) . '-' . now()->year;
        Fee::create([
            'type'          => 'App\Models\GeneralFee',
            'title'         => 'Frais autre année',
            'grade_id'      => $this->grade->id,
            'total_amount'  => 12000,
            'academic_year' => $otherYear,
            'due_before'    => now()->subMonths(6),
        ]);

        $this->actingAs($this->student);

        Livewire::test(GeneralFees::class)
            ->assertSee('Frais de sortie scolaire')
            ->assertSee('Frais de tenue scolaire')
            ->assertDontSee('Frais autre classe')
            ->assertDontSee('Frais autre année');
    }

    /** @test */
    public function shows_navigation_badge_with_unpaid_count(): void
    {
        // Add a second fee for the same grade + year — badge should count both
        Fee::create([
            'type'          => 'App\Models\GeneralFee',
            'title'         => 'Frais de tenue scolaire',
            'grade_id'      => $this->grade->id,
            'total_amount'  => 10000,
            'academic_year' => $this->registrationAcademicYear,
            'due_before'    => now()->addMonths(2),
        ]);

        $this->actingAs($this->student);

        $badge = GeneralFees::getNavigationBadge();

        $this->assertEquals('2', $badge);
    }

    /** @test */
    public function badge_is_null_when_all_fees_paid(): void
    {
        Transaction::create([
            'user_id'           => $this->student->id,
            'fee_id'            => $this->generalFee->id,
            'amount'            => 15000,
            'date'              => now()->toDateString(),
            'status'            => 'completed',
            'kkiapay_reference' => 'REF-PAID',
            'phone_number'      => '97000000',
        ]);

        $this->actingAs($this->student);

        $badge = GeneralFees::getNavigationBadge();

        $this->assertNull($badge);
    }

    /** @test */
    public function dashboard_shows_alert_when_unpaid_fees_exist(): void
    {
        $this->actingAs($this->student);

        $this->get('/portal')
            ->assertSee('Voir les frais généraux');
    }

    /** @test */
    public function dashboard_hides_alert_when_all_fees_paid(): void
    {
        Transaction::create([
            'user_id'           => $this->student->id,
            'fee_id'            => $this->generalFee->id,
            'amount'            => 15000,
            'date'              => now()->toDateString(),
            'status'            => 'completed',
            'kkiapay_reference' => 'REF-PAID',
            'phone_number'      => '97000000',
        ]);

        $this->actingAs($this->student);

        $this->get('/portal')
            ->assertDontSee('Voir les frais généraux');
    }

    /** @test */
    public function initiate_payment_requires_phone_number(): void
    {
        $this->actingAs($this->student);

        Livewire::test(GeneralFees::class)
            ->call('openPayModal', $this->generalFee->id)
            ->set('phoneNumber', '')
            ->call('initiatePayment')
            ->assertHasErrors(['phoneNumber']);
    }

    /** @test */
    public function initiate_payment_fails_when_fee_already_paid(): void
    {
        Transaction::create([
            'user_id'           => $this->student->id,
            'fee_id'            => $this->generalFee->id,
            'amount'            => 15000,
            'date'              => now()->toDateString(),
            'status'            => 'completed',
            'kkiapay_reference' => 'REF-PAID',
            'phone_number'      => '97000000',
        ]);

        $this->actingAs($this->student);

        Livewire::test(GeneralFees::class)
            ->set('selectedFeeId', $this->generalFee->id)
            ->set('showModal', true)
            ->set('phoneNumber', '97000001')
            ->call('initiatePayment')
            ->assertNotDispatched('open-kkiapay-widget');
    }

    /** @test */
    public function initiate_payment_dispatches_kkiapay_event_with_correct_amount(): void
    {
        $this->actingAs($this->student);

        Livewire::test(GeneralFees::class)
            ->call('openPayModal', $this->generalFee->id)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->assertDispatched('open-kkiapay-widget', amount: 15000, phone: '97000000');
    }

    /** @test */
    public function initiate_payment_dispatches_kkiapay_with_fine_for_overdue_fee(): void
    {
        // Update fee to be overdue (due 2 weeks ago)
        $this->generalFee->update(['due_before' => now()->subWeeks(2)]);

        $this->actingAs($this->student);

        // 2 weeks overdue × 1000 F CFA / week = 2000 fine
        Livewire::test(GeneralFees::class)
            ->call('openPayModal', $this->generalFee->id)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->assertDispatched('open-kkiapay-widget', amount: 17000, phone: '97000000');
    }

    /** @test */
    public function handle_success_creates_transaction(): void
    {
        Mail::fake();
        $this->bindSuccessfulKkiapay();

        $this->actingAs($this->student);

        Livewire::test(GeneralFees::class)
            ->call('openPayModal', $this->generalFee->id)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'REF-GF-001');

        $this->assertDatabaseHas('transactions', [
            'user_id'           => $this->student->id,
            'fee_id'            => $this->generalFee->id,
            'amount'            => 15000,
            'kkiapay_reference' => 'REF-GF-001',
            'status'            => 'completed',
        ]);
    }

    /** @test */
    public function handle_success_marks_fee_as_paid(): void
    {
        Mail::fake();
        $this->bindSuccessfulKkiapay();

        $this->actingAs($this->student);

        $component = Livewire::test(GeneralFees::class)
            ->call('openPayModal', $this->generalFee->id)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'REF-GF-002');

        // After payment, badge should return null (0 unpaid)
        $badge = GeneralFees::getNavigationBadge();
        $this->assertNull($badge);
    }

    /** @test */
    public function handle_success_applies_fine_to_overdue_fee(): void
    {
        Mail::fake();
        $this->bindSuccessfulKkiapay();

        // Fee overdue by exactly 2 weeks
        $this->generalFee->update(['due_before' => now()->subWeeks(2)->startOfDay()]);

        $this->actingAs($this->student);

        Livewire::test(GeneralFees::class)
            ->call('openPayModal', $this->generalFee->id)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'REF-GF-003');

        $this->assertDatabaseHas('transactions', [
            'user_id'           => $this->student->id,
            'fee_id'            => $this->generalFee->id,
            'amount'            => 17000, // 15000 + 2 weeks × 1000
            'kkiapay_reference' => 'REF-GF-003',
        ]);
    }

    /** @test */
    public function handle_success_sends_receipt_email(): void
    {
        Mail::fake();
        $this->bindSuccessfulKkiapay();

        $this->actingAs($this->student);

        Livewire::test(GeneralFees::class)
            ->call('openPayModal', $this->generalFee->id)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'REF-GF-004');

        Mail::assertSent(GeneralFeeReceiptMail::class, function ($mail) {
            return $mail->hasTo($this->student->email);
        });
    }

    /** @test */
    public function handle_success_stores_receipt_on_supabase(): void
    {
        Mail::fake();
        $this->bindSuccessfulKkiapay();

        $this->actingAs($this->student);

        Livewire::test(GeneralFees::class)
            ->call('openPayModal', $this->generalFee->id)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'REF-GF-005');

        Storage::disk('supabase')->assertExists(
            'receipts/' . $this->student->id . '/general-fee-REF-GF-005.pdf'
        );
    }

    /** @test */
    public function handle_success_aborts_when_sdk_returns_failure(): void
    {
        Mail::fake();
        $this->bindFailedKkiapay();

        $this->actingAs($this->student);

        Livewire::test(GeneralFees::class)
            ->call('openPayModal', $this->generalFee->id)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'INVALID-REF');

        $this->assertDatabaseMissing('transactions', [
            'fee_id'            => $this->generalFee->id,
            'kkiapay_reference' => 'INVALID-REF',
        ]);

        Mail::assertNotSent(GeneralFeeReceiptMail::class);
    }

    /** @test */
    public function cannot_pay_fee_belonging_to_different_grade(): void
    {
        $otherGrade = Grade::factory()->create(['name' => 'Seconde']);

        $otherFee = Fee::create([
            'type'          => 'App\Models\GeneralFee',
            'title'         => 'Frais autre classe',
            'grade_id'      => $otherGrade->id,
            'total_amount'  => 8000,
            'academic_year' => $this->registrationAcademicYear,
            'due_before'    => now()->addMonths(1),
        ]);

        $this->actingAs($this->student);

        // The fee for the other grade should not appear in the student's view
        Livewire::test(GeneralFees::class)
            ->assertDontSee('Frais autre classe');
    }
}