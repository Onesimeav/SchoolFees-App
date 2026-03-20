<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Pages\Auth\VerifyEmail;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function content(Schema $schema): Schema
    {
        $components = [];

        if (auth()->check() && ! auth()->user()->verified) {
            $components[] = View::make('filament.portal.pages.verification-warning')
                ->viewData(['verifyUrl' => VerifyEmail::getUrl()]);
        }

        return $schema->components([
            ...$components,
            $this->getWidgetsContentComponent(),
        ]);
    }
}