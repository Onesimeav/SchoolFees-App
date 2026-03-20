<?php

namespace Tests\Feature\Policies;

use App\Models\Fee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FeePolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'accountant']);
        Role::create(['name' => 'secretary']);
        Role::create(['name' => 'employee']);
        Role::create(['name' => 'parent_student']);
    }

    /** @test */
    public function all_authenticated_users_can_view_any_fees(): void
    {
        $student = User::factory()->create();
        $student->assignRole('parent_student');

        $this->assertTrue(Gate::forUser($student)->allows('viewAny', Fee::class));
    }

    /** @test */
    public function all_authenticated_users_can_view_fees(): void
    {
        $student = User::factory()->create();
        $student->assignRole('parent_student');

        $fee = Fee::factory()->create();

        $this->assertTrue(Gate::forUser($student)->allows('view', $fee));
    }

    /** @test */
    public function admin_can_create_fees(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->assertTrue(Gate::forUser($admin)->allows('create', Fee::class));
    }

    /** @test */
    public function accountant_can_create_fees(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $this->assertTrue(Gate::forUser($accountant)->allows('create', Fee::class));
    }

    /** @test */
    public function secretary_can_create_fees(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $this->assertTrue(Gate::forUser($secretary)->allows('create', Fee::class));
    }

    /** @test */
    public function employee_cannot_create_fees(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $this->assertFalse(Gate::forUser($employee)->allows('create', Fee::class));
    }

    /** @test */
    public function student_cannot_create_fees(): void
    {
        $student = User::factory()->create();
        $student->assignRole('parent_student');

        $this->assertFalse(Gate::forUser($student)->allows('create', Fee::class));
    }

    /** @test */
    public function admin_can_update_fees(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $fee = Fee::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('update', $fee));
    }

    /** @test */
    public function accountant_can_update_fees(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $fee = Fee::factory()->create();

        $this->assertTrue(Gate::forUser($accountant)->allows('update', $fee));
    }

    /** @test */
    public function secretary_can_update_fees(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $fee = Fee::factory()->create();

        $this->assertTrue(Gate::forUser($secretary)->allows('update', $fee));
    }

    /** @test */
    public function student_cannot_update_fees(): void
    {
        $student = User::factory()->create();
        $student->assignRole('parent_student');

        $fee = Fee::factory()->create();

        $this->assertFalse(Gate::forUser($student)->allows('update', $fee));
    }

    /** @test */
    public function admin_can_delete_fees(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $fee = Fee::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('delete', $fee));
    }

    /** @test */
    public function accountant_cannot_delete_fees(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $fee = Fee::factory()->create();

        $this->assertFalse(Gate::forUser($accountant)->allows('delete', $fee));
    }

    /** @test */
    public function secretary_can_delete_fees(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $fee = Fee::factory()->create();

        $this->assertTrue(Gate::forUser($secretary)->allows('delete', $fee));
    }

    /** @test */
    public function admin_can_restore_fees(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $fee = Fee::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('restore', $fee));
    }

    /** @test */
    public function non_admin_cannot_restore_fees(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $fee = Fee::factory()->create();

        $this->assertFalse(Gate::forUser($accountant)->allows('restore', $fee));
    }

    /** @test */
    public function admin_can_force_delete_fees(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $fee = Fee::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('forceDelete', $fee));
    }

    /** @test */
    public function non_admin_cannot_force_delete_fees(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $fee = Fee::factory()->create();

        $this->assertFalse(Gate::forUser($accountant)->allows('forceDelete', $fee));
    }

    /** @test */
    public function admin_can_approve_fees(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $fee = Fee::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('approve', $fee));
    }

    /** @test */
    public function accountant_cannot_approve_fees(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $fee = Fee::factory()->create();

        $this->assertFalse(Gate::forUser($accountant)->allows('approve', $fee));
    }
}
