<?php

namespace Tests\Feature\Models;

use App\Models\ClassRegistration;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Installment;
use App\Models\Transaction;
use App\Models\TuitionFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_has_transactions_relationship(): void
    {
        $user = User::factory()->create();
        $fee = Fee::factory()->create();

        // Create some transactions for the user
        Transaction::factory()->count(3)->create([
            'user_id' => $user->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertCount(3, $user->transactions);
        $this->assertInstanceOf(Transaction::class, $user->transactions->first());
    }

    /** @test */
    public function transaction_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $fee = Fee::factory()->create();

        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertInstanceOf(User::class, $transaction->user);
        $this->assertEquals($user->id, $transaction->user->id);
        $this->assertEquals($user->email, $transaction->user->email);
    }

    /** @test */
    public function transaction_belongs_to_fee(): void
    {
        $user = User::factory()->create();
        $fee = Fee::factory()->create([
            'title' => 'Test Registration Fee',
        ]);

        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertInstanceOf(Fee::class, $transaction->fee);
        $this->assertEquals($fee->id, $transaction->fee->id);
        $this->assertEquals('Test Registration Fee', $transaction->fee->title);
    }

    /** @test */
    public function transaction_can_have_null_fee(): void
    {
        $user = User::factory()->create();

        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'fee_id' => null, // General payment not tied to specific fee
        ]);

        $this->assertNull($transaction->fee);
        $this->assertInstanceOf(User::class, $transaction->user);
    }

    /** @test */
    public function fee_has_installments_relationship(): void
    {
        $fee = Fee::factory()->create([
            'type' => 'TuitionFee',
            'number_of_installments' => 3,
        ]);

        // Create installments for the fee
        Installment::factory()->count(3)->create([
            'tuition_fee_id' => $fee->id,
        ]);

        $this->assertCount(3, $fee->installments);
        $this->assertInstanceOf(Installment::class, $fee->installments->first());
    }

    /** @test */
    public function installment_belongs_to_tuition_fee(): void
    {
        $fee = Fee::factory()->create([
            'type' => 'TuitionFee',
            'title' => 'Tuition Fee 2024-2025',
        ]);

        $installment = Installment::factory()->create([
            'tuition_fee_id' => $fee->id,
            'number' => 1,
            'amount' => 1000,
        ]);

        $this->assertInstanceOf(Fee::class, $installment->tuitionFee);
        $this->assertEquals($fee->id, $installment->tuitionFee->id);
        $this->assertEquals('Tuition Fee 2024-2025', $installment->tuitionFee->title);
    }

    /** @test */
    public function user_transactions_are_deleted_when_user_is_deleted(): void
    {
        $user = User::factory()->create();
        $fee = Fee::factory()->create();

        // Create transactions for the user
        Transaction::factory()->count(2)->create([
            'user_id' => $user->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertCount(2, Transaction::all());

        // Delete the user
        $user->delete();

        // Transactions should be deleted (cascade)
        $this->assertCount(0, Transaction::all());
    }

    /** @test */
    public function transactions_fee_is_set_to_null_when_fee_is_deleted(): void
    {
        $user = User::factory()->create();
        $fee = Fee::factory()->create();

        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertNotNull($transaction->fee_id);

        // Force delete the fee (soft deletes don't trigger foreign key constraints)
        $fee->forceDelete();

        // Refresh the transaction
        $transaction->refresh();

        // Fee ID should be set to null (set null on delete)
        $this->assertNull($transaction->fee_id);
        $this->assertNull($transaction->fee);
    }

    /** @test */
    public function installments_are_deleted_when_fee_is_deleted(): void
    {
        $fee = Fee::factory()->create([
            'type' => 'TuitionFee',
        ]);

        // Create installments
        Installment::factory()->count(3)->create([
            'tuition_fee_id' => $fee->id,
        ]);

        $this->assertCount(3, Installment::all());

        // Force delete the fee (soft deletes don't trigger foreign key constraints)
        $fee->forceDelete();

        // Installments should be deleted (cascade)
        $this->assertCount(0, Installment::all());
    }

    /** @test */
    public function user_model_uses_uuid_as_primary_key(): void
    {
        $user = User::factory()->create();

        $this->assertIsString($user->id);
        $this->assertEquals(36, strlen($user->id)); // UUID length
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $user->id
        );
    }

    /** @test */
    public function fee_model_uses_uuid_as_primary_key(): void
    {
        $fee = Fee::factory()->create();

        $this->assertIsString($fee->id);
        $this->assertEquals(36, strlen($fee->id));
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $fee->id
        );
    }

    /** @test */
    public function transaction_model_uses_uuid_as_primary_key(): void
    {
        $user = User::factory()->create();
        $fee = Fee::factory()->create();

        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'fee_id' => $fee->id,
        ]);

        $this->assertIsString($transaction->id);
        $this->assertEquals(36, strlen($transaction->id));
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $transaction->id
        );
    }

    /** @test */
    public function installment_model_uses_uuid_as_primary_key(): void
    {
        $fee = Fee::factory()->create([
            'type' => 'TuitionFee',
        ]);

        $installment = Installment::factory()->create([
            'tuition_fee_id' => $fee->id,
        ]);

        $this->assertIsString($installment->id);
        $this->assertEquals(36, strlen($installment->id));
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $installment->id
        );
    }

    /** @test */
    public function multiple_transactions_can_belong_to_same_user_and_fee(): void
    {
        $user = User::factory()->create();
        $fee = Fee::factory()->create();

        // Create multiple transactions (e.g., installment payments)
        $transaction1 = Transaction::factory()->create([
            'user_id' => $user->id,
            'fee_id' => $fee->id,
            'amount' => 1000,
            'status' => 'completed',
        ]);

        $transaction2 = Transaction::factory()->create([
            'user_id' => $user->id,
            'fee_id' => $fee->id,
            'amount' => 1000,
            'status' => 'pending',
        ]);

        $this->assertCount(2, $user->transactions);
        $this->assertEquals($fee->id, $transaction1->fee_id);
        $this->assertEquals($fee->id, $transaction2->fee_id);
    }

    /** @test */
    public function user_can_have_transactions_for_different_fees(): void
    {
        $user = User::factory()->create();
        $registrationFee = Fee::factory()->create(['type' => 'RegistrationFee']);
        $tuitionFee = Fee::factory()->create(['type' => 'TuitionFee']);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'fee_id' => $registrationFee->id,
        ]);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'fee_id' => $tuitionFee->id,
        ]);

        $this->assertCount(2, $user->transactions);
        $this->assertNotEquals(
            $user->transactions[0]->fee_id,
            $user->transactions[1]->fee_id
        );
    }

    // ── Grade & ClassRegistration relationships ───────────────────────────────

    /** @test */
    public function grade_uses_uuid_as_primary_key(): void
    {
        $grade = Grade::factory()->create();

        $this->assertIsString($grade->id);
        $this->assertEquals(36, strlen($grade->id));
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $grade->id
        );
    }

    /** @test */
    public function class_registration_uses_uuid_as_primary_key(): void
    {
        $registration = ClassRegistration::factory()->create();

        $this->assertIsString($registration->id);
        $this->assertEquals(36, strlen($registration->id));
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $registration->id
        );
    }

    /** @test */
    public function class_registration_belongs_to_user(): void
    {
        $user         = User::factory()->create();
        $registration = ClassRegistration::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $registration->user);
        $this->assertEquals($user->id, $registration->user->id);
    }

    /** @test */
    public function class_registration_belongs_to_grade(): void
    {
        $grade        = Grade::factory()->create();
        $registration = ClassRegistration::factory()->create(['grade_id' => $grade->id]);

        $this->assertInstanceOf(Grade::class, $registration->grade);
        $this->assertEquals($grade->id, $registration->grade->id);
    }

    /** @test */
    public function class_registration_belongs_to_transaction(): void
    {
        $user        = User::factory()->create();
        $fee         = Fee::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'fee_id'  => $fee->id,
        ]);

        $registration = ClassRegistration::factory()->create([
            'user_id'        => $user->id,
            'transaction_id' => $transaction->id,
        ]);

        $this->assertInstanceOf(Transaction::class, $registration->transaction);
        $this->assertEquals($transaction->id, $registration->transaction->id);
    }

    /** @test */
    public function class_registration_transaction_id_is_nullable(): void
    {
        $registration = ClassRegistration::factory()->create(['transaction_id' => null]);

        $this->assertNull($registration->transaction_id);
        $this->assertNull($registration->transaction);
    }

    /** @test */
    public function grade_has_many_class_registrations(): void
    {
        $grade = Grade::factory()->create();

        ClassRegistration::factory()->count(3)->create(['grade_id' => $grade->id]);

        $this->assertCount(3, $grade->classRegistrations);
        $this->assertInstanceOf(ClassRegistration::class, $grade->classRegistrations->first());
    }

    /** @test */
    public function grade_registration_fees_filters_by_full_namespace_type(): void
    {
        $grade = Grade::factory()->create();

        // Fee saved with full namespace (as form does)
        Fee::create([
            'type'          => 'App\Models\RegistrationFee',
            'title'         => 'Frais inscription',
            'grade_id'      => $grade->id,
            'total_amount'  => 10000,
            'academic_year' => '2026-2027',
        ]);

        // Fee saved with short name (should NOT be counted)
        Fee::create([
            'type'          => 'RegistrationFee',
            'title'         => 'Old fee',
            'grade_id'      => $grade->id,
            'total_amount'  => 8000,
            'academic_year' => '2025-2026',
        ]);

        $grade->load('registrationFees');

        $this->assertCount(1, $grade->registrationFees);
        $this->assertEquals('App\Models\RegistrationFee', $grade->registrationFees->first()->type);
    }

    /** @test */
    public function class_registrations_are_deleted_when_user_is_deleted(): void
    {
        $user         = User::factory()->create();
        $registration = ClassRegistration::factory()->create(['user_id' => $user->id]);

        $user->forceDelete();

        $this->assertDatabaseMissing('class_registrations', ['id' => $registration->id]);
    }
}