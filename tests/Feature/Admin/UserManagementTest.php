<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
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
    public function admin_creation_auto_sets_email_as_verified(): void
    {
        $page   = new CreateUser();
        $method = (new ReflectionMethod($page, 'mutateFormDataBeforeCreate'));
        $method->setAccessible(true);

        $result = $method->invoke($page, ['name' => 'Jean', 'email' => 'jean@example.com']);

        $this->assertTrue($result['verified']);
        $this->assertNotNull($result['email_verified_at']);
    }

    /** @test */
    public function admin_creation_generates_a_hashed_password(): void
    {
        $page   = new CreateUser();
        $method = (new ReflectionMethod($page, 'mutateFormDataBeforeCreate'));
        $method->setAccessible(true);

        $result = $method->invoke($page, ['name' => 'Jean']);

        $this->assertNotEmpty($result['password']);
        $this->assertStringStartsWith('$2', $result['password']); // bcrypt hash
    }

    /** @test */
    public function admin_creation_sends_welcome_email_to_the_new_user(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'jean@example.com']);
        $user->assignRole('parent_student');

        $page    = new CreateUser();
        $reflect = new ReflectionClass($page);

        $passwordProp = $reflect->getProperty('rawPassword');
        $passwordProp->setAccessible(true);
        $passwordProp->setValue($page, 'generated_password');

        $mock = \Mockery::mock(CreateUser::class)->makePartial();
        $passwordProp->setValue($mock, 'generated_password');
        $mock->shouldReceive('getRecord')->andReturn($user);

        $afterCreate = $reflect->getMethod('afterCreate');
        $afterCreate->setAccessible(true);
        $afterCreate->invoke($mock);

        Mail::assertSent(WelcomeUserMail::class, fn ($mail) =>
            $mail->hasTo('jean@example.com')
        );
    }

    /** @test */
    public function admin_creation_welcome_email_contains_the_raw_password(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'jean@example.com']);
        $user->assignRole('parent_student');

        $page    = new CreateUser();
        $reflect = new ReflectionClass($page);

        $mock = \Mockery::mock(CreateUser::class)->makePartial();
        $passwordProp = $reflect->getProperty('rawPassword');
        $passwordProp->setAccessible(true);
        $passwordProp->setValue($mock, 'secret_pass_123');
        $mock->shouldReceive('getRecord')->andReturn($user);

        $afterCreate = $reflect->getMethod('afterCreate');
        $afterCreate->setAccessible(true);
        $afterCreate->invoke($mock);

        Mail::assertSent(WelcomeUserMail::class, function ($mail) {
            return $mail->rawPassword === 'secret_pass_123';
        });
    }
}