<?php

namespace Tests\Feature\Policies;

use App\Models\Fee;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TransactionPolicyTest extends TestCase
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
    public function all_authenticated_users_can_view_any_transactions(): void
    {
        $student = User::factory()->create();
        $student->assignRole('parent_student');

        $this->assertTrue(Gate::forUser($student)->allows('viewAny', Transaction::class));
    }

    /** @test */
    public function admin_can_view_any_transaction(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertTrue(Gate::forUser($admin)->allows('view', $transaction));
    }

    /** @test */
    public function accountant_can_view_any_transaction(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertTrue(Gate::forUser($accountant)->allows('view', $transaction));
    }

    /** @test */
    public function secretary_can_view_any_transaction(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertTrue(Gate::forUser($secretary)->allows('view', $transaction));
    }

    /** @test */
    public function student_can_view_their_own_transaction(): void
    {
        $student = User::factory()->create();
        $student->assignRole('parent_student');

        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertTrue(Gate::forUser($student)->allows('view', $transaction));
    }

    /** @test */
    public function student_cannot_view_other_students_transaction(): void
    {
        $student = User::factory()->create();
        $student->assignRole('parent_student');

        $otherStudent = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $otherStudent->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertFalse(Gate::forUser($student)->allows('view', $transaction));
    }

    /** @test */
    public function admin_can_create_transactions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->assertTrue(Gate::forUser($admin)->allows('create', Transaction::class));
    }

    /** @test */
    public function accountant_can_create_transactions(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $this->assertTrue(Gate::forUser($accountant)->allows('create', Transaction::class));
    }

    /** @test */
    public function secretary_cannot_create_transactions(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $this->assertFalse(Gate::forUser($secretary)->allows('create', Transaction::class));
    }

    /** @test */
    public function student_cannot_create_transactions(): void
    {
        $student = User::factory()->create();
        $student->assignRole('parent_student');

        $this->assertFalse(Gate::forUser($student)->allows('create', Transaction::class));
    }

    /** @test */
    public function admin_can_update_pending_transaction(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'status' => 'pending',
        ]);

        $this->assertTrue(Gate::forUser($admin)->allows('update', $transaction));
    }

    /** @test */
    public function accountant_can_update_pending_transaction(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'status' => 'pending',
        ]);

        $this->assertTrue(Gate::forUser($accountant)->allows('update', $transaction));
    }

    /** @test */
    public function admin_cannot_update_completed_transaction(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'status' => 'completed',
        ]);

        $this->assertFalse(Gate::forUser($admin)->allows('update', $transaction));
    }

    /** @test */
    public function student_cannot_update_transactions(): void
    {
        $student = User::factory()->create();
        $student->assignRole('parent_student');

        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'status' => 'pending',
        ]);

        $this->assertFalse(Gate::forUser($student)->allows('update', $transaction));
    }

    /** @test */
    public function admin_can_delete_transactions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertTrue(Gate::forUser($admin)->allows('delete', $transaction));
    }

    /** @test */
    public function accountant_cannot_delete_transactions(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertFalse(Gate::forUser($accountant)->allows('delete', $transaction));
    }

    /** @test */
    public function admin_can_process_pending_transaction(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'status' => 'pending',
        ]);

        $this->assertTrue(Gate::forUser($admin)->allows('process', $transaction));
    }

    /** @test */
    public function accountant_can_process_pending_transaction(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'status' => 'pending',
        ]);

        $this->assertTrue(Gate::forUser($accountant)->allows('process', $transaction));
    }

    /** @test */
    public function admin_cannot_process_completed_transaction(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'status' => 'completed',
        ]);

        $this->assertFalse(Gate::forUser($admin)->allows('process', $transaction));
    }

    /** @test */
    public function secretary_cannot_process_transactions(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'status' => 'pending',
        ]);

        $this->assertFalse(Gate::forUser($secretary)->allows('process', $transaction));
    }

    /** @test */
    public function student_cannot_process_transactions(): void
    {
        $student = User::factory()->create();
        $student->assignRole('parent_student');

        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'status' => 'pending',
        ]);

        $this->assertFalse(Gate::forUser($student)->allows('process', $transaction));
    }

    /** @test */
    public function admin_can_refund_completed_transaction(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'status' => 'completed',
        ]);

        $this->assertTrue(Gate::forUser($admin)->allows('refund', $transaction));
    }

    /** @test */
    public function admin_cannot_refund_pending_transaction(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'status' => 'pending',
        ]);

        $this->assertFalse(Gate::forUser($admin)->allows('refund', $transaction));
    }

    /** @test */
    public function accountant_cannot_refund_transactions(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
            'status' => 'completed',
        ]);

        $this->assertFalse(Gate::forUser($accountant)->allows('refund', $transaction));
    }

    /** @test */
    public function admin_can_restore_transactions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertTrue(Gate::forUser($admin)->allows('restore', $transaction));
    }

    /** @test */
    public function non_admin_cannot_restore_transactions(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertFalse(Gate::forUser($accountant)->allows('restore', $transaction));
    }

    /** @test */
    public function admin_can_force_delete_transactions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertTrue(Gate::forUser($admin)->allows('forceDelete', $transaction));
    }

    /** @test */
    public function non_admin_cannot_force_delete_transactions(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $student = User::factory()->create();
        $fee = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $student->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertFalse(Gate::forUser($accountant)->allows('forceDelete', $transaction));
    }
}