<?php

namespace App\Filament\Portal\Pages\Auth;

use App\Mail\EmailVerificationMail;
use App\Models\VerificationCode;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Password;

class Register extends \Filament\Auth\Pages\Register
{
    public function getTitle(): string|Htmlable
    {
        return 'Inscription';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Créer votre compte';
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (! filament()->hasLogin()) {
            return null;
        }

        return new HtmlString(
            'Vous avez déjà un compte ? ' . $this->loginAction->toHtml()
        );
    }

    public function loginAction(): Action
    {
        return Action::make('login')
            ->link()
            ->label('Se connecter')
            ->url(filament()->getLoginUrl());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Informations personnelles')
                        ->schema([
                            TextInput::make('name')
                                ->label('Prénom')
                                ->required()
                                ->maxLength(255)
                                ->autofocus(),

                            TextInput::make('surname')
                                ->label('Nom')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label('Adresse email')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->unique($this->getUserModel()),

                            TextInput::make('phone_number')
                                ->label('Numéro de téléphone')
                                ->tel()
                                ->maxLength(255),

                            TextInput::make('password')
                                ->label('Mot de passe')
                                ->password()
                                ->revealable(filament()->arePasswordsRevealable())
                                ->required()
                                ->rule(Password::default())
                                ->showAllValidationMessages()
                                ->dehydrateStateUsing(fn ($state) => \Illuminate\Support\Facades\Hash::make($state))
                                ->same('passwordConfirmation')
                                ->validationAttribute('mot de passe'),

                            TextInput::make('passwordConfirmation')
                                ->label('Confirmer le mot de passe')
                                ->password()
                                ->revealable(filament()->arePasswordsRevealable())
                                ->required()
                                ->dehydrated(false),
                        ]),

                    Step::make('Informations des parents')
                        ->schema([
                            TextInput::make('parent1_name')
                                ->label('Prénom du parent 1')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('parent1_surname')
                                ->label('Nom du parent 1')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('parent1_phone')
                                ->label('Téléphone du parent 1')
                                ->tel()
                                ->maxLength(255),

                            TextInput::make('parent2_name')
                                ->label('Prénom du parent 2')
                                ->maxLength(255),

                            TextInput::make('parent2_surname')
                                ->label('Nom du parent 2')
                                ->maxLength(255),

                            TextInput::make('parent2_phone')
                                ->label('Téléphone du parent 2')
                                ->tel()
                                ->maxLength(255),
                        ]),
                ])
                ->nextAction(fn (Action $action) => $action->label('Suivant'))
                ->previousAction(fn (Action $action) => $action->label('Précédent'))
                ->submitAction(new HtmlString(
                    Blade::render('<x-filament::button wire:click="register" wire:loading.attr="disabled">S\'inscrire</x-filament::button>')
                )),
            ]);
    }

    /**
     * Remove the default form footer action (Sign up button outside the wizard).
     */
    protected function getFormActions(): array
    {
        return [];
    }

    public function register(): ?\Filament\Auth\Http\Responses\Contracts\RegistrationResponse
    {
        $data = $this->form->getState();

        $user = $this->handleRegistration($data);

        $user->assignRole('parent_student');

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        VerificationCode::where('email', $user->email)->where('type', 'email_verification')->delete();
        VerificationCode::create([
            'email'      => $user->email,
            'code'       => $otp,
            'type'       => 'email_verification',
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($user->email)->queue(new EmailVerificationMail($otp, $user->name));

        Filament::auth()->login($user);
        session()->regenerate();

        $this->redirect(VerifyEmail::getUrl());

        return null;
    }
}