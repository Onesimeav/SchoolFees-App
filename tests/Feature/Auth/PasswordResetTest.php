<?php

namespace Tests\Feature\Auth;

use App\Filament\Portal\Pages\Auth\ForgotPassword as PortalForgotPassword;
use App\Filament\Portal\Pages\Auth\ResetPassword as PortalResetPassword;
use App\Filament\Staff\Pages\Auth\ForgotPassword as StaffForgotPassword;
use App\Filament\Staff\Pages\Auth\ResetPassword as StaffResetPassword;
use App\Mail\PasswordResetMail;
use App\Models\User;
use App\Models\VerificationCode;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PasswordResetTest extends TestCase
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

    // -------------------------------------------------------------------------
    // Portal — ForgotPassword
    // -------------------------------------------------------------------------

    /** @test */
    public function portal_reset_creates_otp_and_sends_email_for_valid_parent_student(): void
    {
        Mail::fake();
        Filament::setCurrentPanel(Filament::getPanel('portal'));

        $user = User::factory()->create(['email' => 'student@example.com']);
        $user->assignRole('parent_student');

        Livewire::test(PortalForgotPassword::class)
            ->set('data.email', 'student@example.com')
            ->call('request');

        $this->assertDatabaseHas('verification_codes', [
            'email' => 'student@example.com',
            'type'  => 'password_reset',
        ]);

        Mail::assertSent(PasswordResetMail::class, fn ($mail) =>
            $mail->hasTo('student@example.com')
        );
    }

    /** @test */
    public function portal_reset_shows_success_but_sends_no_email_for_unknown_address(): void
    {
        Mail::fake();
        Filament::setCurrentPanel(Filament::getPanel('portal'));

        Livewire::test(PortalForgotPassword::class)
            ->set('data.email', 'nobody@example.com')
            ->call('request');

        Mail::assertNothingSent();
        $this->assertDatabaseCount('verification_codes', 0);
    }

    /** @test */
    public function portal_reset_does_not_send_email_for_staff_role(): void
    {
        Mail::fake();
        Filament::setCurrentPanel(Filament::getPanel('portal'));

        $staff = User::factory()->create(['email' => 'staff@example.com']);
        $staff->assignRole('accountant');

        Livewire::test(PortalForgotPassword::class)
            ->set('data.email', 'staff@example.com')
            ->call('request');

        Mail::assertNothingSent();
    }

    // -------------------------------------------------------------------------
    // Portal — ResetPassword
    // -------------------------------------------------------------------------

    /** @test */
    public function portal_reset_page_redirects_to_forgot_password_without_session(): void
    {
        $this->get('/portal/auth/reset-password')
            ->assertRedirect('/portal/password-reset/request');
    }

    /** @test */
    public function portal_valid_otp_resets_password_and_logs_user_in(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('portal'));

        $user = User::factory()->create(['email' => 'student@example.com']);
        $user->assignRole('parent_student');

        VerificationCode::create([
            'email'      => 'student@example.com',
            'code'       => '123456',
            'type'       => 'password_reset',
            'expires_at' => now()->addMinutes(15),
        ]);

        session()->put('portal_reset_email', 'student@example.com');

        Livewire::test(PortalResetPassword::class)
            ->set('data.code', '123456')
            ->set('data.password', 'NewPassword123!')
            ->set('data.passwordConfirmation', 'NewPassword123!')
            ->call('resetPassword');

        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
        $this->assertAuthenticated();
        $this->assertDatabaseCount('verification_codes', 0);
    }

    /** @test */
    public function portal_expired_otp_returns_a_validation_error(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('portal'));

        $user = User::factory()->create(['email' => 'student@example.com']);
        $user->assignRole('parent_student');

        VerificationCode::create([
            'email'      => 'student@example.com',
            'code'       => '123456',
            'type'       => 'password_reset',
            'expires_at' => now()->subMinutes(1),
        ]);

        session()->put('portal_reset_email', 'student@example.com');

        Livewire::test(PortalResetPassword::class)
            ->set('data.code', '123456')
            ->set('data.password', 'NewPassword123!')
            ->set('data.passwordConfirmation', 'NewPassword123!')
            ->call('resetPassword')
            ->assertHasErrors(['data.code']);
    }

    /** @test */
    public function portal_wrong_otp_returns_a_validation_error(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('portal'));

        $user = User::factory()->create(['email' => 'student@example.com']);
        $user->assignRole('parent_student');

        VerificationCode::create([
            'email'      => 'student@example.com',
            'code'       => '123456',
            'type'       => 'password_reset',
            'expires_at' => now()->addMinutes(15),
        ]);

        session()->put('portal_reset_email', 'student@example.com');

        Livewire::test(PortalResetPassword::class)
            ->set('data.code', '999999')
            ->set('data.password', 'NewPassword123!')
            ->set('data.passwordConfirmation', 'NewPassword123!')
            ->call('resetPassword')
            ->assertHasErrors(['data.code']);
    }

    // -------------------------------------------------------------------------
    // Staff — ForgotPassword
    // -------------------------------------------------------------------------

    /** @test */
    public function staff_reset_creates_otp_and_sends_email_for_valid_staff_user(): void
    {
        Mail::fake();
        Filament::setCurrentPanel(Filament::getPanel('staff'));

        foreach (['accountant', 'secretary', 'employee'] as $role) {
            $user = User::factory()->create(['email' => "{$role}@example.com"]);
            $user->assignRole($role);

            Livewire::test(StaffForgotPassword::class)
                ->set('data.email', "{$role}@example.com")
                ->call('request');

            $this->assertDatabaseHas('verification_codes', [
                'email' => "{$role}@example.com",
                'type'  => 'password_reset',
            ]);

            Mail::assertSent(PasswordResetMail::class, fn ($mail) =>
                $mail->hasTo("{$role}@example.com")
            );
        }
    }

    /** @test */
    public function staff_reset_does_not_send_email_for_parent_student_role(): void
    {
        Mail::fake();
        Filament::setCurrentPanel(Filament::getPanel('staff'));

        $user = User::factory()->create(['email' => 'student@example.com']);
        $user->assignRole('parent_student');

        Livewire::test(StaffForgotPassword::class)
            ->set('data.email', 'student@example.com')
            ->call('request');

        Mail::assertNothingSent();
    }

    /** @test */
    public function staff_reset_page_redirects_to_forgot_password_without_session(): void
    {
        $this->get('/staff/auth/reset-password')
            ->assertRedirect('/staff/password-reset/request');
    }

    /** @test */
    public function staff_valid_otp_resets_password_and_logs_user_in(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('staff'));

        $user = User::factory()->create(['email' => 'accountant@example.com']);
        $user->assignRole('accountant');

        VerificationCode::create([
            'email'      => 'accountant@example.com',
            'code'       => '654321',
            'type'       => 'password_reset',
            'expires_at' => now()->addMinutes(15),
        ]);

        session()->put('staff_reset_email', 'accountant@example.com');

        Livewire::test(StaffResetPassword::class)
            ->set('data.code', '654321')
            ->set('data.password', 'NewPassword123!')
            ->set('data.passwordConfirmation', 'NewPassword123!')
            ->call('resetPassword');

        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
        $this->assertAuthenticated();
        $this->assertDatabaseCount('verification_codes', 0);
    }
}