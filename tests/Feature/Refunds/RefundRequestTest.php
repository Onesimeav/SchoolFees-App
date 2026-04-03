<?php

namespace Tests\Feature\Refunds;

use App\Filament\Portal\Resources\RefundRequests\Pages\ViewRefundRequest as PortalViewRefundRequest;
use App\Filament\Portal\Resources\Transactions\Pages\ViewTransaction;
use App\Filament\Staff\Resources\RefundRequests\Pages\ViewRefundRequest;
use App\Mail\RefundConfirmationMail;
use App\Models\ClassRegistration;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\RefundRequest;
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

class RefundRequestTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $admin;
    protected User $accountant;
    protected User $secretary;
    protected Grade $grade;
    protected Fee $generalFee;
    protected Fee $tuitionFee;
    protected Fee $regFee;
    protected Transaction $regTransaction;
    protected Transaction $generalFeeTransaction;
    protected ClassRegistration $registration;
    protected string $academicYear;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'accountant']);
        Role::create(['name' => 'secretary']);
        Role::create(['name' => 'employee']);
        Role::create(['name' => 'parent_student']);

        $this->academicYear = now()->year . '-' . (now()->year + 1);

        $this->student = User::factory()->create(['verified' => true]);
        $this->student->assignRole('parent_student');

        $this->admin = User::factory()->create(['verified' => true]);
        $this->admin->assignRole('admin');

        $this->accountant = User::factory()->create(['verified' => true]);
        $this->accountant->assignRole('accountant');

        $this->secretary = User::factory()->create(['verified' => true]);
        $this->secretary->assignRole('secretary');

        $this->grade = Grade::factory()->create(['name' => 'Terminal']);

        $this->regFee = Fee::create([
            'type'          => 'App\Models\RegistrationFee',
            'title'         => 'Frais inscription Terminal',
            'grade_id'      => $this->grade->id,
            'total_amount'  => 5000,
            'academic_year' => $this->academicYear,
            'due_before'    => now()->addMonths(1),
        ]);

        $this->generalFee = Fee::create([
            'type'          => 'App\Models\GeneralFee',
            'title'         => 'Frais de sortie scolaire',
            'grade_id'      => $this->grade->id,
            'total_amount'  => 15000,
            'academic_year' => $this->academicYear,
            'due_before'    => now()->addMonths(1),
        ]);

        $this->regTransaction = Transaction::create([
            'user_id'      => $this->student->id,
            'fee_id'       => $this->regFee->id,
            'amount'       => 5000,
            'date'         => now()->toDateString(),
            'status'       => 'completed',
            'phone_number' => '97000000',
        ]);

        $this->registration = ClassRegistration::create([
            'user_id'        => $this->student->id,
            'grade_id'       => $this->grade->id,
            'status'         => 'accepted',
            'transaction_id' => $this->regTransaction->id,
        ]);

        $this->generalFeeTransaction = Transaction::create([
            'user_id'           => $this->student->id,
            'fee_id'            => $this->generalFee->id,
            'amount'            => 15000,
            'date'              => now()->toDateString(),
            'status'            => 'completed',
            'kkiapay_reference' => 'REF-GF-001',
            'phone_number'      => '97000000',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('portal'));
        Storage::fake('supabase');
    }

    private function bindSuccessfulKkiapay(): void
    {
        $mock = $this->createMock(KkiapayService::class);
        $mock->method('refund')->willReturn(true);
        app()->instance(KkiapayService::class, $mock);
    }

    private function bindFailedKkiapay(): void
    {
        $mock = $this->createMock(KkiapayService::class);
        $mock->method('refund')->willReturn(false);
        app()->instance(KkiapayService::class, $mock);
    }

    /** @test */
    public function student_can_request_refund_for_general_fee_transaction(): void
    {
        $this->actingAs($this->student);

        Livewire::test(ViewTransaction::class, ['record' => $this->generalFeeTransaction->getRouteKey()])
            ->callAction('request_refund', data: ['reason' => 'Je souhaite un remboursement'])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('refund_requests', [
            'transaction_id' => $this->generalFeeTransaction->id,
            'user_id'        => $this->student->id,
            'status'         => 'pending',
            'reason'         => 'Je souhaite un remboursement',
        ]);
    }

    /** @test */
    public function student_cannot_request_refund_for_registration_transaction(): void
    {
        $this->actingAs($this->student);

        Livewire::test(ViewTransaction::class, ['record' => $this->regTransaction->getRouteKey()])
            ->assertActionHidden('request_refund');
    }

    /** @test */
    public function student_cannot_request_refund_for_pending_transaction(): void
    {
        $pendingTx = Transaction::create([
            'user_id'      => $this->student->id,
            'fee_id'       => $this->generalFee->id,
            'amount'       => 15000,
            'date'         => now()->toDateString(),
            'status'       => 'pending',
            'phone_number' => '97000000',
        ]);

        $this->actingAs($this->student);

        Livewire::test(ViewTransaction::class, ['record' => $pendingTx->getRouteKey()])
            ->assertActionHidden('request_refund');
    }

    /** @test */
    public function student_cannot_create_duplicate_refund_request(): void
    {
        RefundRequest::create([
            'transaction_id' => $this->generalFeeTransaction->id,
            'user_id'        => $this->student->id,
            'reason'         => 'First request',
            'status'         => 'pending',
        ]);

        $this->actingAs($this->student);

        Livewire::test(ViewTransaction::class, ['record' => $this->generalFeeTransaction->getRouteKey()])
            ->assertActionHidden('request_refund');
    }

    /** @test */
    public function refund_button_reappears_after_refusal(): void
    {
        RefundRequest::create([
            'transaction_id' => $this->generalFeeTransaction->id,
            'user_id'        => $this->student->id,
            'reason'         => 'First request',
            'status'         => 'refused',
        ]);

        $this->actingAs($this->student);

        Livewire::test(ViewTransaction::class, ['record' => $this->generalFeeTransaction->getRouteKey()])
            ->assertActionVisible('request_refund');
    }

    /** @test */
    public function admin_can_accept_refund_request(): void
    {
        Mail::fake();
        $this->bindSuccessfulKkiapay();

        $refundRequest = RefundRequest::create([
            'transaction_id' => $this->generalFeeTransaction->id,
            'user_id'        => $this->student->id,
            'reason'         => 'Raison valide',
            'status'         => 'pending',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('staff'));
        $this->actingAs($this->admin);

        Livewire::test(ViewRefundRequest::class, ['record' => $refundRequest->getRouteKey()])
            ->callAction('accept');

        $this->assertDatabaseHas('refund_requests', [
            'id'     => $refundRequest->id,
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas('transactions', [
            'id'     => $this->generalFeeTransaction->id,
            'status' => 'refunded',
        ]);
    }

    /** @test */
    public function accountant_can_accept_refund_request(): void
    {
        Mail::fake();
        $this->bindSuccessfulKkiapay();

        $refundRequest = RefundRequest::create([
            'transaction_id' => $this->generalFeeTransaction->id,
            'user_id'        => $this->student->id,
            'reason'         => 'Raison valide',
            'status'         => 'pending',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('staff'));
        $this->actingAs($this->accountant);

        Livewire::test(ViewRefundRequest::class, ['record' => $refundRequest->getRouteKey()])
            ->callAction('accept');

        $this->assertDatabaseHas('refund_requests', [
            'id'     => $refundRequest->id,
            'status' => 'accepted',
        ]);
    }

    /** @test */
    public function secretary_cannot_accept_refund_request(): void
    {
        Mail::fake();

        $refundRequest = RefundRequest::create([
            'transaction_id' => $this->generalFeeTransaction->id,
            'user_id'        => $this->student->id,
            'reason'         => 'Raison',
            'status'         => 'pending',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('staff'));
        $this->actingAs($this->secretary);

        Livewire::test(ViewRefundRequest::class, ['record' => $refundRequest->getRouteKey()])
            ->assertActionHidden('accept');
    }

    /** @test */
    public function accept_sends_refund_confirmation_email(): void
    {
        Mail::fake();
        $this->bindSuccessfulKkiapay();

        $refundRequest = RefundRequest::create([
            'transaction_id' => $this->generalFeeTransaction->id,
            'user_id'        => $this->student->id,
            'reason'         => 'Raison',
            'status'         => 'pending',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('staff'));
        $this->actingAs($this->admin);

        Livewire::test(ViewRefundRequest::class, ['record' => $refundRequest->getRouteKey()])
            ->callAction('accept');

        Mail::assertSent(RefundConfirmationMail::class, function ($mail) {
            return $mail->hasTo($this->student->email);
        });

        Storage::disk('supabase')->assertExists(
            'receipts/' . $this->student->id . '/refund-REF-GF-001.pdf'
        );
    }

    /** @test */
    public function accept_reverts_fee_to_unpaid(): void
    {
        Mail::fake();
        $this->bindSuccessfulKkiapay();

        $refundRequest = RefundRequest::create([
            'transaction_id' => $this->generalFeeTransaction->id,
            'user_id'        => $this->student->id,
            'reason'         => 'Raison',
            'status'         => 'pending',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('staff'));
        $this->actingAs($this->admin);

        Livewire::test(ViewRefundRequest::class, ['record' => $refundRequest->getRouteKey()])
            ->callAction('accept');

        // The transaction is now refunded — fee should no longer count as paid
        $paidCount = Transaction::where('user_id', $this->student->id)
            ->where('status', 'completed')
            ->where('fee_id', $this->generalFee->id)
            ->count();

        $this->assertEquals(0, $paidCount);
    }

    /** @test */
    public function accept_aborts_when_kkiapay_returns_failure(): void
    {
        Mail::fake();
        $this->bindFailedKkiapay();

        $refundRequest = RefundRequest::create([
            'transaction_id' => $this->generalFeeTransaction->id,
            'user_id'        => $this->student->id,
            'reason'         => 'Raison',
            'status'         => 'pending',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('staff'));
        $this->actingAs($this->admin);

        Livewire::test(ViewRefundRequest::class, ['record' => $refundRequest->getRouteKey()])
            ->callAction('accept');

        // Request and transaction should remain unchanged
        $this->assertDatabaseHas('refund_requests', [
            'id'     => $refundRequest->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('transactions', [
            'id'     => $this->generalFeeTransaction->id,
            'status' => 'completed',
        ]);

        Mail::assertNotSent(RefundConfirmationMail::class);
    }

    /** @test */
    public function admin_can_refuse_refund_request(): void
    {
        $refundRequest = RefundRequest::create([
            'transaction_id' => $this->generalFeeTransaction->id,
            'user_id'        => $this->student->id,
            'reason'         => 'Raison',
            'status'         => 'pending',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('staff'));
        $this->actingAs($this->admin);

        Livewire::test(ViewRefundRequest::class, ['record' => $refundRequest->getRouteKey()])
            ->callAction('refuse', data: ['notes' => 'Demande non conforme']);

        $this->assertDatabaseHas('refund_requests', [
            'id'     => $refundRequest->id,
            'status' => 'refused',
            'notes'  => 'Demande non conforme',
        ]);
    }

    /** @test */
    public function refuse_sends_no_email(): void
    {
        Mail::fake();

        $refundRequest = RefundRequest::create([
            'transaction_id' => $this->generalFeeTransaction->id,
            'user_id'        => $this->student->id,
            'reason'         => 'Raison',
            'status'         => 'pending',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('staff'));
        $this->actingAs($this->admin);

        Livewire::test(ViewRefundRequest::class, ['record' => $refundRequest->getRouteKey()])
            ->callAction('refuse', data: ['notes' => 'Refusé']);

        Mail::assertNotSent(RefundConfirmationMail::class);
    }

    /** @test */
    public function student_can_view_their_own_refund_requests(): void
    {
        $refundRequest = RefundRequest::create([
            'transaction_id' => $this->generalFeeTransaction->id,
            'user_id'        => $this->student->id,
            'reason'         => 'Raison',
            'status'         => 'pending',
        ]);

        $this->actingAs($this->student);

        Livewire::test(PortalViewRefundRequest::class, ['record' => $refundRequest->getRouteKey()])
            ->assertSuccessful();
    }

    /** @test */
    public function student_cannot_view_other_students_refund_requests(): void
    {
        $otherStudent = User::factory()->create(['verified' => true]);
        $otherStudent->assignRole('parent_student');

        $otherTx = Transaction::create([
            'user_id'           => $otherStudent->id,
            'fee_id'            => $this->generalFee->id,
            'amount'            => 15000,
            'date'              => now()->toDateString(),
            'status'            => 'completed',
            'kkiapay_reference' => 'REF-OTHER-001',
            'phone_number'      => '97000001',
        ]);

        $otherRequest = RefundRequest::create([
            'transaction_id' => $otherTx->id,
            'user_id'        => $otherStudent->id,
            'reason'         => 'Raison autre',
            'status'         => 'pending',
        ]);

        $this->actingAs($this->student);

        // Portal resource is scoped to auth()->id() — other student's record is not in the query
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::test(PortalViewRefundRequest::class, ['record' => $otherRequest->getRouteKey()]);
    }

    /** @test */
    public function staff_can_view_all_refund_requests(): void
    {
        $refundRequest = RefundRequest::create([
            'transaction_id' => $this->generalFeeTransaction->id,
            'user_id'        => $this->student->id,
            'reason'         => 'Raison',
            'status'         => 'pending',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('staff'));

        foreach ([$this->admin, $this->accountant, $this->secretary] as $staff) {
            $this->actingAs($staff);

            Livewire::test(ViewRefundRequest::class, ['record' => $refundRequest->getRouteKey()])
                ->assertSuccessful();
        }
    }
}