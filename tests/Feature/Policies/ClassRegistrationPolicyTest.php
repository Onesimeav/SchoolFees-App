<?php

namespace Tests\Feature\Policies;

use App\Models\ClassRegistration;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClassRegistrationPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'accountant']);
        Role::create(['name' => 'secretary']);
        Role::create(['name' => 'employee']);
        Role::create(['name' => 'parent_student']);
    }

    private function makeRegistration(): ClassRegistration
    {
        return ClassRegistration::factory()->create();
    }

    // ── viewAny ─────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_view_any_registrations(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->assertTrue(Gate::forUser($admin)->allows('viewAny', ClassRegistration::class));
    }

    /** @test */
    public function secretary_can_view_any_registrations(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $this->assertTrue(Gate::forUser($secretary)->allows('viewAny', ClassRegistration::class));
    }

    /** @test */
    public function accountant_can_view_any_registrations(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $this->assertTrue(Gate::forUser($accountant)->allows('viewAny', ClassRegistration::class));
    }

    /** @test */
    public function employee_can_view_any_registrations(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $this->assertTrue(Gate::forUser($employee)->allows('viewAny', ClassRegistration::class));
    }

    /** @test */
    public function parent_student_can_view_any_registrations(): void
    {
        $student = User::factory()->create(['verified' => true]);
        $student->assignRole('parent_student');

        $this->assertTrue(Gate::forUser($student)->allows('viewAny', ClassRegistration::class));
    }

    // ── view ─────────────────────────────────────────────────────────────────

    /** @test */
    public function parent_student_can_view_their_own_registration(): void
    {
        $student = User::factory()->create(['verified' => true]);
        $student->assignRole('parent_student');

        $registration = ClassRegistration::factory()->create(['user_id' => $student->id]);

        $this->assertTrue(Gate::forUser($student)->allows('view', $registration));
    }

    /** @test */
    public function parent_student_cannot_view_another_students_registration(): void
    {
        $student = User::factory()->create(['verified' => true]);
        $student->assignRole('parent_student');

        $otherRegistration = $this->makeRegistration();

        $this->assertFalse(Gate::forUser($student)->allows('view', $otherRegistration));
    }

    /** @test */
    public function staff_can_view_any_individual_registration(): void
    {
        $registration = $this->makeRegistration();

        foreach (['admin', 'secretary', 'accountant', 'employee'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $this->assertTrue(
                Gate::forUser($user)->allows('view', $registration),
                "Failed: {$role} should be able to view a registration"
            );
        }
    }

    // ── create ───────────────────────────────────────────────────────────────

    /** @test */
    public function verified_parent_student_can_create_a_registration(): void
    {
        $student = User::factory()->create(['verified' => true]);
        $student->assignRole('parent_student');

        $this->assertTrue(Gate::forUser($student)->allows('create', ClassRegistration::class));
    }

    /** @test */
    public function unverified_parent_student_cannot_create_a_registration(): void
    {
        $student = User::factory()->create(['verified' => false]);
        $student->assignRole('parent_student');

        $this->assertFalse(Gate::forUser($student)->allows('create', ClassRegistration::class));
    }

    /** @test */
    public function staff_cannot_create_registrations(): void
    {
        foreach (['admin', 'secretary', 'accountant', 'employee'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $this->assertFalse(
                Gate::forUser($user)->allows('create', ClassRegistration::class),
                "Failed: {$role} should not be able to create a registration"
            );
        }
    }

    // ── delete ───────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_delete_a_registration(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $registration = $this->makeRegistration();

        $this->assertTrue(Gate::forUser($admin)->allows('delete', $registration));
    }

    /** @test */
    public function secretary_cannot_delete_a_registration(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $registration = $this->makeRegistration();

        $this->assertFalse(Gate::forUser($secretary)->allows('delete', $registration));
    }

    /** @test */
    public function accountant_cannot_delete_a_registration(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $registration = $this->makeRegistration();

        $this->assertFalse(Gate::forUser($accountant)->allows('delete', $registration));
    }

    /** @test */
    public function parent_student_cannot_delete_a_registration(): void
    {
        $student = User::factory()->create(['verified' => true]);
        $student->assignRole('parent_student');

        $registration = $this->makeRegistration();

        $this->assertFalse(Gate::forUser($student)->allows('delete', $registration));
    }

    // ── update (immutable) ───────────────────────────────────────────────────

    /** @test */
    public function no_role_can_update_a_registration(): void
    {
        $registration = $this->makeRegistration();

        foreach (['admin', 'secretary', 'accountant', 'employee', 'parent_student'] as $role) {
            $user = User::factory()->create(['verified' => true]);
            $user->assignRole($role);

            $this->assertFalse(
                Gate::forUser($user)->allows('update', $registration),
                "Failed: {$role} should not be able to update a registration"
            );
        }
    }

    // ── updateStatus ─────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_update_registration_status(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $registration = $this->makeRegistration();

        $this->assertTrue(Gate::forUser($admin)->allows('updateStatus', $registration));
    }

    /** @test */
    public function secretary_can_update_registration_status(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $registration = $this->makeRegistration();

        $this->assertTrue(Gate::forUser($secretary)->allows('updateStatus', $registration));
    }

    /** @test */
    public function accountant_cannot_update_registration_status(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $registration = $this->makeRegistration();

        $this->assertFalse(Gate::forUser($accountant)->allows('updateStatus', $registration));
    }

    /** @test */
    public function parent_student_cannot_update_registration_status(): void
    {
        $student = User::factory()->create(['verified' => true]);
        $student->assignRole('parent_student');

        $registration = $this->makeRegistration();

        $this->assertFalse(Gate::forUser($student)->allows('updateStatus', $registration));
    }
}