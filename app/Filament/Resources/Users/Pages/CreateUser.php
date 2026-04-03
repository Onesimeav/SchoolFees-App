<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Mail\WelcomeUserMail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected string $rawPassword = '';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->rawPassword = Str::password(12);

        $data['password'] = Hash::make($this->rawPassword);
        $data['verified'] = true;
        $data['email_verified_at'] = now();

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        Mail::to($record->email)->queue(new WelcomeUserMail($record, $this->rawPassword));
    }
}