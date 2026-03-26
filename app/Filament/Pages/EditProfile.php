<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditProfile extends \Filament\Auth\Pages\EditProfile
{
    public static function getLabel(): string
    {
        return 'Profil';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Mon Profil';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Mon Profil';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations personnelles')->schema([
                TextInput::make('name')
                    ->label('Prénom')
                    ->required()
                    ->maxLength(255),

                TextInput::make('surname')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Adresse email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('phone_number')
                    ->label('Numéro de téléphone')
                    ->tel()
                    ->maxLength(255),
            ]),

            Section::make('Changer le mot de passe')->schema([
                $this->getCurrentPasswordFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]),
        ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $hashedPassword = $data['password'] ?? null;
        unset($data['password']);

        $record->update($data);

        if (filled($hashedPassword)) {
            $record->forceFill(['password' => $hashedPassword])->save();
        }

        return $record;
    }

    protected function getCurrentPasswordFormComponent(): Component
    {
        return TextInput::make('currentPassword')
            ->label('Mot de passe actuel')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->currentPassword(guard: \Filament\Facades\Filament::getAuthGuard())
            ->autocomplete('current-password')
            ->dehydrated(false)
            ->required(fn ($get) => filled($get('password')));
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Nouveau mot de passe')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->rule(Password::default())
            ->showAllValidationMessages()
            ->autocomplete('new-password')
            ->dehydrated(fn ($state): bool => filled($state))
            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
            ->live(debounce: 500)
            ->same('passwordConfirmation');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label('Confirmer le nouveau mot de passe')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('new-password')
            ->dehydrated(false)
            ->required(fn ($get) => filled($get('password')));
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Profil mis à jour avec succès';
    }
}
