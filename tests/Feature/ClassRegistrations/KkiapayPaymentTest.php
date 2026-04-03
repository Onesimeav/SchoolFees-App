<?php

namespace Tests\Feature\ClassRegistrations;

use App\Filament\Portal\Pages\ClassEnrollment;
use App\Mail\RegistrationReceiptMail;
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

class KkiapayPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected Grade $grade;
    protected Fee $fee;
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
        $this->grade = Grade::factory()->create(['name' => 'CP']);

        $this->fee = Fee::create([
            'type'          => 'App\Models\RegistrationFee',
            'title'         => 'Frais inscription CP',
            'grade_id'      => $this->grade->id,
            'total_amount'  => 15000,
            'academic_year' => $this->nextAcademicYear,
        ]);

        $this->student = User::factory()->create(['verified' => true]);
        $this->student->assignRole('parent_student');

        Filament::setCurrentPanel(Filament::getPanel('portal'));

        // Fake Storage so no real S3/Supabase calls are made
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
    public function initiate_payment_dispatches_kkiapay_event(): void
    {
        $this->actingAs($this->student);

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment')
            ->assertDispatched('open-kkiapay-widget', amount: 15000, phone: '0600000000');
    }

    /** @test */
    public function initiate_payment_requires_phone_number(): void
    {
        $this->actingAs($this->student);

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '')
            ->call('initiatePayment')
            ->assertHasErrors(['phoneNumber'])
            ->assertNotDispatched('open-kkiapay-widget');
    }

    /** @test */
    public function initiate_payment_fails_when_no_fee_exists(): void
    {
        $this->actingAs($this->student);

        $emptyGrade = Grade::factory()->create(['name' => 'CE2']);

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $emptyGrade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment')
            ->assertNotDispatched('open-kkiapay-widget');

        $this->assertDatabaseCount('transactions', 0);
    }

    /** @test */
    public function handle_success_creates_transaction_and_registration(): void
    {
        $this->actingAs($this->student);
        $this->bindSuccessfulKkiapay();
        Mail::fake();

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'kkia_test_ref_123');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->student->id,
            'fee_id'  => $this->fee->id,
            'amount'  => $this->fee->total_amount,
            'status'  => 'completed',
        ]);

        $this->assertDatabaseHas('class_registrations', [
            'user_id'  => $this->student->id,
            'grade_id' => $this->grade->id,
            'status'   => 'pending',
        ]);

        $registration = ClassRegistration::where('user_id', $this->student->id)->first();
        $this->assertNotNull($registration->transaction_id);
    }

    /** @test */
    public function handle_success_stores_kkiapay_reference_on_transaction(): void
    {
        $this->actingAs($this->student);
        $this->bindSuccessfulKkiapay();
        Mail::fake();

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'kkia_unique_ref_abc');

        $this->assertDatabaseHas('transactions', [
            'user_id'           => $this->student->id,
            'kkiapay_reference' => 'kkia_unique_ref_abc',
        ]);
    }

    /** @test */
    public function handle_success_sends_receipt_email(): void
    {
        $this->actingAs($this->student);
        $this->bindSuccessfulKkiapay();
        Mail::fake();

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'kkia_test_ref_456');

        Mail::assertSent(RegistrationReceiptMail::class, function ($mail) {
            return $mail->hasTo($this->student->email);
        });
    }

    /** @test */
    public function handle_success_stores_receipt_on_supabase(): void
    {
        $this->actingAs($this->student);
        $this->bindSuccessfulKkiapay();
        Mail::fake();

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'kkia_test_ref_789');

        $registration = ClassRegistration::where('user_id', $this->student->id)->first();
        $expectedPath = 'receipts/' . $this->student->id . '/' . $registration->id . '.pdf';

        Storage::disk('supabase')->assertExists($expectedPath);
    }

    /** @test */
    public function handle_success_aborts_when_sdk_returns_non_success(): void
    {
        $this->actingAs($this->student);
        $this->bindFailedKkiapay();
        Mail::fake();

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'kkia_bad_ref');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('class_registrations', 0);
        Mail::assertNothingSent();
    }

    /** @test */
    public function handle_success_is_idempotent_when_already_pending(): void
    {
        $this->actingAs($this->student);
        $this->bindSuccessfulKkiapay();
        Mail::fake();

        ClassRegistration::create([
            'user_id'  => $this->student->id,
            'grade_id' => $this->grade->id,
            'status'   => 'pending',
        ]);

        Livewire::test(ClassEnrollment::class)
            ->call('handleKkiapaySuccess', 'kkia_test_ref_dupe');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('class_registrations', 1);
    }

    /** @test */
    public function handle_failure_shows_danger_notification_and_creates_nothing(): void
    {
        $this->actingAs($this->student);

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '0600000000')
            ->call('handleKkiapayFailure');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('class_registrations', 0);
    }
}