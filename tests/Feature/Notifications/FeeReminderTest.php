<?php

namespace Tests\Feature\Notifications;

use App\Mail\FeeReminderMail;
use App\Models\ClassRegistration;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Installment;
use App\Models\Transaction;
use App\Models\TuitionFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FeeReminderTest extends TestCase
{
    use RefreshDatabase;

    protected User   $student;
    protected Grade  $grade;
    protected string $academicYear;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'parent_student']);

        $this->academicYear = now()->year . '-' . (now()->year + 1);

        $this->grade = Grade::factory()->create(['name' => 'Terminal']);

        $this->student = User::factory()->create(['verified' => true]);
        $this->student->assignRole('parent_student');

        // Registration fee + accepted class registration (sets the academic year context)
        $regFee = Fee::create([
            'type'          => 'App\Models\RegistrationFee',
            'title'         => 'Frais inscription',
            'grade_id'      => $this->grade->id,
            'total_amount'  => 5000,
            'academic_year' => $this->academicYear,
        ]);

        $regTransaction = Transaction::create([
            'user_id'      => $this->student->id,
            'fee_id'       => $regFee->id,
            'amount'       => 5000,
            'date'         => now()->toDateString(),
            'status'       => 'completed',
            'phone_number' => '97000000',
        ]);

        ClassRegistration::create([
            'user_id'        => $this->student->id,
            'grade_id'       => $this->grade->id,
            'status'         => 'accepted',
            'transaction_id' => $regTransaction->id,
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function createGeneralFee(string $dueDate): Fee
    {
        return Fee::create([
            'type'          => 'App\Models\GeneralFee',
            'title'         => 'Frais de sortie',
            'grade_id'      => $this->grade->id,
            'total_amount'  => 10000,
            'academic_year' => $this->academicYear,
            'due_before'    => $dueDate,
        ]);
    }

    private function createTuitionFeeWithInstallment(string $dueDate): array
    {
        $tuition = Fee::create([
            'type'                   => 'App\Models\TuitionFee',
            'title'                  => 'Frais de scolarité',
            'grade_id'               => $this->grade->id,
            'total_amount'           => 150000,
            'academic_year'          => $this->academicYear,
            'number_of_installments' => 3,
        ]);

        $installment = Installment::create([
            'tuition_fee_id' => $tuition->id,
            'number'         => 1,
            'amount'         => 50000,
            'due_date'       => $dueDate,
        ]);

        return [$tuition, $installment];
    }

    // ── General fee — near due ────────────────────────────────────────────────

    /** @test */
    public function sends_near_due_reminder_for_unpaid_general_fee_due_in_7_days(): void
    {
        Mail::fake();
        $this->createGeneralFee(now()->addDays(7)->toDateString());

        $this->artisan('fees:send-reminders')->assertSuccessful();

        Mail::assertSent(FeeReminderMail::class, function ($mail) {
            return $mail->hasTo($this->student->email)
                && $mail->type === 'near_due';
        });
    }

    /** @test */
    public function does_not_send_reminder_for_general_fee_due_in_6_days(): void
    {
        Mail::fake();
        $this->createGeneralFee(now()->addDays(6)->toDateString());

        $this->artisan('fees:send-reminders')->assertSuccessful();

        Mail::assertNotSent(FeeReminderMail::class);
    }

    /** @test */
    public function does_not_send_reminder_for_general_fee_due_in_8_days(): void
    {
        Mail::fake();
        $this->createGeneralFee(now()->addDays(8)->toDateString());

        $this->artisan('fees:send-reminders')->assertSuccessful();

        Mail::assertNotSent(FeeReminderMail::class);
    }

    // ── General fee — past due ────────────────────────────────────────────────

    /** @test */
    public function sends_past_due_alert_for_unpaid_general_fee_1_day_overdue(): void
    {
        Mail::fake();
        $this->createGeneralFee(now()->subDay()->toDateString());

        $this->artisan('fees:send-reminders')->assertSuccessful();

        Mail::assertSent(FeeReminderMail::class, function ($mail) {
            return $mail->hasTo($this->student->email)
                && $mail->type === 'past_due';
        });
    }

    /** @test */
    public function does_not_send_alert_for_general_fee_2_days_overdue(): void
    {
        Mail::fake();
        $this->createGeneralFee(now()->subDays(2)->toDateString());

        $this->artisan('fees:send-reminders')->assertSuccessful();

        Mail::assertNotSent(FeeReminderMail::class);
    }

    // ── General fee — already paid ────────────────────────────────────────────

    /** @test */
    public function does_not_send_reminder_for_already_paid_general_fee(): void
    {
        Mail::fake();
        $fee = $this->createGeneralFee(now()->addDays(7)->toDateString());

        Transaction::create([
            'user_id'           => $this->student->id,
            'fee_id'            => $fee->id,
            'amount'            => 10000,
            'date'              => now()->toDateString(),
            'status'            => 'completed',
            'kkiapay_reference' => 'REF-PAID',
            'phone_number'      => '97000000',
        ]);

        $this->artisan('fees:send-reminders')->assertSuccessful();

        Mail::assertNotSent(FeeReminderMail::class);
    }

    // ── Tuition installment — near due ────────────────────────────────────────

    /** @test */
    public function sends_near_due_reminder_for_unpaid_tuition_installment_due_in_7_days(): void
    {
        Mail::fake();
        $this->createTuitionFeeWithInstallment(now()->addDays(7)->toDateString());

        $this->artisan('fees:send-reminders')->assertSuccessful();

        Mail::assertSent(FeeReminderMail::class, function ($mail) {
            return $mail->hasTo($this->student->email)
                && $mail->type === 'near_due'
                && $mail->installmentNumber === 1;
        });
    }

    /** @test */
    public function does_not_send_reminder_for_tuition_installment_due_in_5_days(): void
    {
        Mail::fake();
        $this->createTuitionFeeWithInstallment(now()->addDays(5)->toDateString());

        $this->artisan('fees:send-reminders')->assertSuccessful();

        Mail::assertNotSent(FeeReminderMail::class);
    }

    // ── Tuition installment — past due ────────────────────────────────────────

    /** @test */
    public function sends_past_due_alert_for_unpaid_tuition_installment_1_day_overdue(): void
    {
        Mail::fake();
        $this->createTuitionFeeWithInstallment(now()->subDay()->toDateString());

        $this->artisan('fees:send-reminders')->assertSuccessful();

        Mail::assertSent(FeeReminderMail::class, function ($mail) {
            return $mail->hasTo($this->student->email)
                && $mail->type === 'past_due';
        });
    }

    // ── Tuition installment — already paid ───────────────────────────────────

    /** @test */
    public function does_not_send_reminder_for_already_paid_tuition_installment(): void
    {
        Mail::fake();
        [$tuition, $installment] = $this->createTuitionFeeWithInstallment(now()->addDays(7)->toDateString());

        Transaction::create([
            'user_id'           => $this->student->id,
            'fee_id'            => $tuition->id,
            'installment_id'    => $installment->id,
            'amount'            => 50000,
            'date'              => now()->toDateString(),
            'status'            => 'completed',
            'kkiapay_reference' => 'REF-TUI-PAID',
            'phone_number'      => '97000000',
        ]);

        $this->artisan('fees:send-reminders')->assertSuccessful();

        Mail::assertNotSent(FeeReminderMail::class);
    }

    // ── Student without accepted registration ─────────────────────────────────

    /** @test */
    public function does_not_send_reminder_to_student_without_accepted_registration(): void
    {
        Mail::fake();

        $otherStudent = User::factory()->create(['verified' => true]);
        $otherStudent->assignRole('parent_student');

        // This student has no accepted registration — create the fee but no registration for them
        $this->createGeneralFee(now()->addDays(7)->toDateString());

        // Only the student with an accepted registration should receive the email
        $this->artisan('fees:send-reminders')->assertSuccessful();

        Mail::assertSent(FeeReminderMail::class, 1); // only the setUp student
        Mail::assertNotSent(FeeReminderMail::class, function ($mail) use ($otherStudent) {
            return $mail->hasTo($otherStudent->email);
        });
    }

    // ── Cron HTTP endpoint ────────────────────────────────────────────────────

    /** @test */
    public function cron_endpoint_returns_403_without_token(): void
    {
        $this->get('/cron/send-fee-reminders')
            ->assertStatus(403);
    }

    /** @test */
    public function cron_endpoint_returns_403_with_wrong_token(): void
    {
        config(['app.cron_secret' => 'correct-secret']);

        $this->get('/cron/send-fee-reminders?token=wrong-secret')
            ->assertStatus(403);
    }

    /** @test */
    public function cron_endpoint_returns_ok_with_correct_token(): void
    {
        Mail::fake();
        config(['app.cron_secret' => 'correct-secret']);

        $this->get('/cron/send-fee-reminders?token=correct-secret')
            ->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    /** @test */
    public function cron_endpoint_triggers_reminder_emails(): void
    {
        Mail::fake();
        config(['app.cron_secret' => 'test-secret']);

        $this->createGeneralFee(now()->addDays(7)->toDateString());

        $this->get('/cron/send-fee-reminders?token=test-secret')
            ->assertStatus(200)
            ->assertJson(['status' => 'ok']);

        Mail::assertSent(FeeReminderMail::class);
    }
}