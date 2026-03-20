<?php

namespace App\Filament\Portal\Pages\Auth;

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class Login extends \Filament\Auth\Pages\Login
{
    public function getTitle(): string|Htmlable
    {
        return 'Connexion';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Connexion à votre espace';
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (! filament()->hasRegistration()) {
            return null;
        }

        return new HtmlString(
            "Vous n'avez pas encore de compte ? " . $this->registerAction->toHtml()
        );
    }

    public function registerAction(): Action
    {
        return Action::make('register')
            ->link()
            ->label("S'inscrire")
            ->url(filament()->getRegistrationUrl());
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Adresse email')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus();
    }

    protected function getPasswordFormComponent(): Component
    {
        $forgotUrl = filament()->hasPasswordReset()
            ? filament()->getRequestPasswordResetUrl()
            : null;

        return TextInput::make('password')
            ->label('Mot de passe')
            ->hint($forgotUrl ? new HtmlString(Blade::render('<x-filament::link href="' . $forgotUrl . '">Mot de passe oublié ?</x-filament::link>')) : null)
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required();
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label('Se souvenir de moi');
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Se connecter')
            ->submit('authenticate');
    }
}