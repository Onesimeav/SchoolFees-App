<?php

namespace App\Filament\Portal\Pages\Auth;

use App\Mail\PasswordResetMail;
use App\Models\VerificationCode;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Mail;

class ForgotPassword extends \Filament\Auth\Pages\PasswordReset\RequestPasswordReset
{
    public function getTitle(): string|Htmlable
    {
        return 'Mot de passe oublié';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Réinitialiser votre mot de passe';
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

    public function request(): void
    {
        $data = $this->form->getState();
        $email = $data['email'];

        $user = \App\Models\User::where('email', $email)
            ->role('parent_student')
            ->first();

        // Always show the same message regardless of whether the email exists (security)
        Notification::make()
            ->title('Si un compte existe avec cette adresse, vous recevrez un email sous peu.')
            ->success()
            ->send();

        if (! $user) {
            $this->form->fill();
            return;
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        VerificationCode::where('email', $email)->where('type', 'password_reset')->delete();
        VerificationCode::create([
            'email'      => $email,
            'code'       => $otp,
            'type'       => 'password_reset',
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($email)->queue(new PasswordResetMail($otp, $user->name));

        session(['portal_reset_email' => $email]);

        $this->redirect(ResetPassword::getUrl());
    }
}