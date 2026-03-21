<?php

namespace Tests\Feature\Tuition;

use App\Filament\Portal\Pages\TuitionPayment;
use App\Mail\TuitionReceiptMail;
use App\Models\ClassRegistration;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Installment;
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

class TuitionPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected Grade $grade;
    protected Fee $tuitionFee;
    protected Installment $inst1;
    protected Installment $inst2;
    protected Installment $inst3;
    protected ClassRegistration $accepted;
    protected string $nextAcademicYear;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'accountant']);
        Role::create(['name' => 'secretary']);
        Role::create(['name' => 'employee']);
        Role::create(['name' => 'parent_student']);

        $this->nextAcademicYear = now()->year . '-' . (now()->year + 1);

        $this->student = User::factory()->create(['verified' => true]);
        $this->student->assignRole('parent_student');

        $this->grade = Grade::factory()->create(['name' => 'Terminal']);

        // Registration fee so the grade shows up
        Fee::create([
            'type'          => 'App\Models\RegistrationFee',
            'title'         => 'Frais inscription Terminal',
            'grade_id'      => $this->grade->id,
            'total_amount'  => 5000,
            'academic_year' => $this->nextAcademicYear,
        ]);

        // Tuition fee with three installments and a late fine
        $this->tuitionFee = Fee::create([
            'type'                  => 'App\Models\TuitionFee',
            'title'                 => 'Scolarité Terminal',
            'grade_id'              => $this->grade->id,
            'total_amount'          => 90000,
            'academic_year'         => $this->nextAcademicYear,
            'number_of_installments' => 3,
            'late_fine_per_week'    => 2000,
        ]);

        $this->inst1 = Installment::create([
            'tuition_fee_id' => $this->tuitionFee->id,
            'number'         => 1,
            'amount'         => 30000,
            'due_date'       => now()->addMonths(1),
        ]);

        $this->inst2 = Installment::create([
            'tuition_fee_id' => $this->tuitionFee->id,
            'number'         => 2,
            'amount'         => 30000,
            'due_date'       => now()->addMonths(2),
        ]);

        $this->inst3 = Installment::create([
            'tuition_fee_id' => $this->tuitionFee->id,
            'number'         => 3,
            'amount'         => 30000,
            'due_date'       => now()->addMonths(3),
        ]);

        // Accept the student's registration
        $regTransaction = Transaction::create([
            'user_id'      => $this->student->id,
            'fee_id'       => Fee::where('type', 'App\Models\RegistrationFee')->first()->id,
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
    public function accepted_student_can_view_tuition_page(): void
    {
        $this->actingAs($this->student);

        Livewire::test(TuitionPayment::class)
            ->assertSee('Terminal')
            ->assertSee('Versement N°1')
            ->assertSee('Versement N°2')
            ->assertSee('Versement N°3');
    }

    /** @test */
    public function pending_student_is_shown_not_accepted_state(): void
    {
        $pending = User::factory()->create(['verified' => true]);
        $pending->assignRole('parent_student');

        $tx = Transaction::create([
            'user_id'      => $pending->id,
            'fee_id'       => Fee::where('type', 'App\Models\RegistrationFee')->first()->id,
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

        Livewire::test(TuitionPayment::class)
            ->assertSee('Inscription requise');
    }

    /** @test */
    public function loads_correct_tuition_fee_for_grade_and_academic_year(): void
    {
        // Create a tuition fee for a different year — should not be loaded
        Fee::create([
            'type'                  => 'App\Models\TuitionFee',
            'title'                 => 'Scolarité Terminal ancienne',
            'grade_id'              => $this->grade->id,
            'total_amount'          => 80000,
            'academic_year'         => '2024-2025',
            'number_of_installments' => 2,
        ]);

        $this->actingAs($this->student);

        $component = Livewire::test(TuitionPayment::class);

        $this->assertEquals($this->tuitionFee->id, $component->get('tuitionFeeId'));
    }

    /** @test */
    public function initiate_payment_requires_phone_number(): void
    {
        $this->actingAs($this->student);

        Livewire::test(TuitionPayment::class)
            ->set('selectedIds', [$this->inst1->id])
            ->call('openModal', false)
            ->set('phoneNumber', '')
            ->call('initiatePayment')
            ->assertHasErrors(['phoneNumber'])
            ->assertNotDispatched('open-kkiapay-widget');
    }

    /** @test */
    public function initiate_payment_fails_when_no_tuition_fee(): void
    {
        $student2 = User::factory()->create(['verified' => true]);
        $student2->assignRole('parent_student');

        $grade2 = Grade::factory()->create(['name' => 'CE1']);

        $regFee = Fee::where('type', 'App\Models\RegistrationFee')->first();

        $tx = Transaction::create([
            'user_id'      => $student2->id,
            'fee_id'       => $regFee->id,
            'amount'       => 5000,
            'date'         => now()->toDateString(),
            'status'       => 'completed',
            'phone_number' => '97000000',
        ]);

        ClassRegistration::create([
            'user_id'        => $student2->id,
            'grade_id'       => $grade2->id,
            'status'         => 'accepted',
            'transaction_id' => $tx->id,
        ]);

        $this->actingAs($student2);

        Livewire::test(TuitionPayment::class)
            ->call('openModal', false)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->assertNotDispatched('open-kkiapay-widget');

        // No tuition transactions should exist for student2 (only the registration tx above)
        $this->assertDatabaseMissing('transactions', [
            'user_id' => $student2->id,
            'fee_id'  => $this->tuitionFee->id,
        ]);
    }

    /** @test */
    public function initiate_payment_fails_when_all_installments_paid(): void
    {
        $this->actingAs($this->student);

        // Pay all installments
        foreach ([$this->inst1, $this->inst2, $this->inst3] as $inst) {
            Transaction::create([
                'user_id'        => $this->student->id,
                'fee_id'         => $this->tuitionFee->id,
                'installment_id' => $inst->id,
                'amount'         => (int) $inst->amount,
                'date'           => now()->toDateString(),
                'status'         => 'completed',
                'phone_number'   => '97000000',
            ]);
        }

        Livewire::test(TuitionPayment::class)
            ->call('openModal', true)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->assertNotDispatched('open-kkiapay-widget');
    }

    /** @test */
    public function initiate_payment_dispatches_kkiapay_event_with_correct_amount(): void
    {
        $this->actingAs($this->student);

        Livewire::test(TuitionPayment::class)
            ->set('selectedIds', [$this->inst1->id])
            ->call('openModal', false)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->assertDispatched('open-kkiapay-widget', amount: 30000, phone: '97000000');
    }

    /** @test */
    public function handle_success_creates_one_transaction_per_installment(): void
    {
        $this->actingAs($this->student);
        $this->bindSuccessfulKkiapay();
        Mail::fake();

        Livewire::test(TuitionPayment::class)
            ->set('selectedIds', [$this->inst1->id, $this->inst2->id])
            ->call('openModal', false)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'tuition_ref_multi');

        $tuitionTransactions = Transaction::where('user_id', $this->student->id)
            ->where('fee_id', $this->tuitionFee->id)
            ->where('status', 'completed')
            ->get();

        $this->assertCount(2, $tuitionTransactions);
        $this->assertTrue($tuitionTransactions->every(fn ($t) => $t->kkiapay_reference === 'tuition_ref_multi'));
        $this->assertEqualsCanonicalizing(
            [$this->inst1->id, $this->inst2->id],
            $tuitionTransactions->pluck('installment_id')->toArray()
        );
    }

    /** @test */
    public function handle_success_stores_kkiapay_reference(): void
    {
        $this->actingAs($this->student);
        $this->bindSuccessfulKkiapay();
        Mail::fake();

        Livewire::test(TuitionPayment::class)
            ->set('selectedIds', [$this->inst1->id])
            ->call('openModal', false)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'tuition_ref_unique');

        $this->assertDatabaseHas('transactions', [
            'user_id'           => $this->student->id,
            'installment_id'    => $this->inst1->id,
            'kkiapay_reference' => 'tuition_ref_unique',
        ]);
    }

    /** @test */
    public function handle_success_marks_installments_paid(): void
    {
        $this->actingAs($this->student);
        $this->bindSuccessfulKkiapay();
        Mail::fake();

        $component = Livewire::test(TuitionPayment::class)
            ->set('selectedIds', [$this->inst1->id])
            ->call('openModal', false)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'tuition_ref_paid');

        // The paid installment should appear in paidInstallmentIds on next render
        $this->assertDatabaseHas('transactions', [
            'installment_id' => $this->inst1->id,
            'status'         => 'completed',
        ]);
    }

    /** @test */
    public function handle_success_applies_fine_to_overdue_installments(): void
    {
        $this->actingAs($this->student);
        $this->bindSuccessfulKkiapay();
        Mail::fake();

        // Create a 2-week overdue installment
        $overdueInst = Installment::create([
            'tuition_fee_id' => $this->tuitionFee->id,
            'number'         => 4,
            'amount'         => 20000,
            'due_date'       => now()->subDays(14), // 2 weeks overdue
        ]);

        // Expected fine: 2 weeks × 2000 = 4000
        $expectedAmount = 20000 + 4000;

        Livewire::test(TuitionPayment::class)
            ->set('selectedIds', [$overdueInst->id])
            ->call('openModal', false)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'tuition_ref_fine');

        $this->assertDatabaseHas('transactions', [
            'installment_id' => $overdueInst->id,
            'amount'         => $expectedAmount,
        ]);
    }

    /** @test */
    public function handle_success_sends_receipt_email(): void
    {
        $this->actingAs($this->student);
        $this->bindSuccessfulKkiapay();
        Mail::fake();

        Livewire::test(TuitionPayment::class)
            ->set('selectedIds', [$this->inst1->id])
            ->call('openModal', false)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'tuition_ref_email');

        Mail::assertSent(TuitionReceiptMail::class, fn ($mail) => $mail->hasTo($this->student->email));
    }

    /** @test */
    public function handle_success_stores_receipt_on_supabase(): void
    {
        $this->actingAs($this->student);
        $this->bindSuccessfulKkiapay();
        Mail::fake();

        Livewire::test(TuitionPayment::class)
            ->set('selectedIds', [$this->inst1->id])
            ->call('openModal', false)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'tuition_ref_pdf');

        Storage::disk('supabase')->assertExists(
            'receipts/' . $this->student->id . '/tuition-tuition_ref_pdf.pdf'
        );
    }

    /** @test */
    public function handle_success_aborts_when_sdk_returns_failure(): void
    {
        $this->actingAs($this->student);
        $this->bindFailedKkiapay();
        Mail::fake();

        Livewire::test(TuitionPayment::class)
            ->set('selectedIds', [$this->inst1->id])
            ->call('openModal', false)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'bad_ref');

        $this->assertDatabaseMissing('transactions', [
            'fee_id' => $this->tuitionFee->id,
        ]);
        Mail::assertNothingSent();
    }

    /** @test */
    public function full_payment_creates_transaction_for_every_installment(): void
    {
        $this->actingAs($this->student);
        $this->bindSuccessfulKkiapay();
        Mail::fake();

        Livewire::test(TuitionPayment::class)
            ->call('openModal', true) // payAll = true
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'tuition_ref_all');

        $count = Transaction::where('user_id', $this->student->id)
            ->where('fee_id', $this->tuitionFee->id)
            ->where('status', 'completed')
            ->count();

        $this->assertEquals(3, $count);
    }

    /** @test */
    public function cannot_pay_already_paid_installment(): void
    {
        $this->actingAs($this->student);
        $this->bindSuccessfulKkiapay();
        Mail::fake();

        // Pre-pay inst1
        Transaction::create([
            'user_id'        => $this->student->id,
            'fee_id'         => $this->tuitionFee->id,
            'installment_id' => $this->inst1->id,
            'amount'         => 30000,
            'date'           => now()->toDateString(),
            'status'         => 'completed',
            'phone_number'   => '97000000',
        ]);

        // Attempt to pay all — inst1 is already paid so only inst2 + inst3 should be processed
        Livewire::test(TuitionPayment::class)
            ->call('openModal', true)
            ->set('phoneNumber', '97000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'tuition_ref_excl');

        // inst1 should still have exactly 1 transaction
        $this->assertEquals(
            1,
            Transaction::where('installment_id', $this->inst1->id)->count()
        );

        // inst2 and inst3 should each have 1 new transaction
        $this->assertDatabaseHas('transactions', ['installment_id' => $this->inst2->id]);
        $this->assertDatabaseHas('transactions', ['installment_id' => $this->inst3->id]);
    }
}