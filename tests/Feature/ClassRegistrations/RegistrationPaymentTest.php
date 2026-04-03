<?php

namespace Tests\Feature\ClassRegistrations;

use App\Filament\Portal\Pages\ClassEnrollment;
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

class RegistrationPaymentTest extends TestCase
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

        $this->student = User::factory()->create([
            'verified'     => true,
            'phone_number' => '0600000000',
        ]);
        $this->student->assignRole('parent_student');

        Filament::setCurrentPanel(Filament::getPanel('portal'));

        Storage::fake('supabase');
        Mail::fake();

        // Bind a successful KKiaPay mock for all tests in this suite
        $mock = $this->createMock(KkiapayService::class);
        $mock->method('verify')->willReturn(true);
        app()->instance(KkiapayService::class, $mock);
    }

    private function completeRegistration(string $phone = '0600000000'): void
    {
        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', $phone)
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'kkia_test_ref_001');
    }

    /** @test */
    public function registration_creates_transaction_on_confirmation(): void
    {
        $this->actingAs($this->student);

        $this->completeRegistration();

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->student->id,
            'fee_id'  => $this->fee->id,
            'amount'  => $this->fee->total_amount,
            'status'  => 'completed',
        ]);
    }

    /** @test */
    public function registration_links_transaction_to_class_registration(): void
    {
        $this->actingAs($this->student);

        $this->completeRegistration();

        $registration = ClassRegistration::where('user_id', $this->student->id)->first();
        $transaction  = Transaction::where('user_id', $this->student->id)->first();

        $this->assertNotNull($registration);
        $this->assertNotNull($transaction);
        $this->assertEquals($transaction->id, $registration->transaction_id);
    }

    /** @test */
    public function confirmation_saves_phone_number_to_transaction(): void
    {
        $this->actingAs($this->student);

        $this->completeRegistration(phone: '97123456');

        $this->assertDatabaseHas('transactions', [
            'user_id'      => $this->student->id,
            'phone_number' => '97123456',
        ]);
    }

    /** @test */
    public function confirmation_requires_phone_number(): void
    {
        $this->actingAs($this->student);

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '')
            ->call('initiatePayment')
            ->assertHasErrors(['phoneNumber']);

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('class_registrations', 0);
    }

    /** @test */
    public function confirmation_fails_when_no_fee_for_grade_and_next_academic_year(): void
    {
        $this->actingAs($this->student);

        // Grade exists but its fee is for a different year
        $oldFeeGrade = Grade::factory()->create(['name' => 'CE2']);
        Fee::create([
            'type'          => 'App\Models\RegistrationFee',
            'title'         => 'Old fee',
            'grade_id'      => $oldFeeGrade->id,
            'total_amount'  => 10000,
            'academic_year' => '2020-2021',
        ]);

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $oldFeeGrade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('class_registrations', 0);
    }

    /** @test */
    public function one_transaction_is_created_per_registration(): void
    {
        $this->actingAs($this->student);

        $this->completeRegistration();

        // Second attempt is blocked by duplicate pending registration guard
        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'kkia_test_ref_002');

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseCount('class_registrations', 1);
    }
}