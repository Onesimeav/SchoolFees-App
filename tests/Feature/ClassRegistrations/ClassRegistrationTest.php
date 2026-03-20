<?php

namespace Tests\Feature\ClassRegistrations;

use App\Filament\Portal\Pages\ClassEnrollment;
use App\Filament\Portal\Pages\Auth\VerifyEmail;
use App\Filament\Staff\Resources\ClassRegistrations\ClassRegistrationResource;
use App\Filament\Staff\Resources\ClassRegistrations\Pages\ListClassRegistrations as StaffListClassRegistrations;
use App\Filament\Resources\ClassRegistrations\Pages\ListClassRegistrations as AdminListClassRegistrations;
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

class ClassRegistrationTest extends TestCase
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

        Storage::fake('supabase');
        Mail::fake();

        // Default: KKiaPay verification always succeeds
        $mock = $this->createMock(KkiapayService::class);
        $mock->method('verify')->willReturn(true);
        app()->instance(KkiapayService::class, $mock);
    }

    /** @test */
    public function student_can_register_for_a_grade(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('portal'));
        $this->actingAs($this->student);

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'kkia_test_ref');

        $this->assertDatabaseHas('class_registrations', [
            'user_id'  => $this->student->id,
            'grade_id' => $this->grade->id,
            'status'   => 'pending',
        ]);
    }

    /** @test */
    public function student_registration_creates_a_transaction(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('portal'));
        $this->actingAs($this->student);

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'kkia_test_ref');

        $this->assertDatabaseHas('transactions', [
            'user_id'           => $this->student->id,
            'fee_id'            => $this->fee->id,
            'amount'            => $this->fee->total_amount,
            'status'            => 'completed',
            'phone_number'      => '0600000000',
            'kkiapay_reference' => 'kkia_test_ref',
        ]);

        // Registration must be linked to the transaction
        $registration = ClassRegistration::where('user_id', $this->student->id)->first();
        $this->assertNotNull($registration->transaction_id);
    }

    /** @test */
    public function student_cannot_register_without_phone_number(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('portal'));
        $this->actingAs($this->student);

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '')
            ->call('initiatePayment')
            ->assertHasErrors(['phoneNumber']);

        $this->assertDatabaseCount('class_registrations', 0);
        $this->assertDatabaseCount('transactions', 0);
    }

    /** @test */
    public function student_cannot_register_when_no_fee_exists_for_grade(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('portal'));
        $this->actingAs($this->student);

        $emptyGrade = Grade::factory()->create(['name' => 'CE1']);

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $emptyGrade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment');

        $this->assertDatabaseCount('class_registrations', 0);
        $this->assertDatabaseCount('transactions', 0);
    }

    /** @test */
    public function unverified_student_is_redirected_on_mount(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('portal'));

        $unverified = User::factory()->create(['verified' => false]);
        $unverified->assignRole('parent_student');
        $this->actingAs($unverified);

        Livewire::test(ClassEnrollment::class)
            ->assertRedirect(VerifyEmail::getUrl());
    }

    /** @test */
    public function student_cannot_register_twice_when_pending(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('portal'));
        $this->actingAs($this->student);

        ClassRegistration::create([
            'user_id'  => $this->student->id,
            'grade_id' => $this->grade->id,
            'status'   => 'pending',
        ]);

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'kkia_test_ref');

        $this->assertDatabaseCount('class_registrations', 1);
    }

    /** @test */
    public function student_cannot_register_when_already_accepted(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('portal'));
        $this->actingAs($this->student);

        ClassRegistration::create([
            'user_id'  => $this->student->id,
            'grade_id' => $this->grade->id,
            'status'   => 'accepted',
        ]);

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $this->grade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'kkia_test_ref');

        $this->assertDatabaseCount('class_registrations', 1);
    }

    /** @test */
    public function student_can_register_again_after_refusal(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('portal'));
        $this->actingAs($this->student);

        ClassRegistration::create([
            'user_id'  => $this->student->id,
            'grade_id' => $this->grade->id,
            'status'   => 'refused',
        ]);

        $anotherGrade = Grade::factory()->create(['name' => 'CE1']);
        Fee::create([
            'type'          => 'App\Models\RegistrationFee',
            'title'         => 'Frais inscription CE1',
            'grade_id'      => $anotherGrade->id,
            'total_amount'  => 15000,
            'academic_year' => $this->nextAcademicYear,
        ]);

        Livewire::test(ClassEnrollment::class)
            ->call('openModal', $anotherGrade->id)
            ->set('phoneNumber', '0600000000')
            ->call('initiatePayment')
            ->call('handleKkiapaySuccess', 'kkia_test_ref');

        $this->assertDatabaseCount('class_registrations', 2);
        $this->assertDatabaseHas('class_registrations', [
            'user_id'  => $this->student->id,
            'grade_id' => $anotherGrade->id,
            'status'   => 'pending',
        ]);
    }

    /** @test */
    public function secretary_can_accept_a_registration(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('staff'));

        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');
        $this->actingAs($secretary);

        $registration = ClassRegistration::create([
            'user_id'  => $this->student->id,
            'grade_id' => $this->grade->id,
            'status'   => 'pending',
        ]);

        Livewire::test(StaffListClassRegistrations::class)
            ->callTableAction('accept', $registration);

        $this->assertEquals('accepted', $registration->fresh()->status);
    }

    /** @test */
    public function secretary_can_refuse_a_registration_with_notes(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('staff'));

        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');
        $this->actingAs($secretary);

        $registration = ClassRegistration::create([
            'user_id'  => $this->student->id,
            'grade_id' => $this->grade->id,
            'status'   => 'pending',
        ]);

        Livewire::test(StaffListClassRegistrations::class)
            ->callTableAction('refuse', $registration, data: ['notes' => 'Dossier incomplet']);

        $this->assertEquals('refused', $registration->fresh()->status);
        $this->assertEquals('Dossier incomplet', $registration->fresh()->notes);
    }

    /** @test */
    public function accountant_cannot_accept_or_refuse(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('staff'));

        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');
        $this->actingAs($accountant);

        $registration = ClassRegistration::create([
            'user_id'  => $this->student->id,
            'grade_id' => $this->grade->id,
            'status'   => 'pending',
        ]);

        Livewire::test(StaffListClassRegistrations::class)
            ->assertTableActionHidden('accept', $registration)
            ->assertTableActionHidden('refuse', $registration);
    }

    /** @test */
    public function admin_can_accept_a_registration(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $registration = ClassRegistration::create([
            'user_id'  => $this->student->id,
            'grade_id' => $this->grade->id,
            'status'   => 'pending',
        ]);

        Livewire::test(AdminListClassRegistrations::class)
            ->callTableAction('accept', $registration);

        $this->assertEquals('accepted', $registration->fresh()->status);
    }

    /** @test */
    public function admin_can_delete_a_registration(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $registration = ClassRegistration::create([
            'user_id'  => $this->student->id,
            'grade_id' => $this->grade->id,
            'status'   => 'pending',
        ]);

        // Policy grants delete to admin
        $this->assertTrue($admin->can('delete', $registration));

        // Deletion actually removes the record
        $this->actingAs($admin);
        $registration->delete();

        $this->assertDatabaseMissing('class_registrations', ['id' => $registration->id]);
    }

    /** @test */
    public function secretary_cannot_delete_a_registration(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $registration = ClassRegistration::create([
            'user_id'  => $this->student->id,
            'grade_id' => $this->grade->id,
            'status'   => 'pending',
        ]);

        // Policy denies delete for secretary
        $this->assertFalse($secretary->can('delete', $registration));
    }

    /** @test */
    public function registration_is_immutable_via_edit(): void
    {
        $this->assertFalse(ClassRegistrationResource::canEdit(new ClassRegistration()));
    }
}