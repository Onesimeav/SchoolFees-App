<?php

namespace Tests\Feature\Auth;

use App\Filament\Portal\Pages\Auth\Register;
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

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'parent_student']);

        Filament::setCurrentPanel(Filament::getPanel('portal'));
    }

    /** @test */
    public function registration_creates_user_with_correct_data(): void
    {
        Mail::fake();

        Livewire::test(Register::class)
            ->set('data', $this->validData())
            ->call('register');

        $this->assertDatabaseHas('users', [
            'name'    => 'Jean',
            'surname' => 'Dupont',
            'email'   => 'jean@example.com',
        ]);
    }

    /** @test */
    public function registration_assigns_parent_student_role(): void
    {
        Mail::fake();

        Livewire::test(Register::class)
            ->set('data', $this->validData())
            ->call('register');

        $user = User::where('email', 'jean@example.com')->first();
        $this->assertTrue($user->hasRole('parent_student'));
    }

    /** @test */
    public function registration_creates_an_email_verification_code(): void
    {
        Mail::fake();

        Livewire::test(Register::class)
            ->set('data', $this->validData())
            ->call('register');

        $this->assertDatabaseHas('verification_codes', [
            'email' => 'jean@example.com',
            'type'  => 'email_verification',
        ]);
    }

    /** @test */
    public function registration_sends_verification_email_to_the_new_user(): void
    {
        Mail::fake();

        Livewire::test(Register::class)
            ->set('data', $this->validData())
            ->call('register');

        Mail::assertSent(EmailVerificationMail::class, fn ($mail) =>
            $mail->hasTo('jean@example.com')
        );
    }

    /** @test */
    public function registration_rejects_a_duplicate_email(): void
    {
        Mail::fake();

        User::factory()->create(['email' => 'jean@example.com']);

        Livewire::test(Register::class)
            ->set('data', $this->validData())
            ->call('register')
            ->assertHasErrors(['data.email']);

        Mail::assertNothingSent();
    }

    /** @test */
    public function registration_logs_the_user_in(): void
    {
        Mail::fake();

        Livewire::test(Register::class)
            ->set('data', $this->validData())
            ->call('register');

        $this->assertAuthenticated();
    }

    /** @test */
    public function registration_redirects_to_the_email_verification_page(): void
    {
        Mail::fake();

        Livewire::test(Register::class)
            ->set('data', $this->validData())
            ->call('register')
            ->assertRedirect(VerifyEmail::getUrl());
    }

    private function validData(): array
    {
        return [
            'name'                => 'Jean',
            'surname'             => 'Dupont',
            'email'               => 'jean@example.com',
            'phone_number'        => '0600000000',
            'password'            => 'Password123!',
            'passwordConfirmation'=> 'Password123!',
            'parent1_name'        => 'Pierre',
            'parent1_surname'     => 'Dupont',
            'parent1_phone'       => '0600000001',
        ];
    }
}