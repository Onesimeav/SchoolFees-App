<?php

namespace Tests\Feature\Auth;

use App\Filament\Portal\Pages\Auth\VerifyEmail;
use App\Mail\EmailVerificationMail;
use App\Models\User;
use App\Models\VerificationCode;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'parent_student']);

        Filament::setCurrentPanel(Filament::getPanel('portal'));
    }

    /** @test */
    public function unauthenticated_user_is_redirected_to_login(): void
    {
        Livewire::test(VerifyEmail::class)
            ->assertRedirect(Filament::getLoginUrl());
    }

    /** @test */
    public function already_verified_user_is_redirected_to_the_dashboard(): void
    {
        $user = User::factory()->create(['verified' => true]);
        $this->actingAs($user);

        Livewire::test(VerifyEmail::class)
            ->assertRedirect(Filament::getUrl());
    }

    /** @test */
    public function valid_otp_marks_the_user_as_verified(): void
    {
        $user = User::factory()->create(['verified' => false, 'email_verified_at' => null]);
        $this->actingAs($user);

        VerificationCode::create([
            'email'      => $user->email,
            'code'       => '112233',
            'type'       => 'email_verification',
            'expires_at' => now()->addMinutes(15),
        ]);

        Livewire::test(VerifyEmail::class)
            ->set('data.code', '112233')
            ->call('verify');

        $this->assertTrue($user->fresh()->verified);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    /** @test */
    public function valid_otp_deletes_the_verification_code(): void
    {
        $user = User::factory()->create(['verified' => false, 'email_verified_at' => null]);
        $this->actingAs($user);

        VerificationCode::create([
            'email'      => $user->email,
            'code'       => '112233',
            'type'       => 'email_verification',
            'expires_at' => now()->addMinutes(15),
        ]);

        Livewire::test(VerifyEmail::class)
            ->set('data.code', '112233')
            ->call('verify');

        $this->assertDatabaseCount('verification_codes', 0);
    }

    /** @test */
    public function valid_otp_redirects_to_the_dashboard(): void
    {
        $user = User::factory()->create(['verified' => false, 'email_verified_at' => null]);
        $this->actingAs($user);

        VerificationCode::create([
            'email'      => $user->email,
            'code'       => '112233',
            'type'       => 'email_verification',
            'expires_at' => now()->addMinutes(15),
        ]);

        Livewire::test(VerifyEmail::class)
            ->set('data.code', '112233')
            ->call('verify')
            ->assertRedirect(Filament::getUrl());
    }

    /** @test */
    public function expired_otp_returns_a_validation_error(): void
    {
        $user = User::factory()->create(['verified' => false, 'email_verified_at' => null]);
        $this->actingAs($user);

        VerificationCode::create([
            'email'      => $user->email,
            'code'       => '112233',
            'type'       => 'email_verification',
            'expires_at' => now()->subMinutes(1),
        ]);

        Livewire::test(VerifyEmail::class)
            ->set('data.code', '112233')
            ->call('verify')
            ->assertHasErrors(['data.code']);

        $this->assertFalse($user->fresh()->verified);
    }

    /** @test */
    public function wrong_otp_returns_a_validation_error(): void
    {
        $user = User::factory()->create(['verified' => false, 'email_verified_at' => null]);
        $this->actingAs($user);

        VerificationCode::create([
            'email'      => $user->email,
            'code'       => '112233',
            'type'       => 'email_verification',
            'expires_at' => now()->addMinutes(15),
        ]);

        Livewire::test(VerifyEmail::class)
            ->set('data.code', '999999')
            ->call('verify')
            ->assertHasErrors(['data.code']);

        $this->assertFalse($user->fresh()->verified);
    }

    /** @test */
    public function resend_generates_a_new_otp_and_sends_a_new_verification_email(): void
    {
        Mail::fake();

        $user = User::factory()->create(['verified' => false, 'email_verified_at' => null]);
        $this->actingAs($user);

        VerificationCode::create([
            'email'      => $user->email,
            'code'       => '000000',
            'type'       => 'email_verification',
            'expires_at' => now()->addMinutes(15),
        ]);

        Livewire::test(VerifyEmail::class)
            ->call('resend');

        // Old code deleted, new one created
        $this->assertDatabaseCount('verification_codes', 1);
        $this->assertDatabaseMissing('verification_codes', ['code' => '000000']);

        Mail::assertSent(EmailVerificationMail::class, fn ($mail) =>
            $mail->hasTo($user->email)
        );
    }
}