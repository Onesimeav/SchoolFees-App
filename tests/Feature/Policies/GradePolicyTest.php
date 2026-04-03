<?php

namespace Tests\Feature\Policies;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GradePolicyTest extends TestCase
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

    // ── viewAny ──────────────────────────────────────────────────────────────

    /** @test */
    public function all_staff_roles_can_view_any_grades(): void
    {
        $grade = Grade::factory()->create();

        foreach (['admin', 'secretary', 'accountant', 'employee'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $this->assertTrue(
                Gate::forUser($user)->allows('viewAny', Grade::class),
                "Failed: {$role} should be able to viewAny grades"
            );
        }
    }

    /** @test */
    public function parent_student_cannot_view_any_grades(): void
    {
        $student = User::factory()->create(['verified' => true]);
        $student->assignRole('parent_student');

        $this->assertFalse(Gate::forUser($student)->allows('viewAny', Grade::class));
    }

    // ── view ─────────────────────────────────────────────────────────────────

    /** @test */
    public function all_staff_roles_can_view_a_grade(): void
    {
        $grade = Grade::factory()->create();

        foreach (['admin', 'secretary', 'accountant', 'employee'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $this->assertTrue(
                Gate::forUser($user)->allows('view', $grade),
                "Failed: {$role} should be able to view a grade"
            );
        }
    }

    /** @test */
    public function parent_student_cannot_view_a_grade(): void
    {
        $student = User::factory()->create(['verified' => true]);
        $student->assignRole('parent_student');

        $grade = Grade::factory()->create();

        $this->assertFalse(Gate::forUser($student)->allows('view', $grade));
    }

    // ── create ───────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_create_grades(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->assertTrue(Gate::forUser($admin)->allows('create', Grade::class));
    }

    /** @test */
    public function secretary_can_create_grades(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $this->assertTrue(Gate::forUser($secretary)->allows('create', Grade::class));
    }

    /** @test */
    public function accountant_cannot_create_grades(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $this->assertFalse(Gate::forUser($accountant)->allows('create', Grade::class));
    }

    /** @test */
    public function employee_cannot_create_grades(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $this->assertFalse(Gate::forUser($employee)->allows('create', Grade::class));
    }

    /** @test */
    public function parent_student_cannot_create_grades(): void
    {
        $student = User::factory()->create(['verified' => true]);
        $student->assignRole('parent_student');

        $this->assertFalse(Gate::forUser($student)->allows('create', Grade::class));
    }

    // ── update ───────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_update_grades(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $grade = Grade::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('update', $grade));
    }

    /** @test */
    public function secretary_can_update_grades(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $grade = Grade::factory()->create();

        $this->assertTrue(Gate::forUser($secretary)->allows('update', $grade));
    }

    /** @test */
    public function accountant_cannot_update_grades(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $grade = Grade::factory()->create();

        $this->assertFalse(Gate::forUser($accountant)->allows('update', $grade));
    }

    /** @test */
    public function employee_cannot_update_grades(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $grade = Grade::factory()->create();

        $this->assertFalse(Gate::forUser($employee)->allows('update', $grade));
    }

    // ── delete ───────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_delete_grades(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $grade = Grade::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('delete', $grade));
    }

    /** @test */
    public function secretary_cannot_delete_grades(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $grade = Grade::factory()->create();

        $this->assertFalse(Gate::forUser($secretary)->allows('delete', $grade));
    }

    /** @test */
    public function accountant_cannot_delete_grades(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $grade = Grade::factory()->create();

        $this->assertFalse(Gate::forUser($accountant)->allows('delete', $grade));
    }
}