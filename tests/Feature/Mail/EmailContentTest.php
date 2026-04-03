<?php

namespace Tests\Feature\Mail;

use App\Mail\EmailVerificationMail;
use App\Mail\PasswordResetMail;
use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmailContentTest extends TestCase
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

    /** @test */
    public function welcome_email_links_admin_to_the_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $html = (new WelcomeUserMail($user, 'password'))->render();

        $this->assertStringContainsString(url('/admin'), $html);
    }

    /** @test */
    public function welcome_email_links_parent_student_to_the_portal_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('parent_student');

        $html = (new WelcomeUserMail($user, 'password'))->render();

        $this->assertStringContainsString(url('/portal'), $html);
    }

    /** @test */
    public function welcome_email_links_staff_roles_to_the_staff_dashboard(): void
    {
        foreach (['accountant', 'secretary', 'employee'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $html = (new WelcomeUserMail($user, 'password'))->render();

            $this->assertStringContainsString(url('/staff'), $html,
                "Expected /staff link for role: {$role}"
            );
        }
    }

    /** @test */
    public function welcome_email_is_sent_to_the_correct_address(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'target@example.com']);
        $user->assignRole('parent_student');

        Mail::to($user->email)->send(new WelcomeUserMail($user, 'password'));

        Mail::assertSent(WelcomeUserMail::class, fn ($mail) =>
            $mail->hasTo('target@example.com')
        );
    }

    /** @test */
    public function password_reset_email_is_sent_to_the_correct_address(): void
    {
        Mail::fake();

        Mail::to('reset@example.com')->send(new PasswordResetMail('123456', 'Jean'));

        Mail::assertSent(PasswordResetMail::class, fn ($mail) =>
            $mail->hasTo('reset@example.com')
        );
    }

    /** @test */
    public function verification_email_is_sent_to_the_correct_address(): void
    {
        Mail::fake();

        Mail::to('verify@example.com')->send(new EmailVerificationMail('654321', 'Jean'));

        Mail::assertSent(EmailVerificationMail::class, fn ($mail) =>
            $mail->hasTo('verify@example.com')
        );
    }
}