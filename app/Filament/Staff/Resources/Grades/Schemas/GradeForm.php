<?php

namespace App\Filament\Staff\Resources\Grades\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GradeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nom de la classe')
                ->required()
                ->maxLength(255)
                ->placeholder('ex: CP, CE1, 6ème, Terminale…'),

            Textarea::make('description')
                ->label('Description')
                ->rows(3)
                ->maxLength(1000)
                ->columnSpanFull()
                ->placeholder('Description de la classe (optionnel)'),
        ])->columns(1);
    }
}