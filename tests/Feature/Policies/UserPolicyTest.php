<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'secretary']);
        Role::create(['name' => 'employee']);
        Role::create(['name' => 'accountant']);
        Role::create(['name' => 'parent_student']);
    }

    /** @test */
    public function admin_can_view_any_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->assertTrue(Gate::forUser($admin)->allows('viewAny', User::class));
    }

    /** @test */
    public function secretary_can_view_any_users(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $this->assertTrue(Gate::forUser($secretary)->allows('viewAny', User::class));
    }

    /** @test */
    public function employee_can_view_any_users(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $this->assertTrue(Gate::forUser($employee)->allows('viewAny', User::class));
    }

    /** @test */
    public function student_cannot_view_any_users(): void
    {
        $student = User::factory()->create();
        $student->assignRole('parent_student');

        $this->assertFalse(Gate::forUser($student)->allows('viewAny', User::class));
    }

    /** @test */
    public function admin_can_view_any_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $otherUser = User::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('view', $otherUser));
    }

    /** @test */
    public function secretary_can_view_any_user(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $otherUser = User::factory()->create();

        $this->assertTrue(Gate::forUser($secretary)->allows('view', $otherUser));
    }

    /** @test */
    public function user_can_view_their_own_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('parent_student');

        $this->assertTrue(Gate::forUser($user)->allows('view', $user));
    }

    /** @test */
    public function user_cannot_view_other_users_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('parent_student');

        $otherUser = User::factory()->create();

        $this->assertFalse(Gate::forUser($user)->allows('view', $otherUser));
    }

    /** @test */
    public function admin_can_create_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->assertTrue(Gate::forUser($admin)->allows('create', User::class));
    }

    /** @test */
    public function secretary_can_create_users(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $this->assertTrue(Gate::forUser($secretary)->allows('create', User::class));
    }

    /** @test */
    public function student_cannot_create_users(): void
    {
        $student = User::factory()->create();
        $student->assignRole('parent_student');

        $this->assertFalse(Gate::forUser($student)->allows('create', User::class));
    }

    /** @test */
    public function accountant_cannot_create_users(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $this->assertFalse(Gate::forUser($accountant)->allows('create', User::class));
    }

    /** @test */
    public function admin_can_update_any_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $otherUser = User::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('update', $otherUser));
    }

    /** @test */
    public function secretary_can_update_any_user(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $otherUser = User::factory()->create();

        $this->assertTrue(Gate::forUser($secretary)->allows('update', $otherUser));
    }

    /** @test */
    public function user_can_update_their_own_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('parent_student');

        $this->assertTrue(Gate::forUser($user)->allows('update', $user));
    }

    /** @test */
    public function user_cannot_update_other_users(): void
    {
        $user = User::factory()->create();
        $user->assignRole('parent_student');

        $otherUser = User::factory()->create();

        $this->assertFalse(Gate::forUser($user)->allows('update', $otherUser));
    }

    /** @test */
    public function admin_can_delete_other_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $otherUser = User::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('delete', $otherUser));
    }

    /** @test */
    public function admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->assertFalse(Gate::forUser($admin)->allows('delete', $admin));
    }

    /** @test */
    public function non_admin_cannot_delete_users(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $otherUser = User::factory()->create();

        $this->assertFalse(Gate::forUser($secretary)->allows('delete', $otherUser));
    }

    /** @test */
    public function admin_can_restore_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $deletedUser = User::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('restore', $deletedUser));
    }

    /** @test */
    public function non_admin_cannot_restore_users(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $deletedUser = User::factory()->create();

        $this->assertFalse(Gate::forUser($secretary)->allows('restore', $deletedUser));
    }

    /** @test */
    public function admin_can_force_delete_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('forceDelete', $user));
    }

    /** @test */
    public function non_admin_cannot_force_delete_users(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $user = User::factory()->create();

        $this->assertFalse(Gate::forUser($secretary)->allows('forceDelete', $user));
    }
}
