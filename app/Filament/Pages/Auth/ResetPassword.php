<?php

namespace App\Filament\Pages\Auth;

use App\Models\VerificationCode;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Http\Middleware\Authenticate;
use Filament\Pages\Concerns\HasMaxWidth;
use Filament\Pages\Concerns\HasTopbar;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ResetPassword extends Page
{
    use HasMaxWidth;
    use HasTopbar;

    protected static ?string $slug = 'auth/reset-password';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament-panels::pages.simple';

    protected static string $layout = 'filament-panels::components.layout.simple';

    public function hasLogo(): bool
    {
        return true;
    }

    protected function getLayoutData(): array
    {
        return [
            'hasTopbar'       => $this->hasTopbar(),
            'maxContentWidth' => $maxContentWidth = $this->getMaxWidth() ?? $this->getMaxContentWidth(),
            'maxWidth'        => $maxContentWidth,
        ];
    }

    /**
     * @var string|array<string>
     */
    protected static string|array $withoutRouteMiddleware = [Authenticate::class];

    public ?array $data = [];

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            $this->redirect(Filament::getUrl());
            return;
        }

        if (! session()->has('admin_reset_email')) {
            $this->redirect(filament()->getRequestPasswordResetUrl());
            return;
        }

        $this->form->fill();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->label('Code de vérification')
                ->placeholder('000000')
                ->required()
                ->maxLength(6),

            TextInput::make('password')
                ->label('Nouveau mot de passe')
                ->password()
                ->revealable(filament()->arePasswordsRevealable())
                ->required()
                ->rule(Password::default())
                ->showAllValidationMessages()
                ->same('passwordConfirmation')
                ->validationAttribute('mot de passe'),

            TextInput::make('passwordConfirmation')
                ->label('Confirmer le mot de passe')
                ->password()
                ->revealable(filament()->arePasswordsRevealable())
                ->required()
                ->dehydrated(false),
        ]);
    }

    public function resetPassword(): void
    {
        $data = $this->form->getState();
        $email = session('admin_reset_email');

        $record = VerificationCode::where('email', $email)
            ->where('code', $data['code'])
            ->where('type', 'password_reset')
            ->first();

        if (! $record || $record->isExpired()) {
            $this->addError('data.code', 'Le code est invalide ou expiré.');
            return;
        }

        $record->delete();
        session()->forget('admin_reset_email');

        $user = \App\Models\User::where('email', $email)->first();
        $user->forceFill(['password' => Hash::make($data['password'])])->save();

        Auth::login($user);
        session()->regenerate();

        $this->redirect(Filament::getUrl());
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('resetPassword')
                ->footer([
                    Actions::make([
                        Action::make('resetPassword')
                            ->label('Réinitialiser le mot de passe')
                            ->submit('resetPassword'),
                    ])->fullWidth(),
                ]),
        ]);
    }

    public function getTitle(): string|Htmlable
    {
        return 'Réinitialisation du mot de passe';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Nouveau mot de passe';
    }
}
