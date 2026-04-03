<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Pages\Auth\VerifyEmail;
use App\Models\ClassRegistration;
use App\Models\Fee;
use App\Models\Transaction;
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

        if (auth()->check() && auth()->user()->verified) {
            $accepted = ClassRegistration::where('user_id', auth()->id())
                ->where('status', 'accepted')
                ->with('transaction.fee')
                ->latest()
                ->first();

            $academicYear = $accepted?->transaction?->fee?->academic_year;

            if ($accepted && $academicYear) {
                $feeIds = Fee::where('type', 'App\Models\GeneralFee')
                    ->where('grade_id', $accepted->grade_id)
                    ->where('academic_year', $academicYear)
                    ->pluck('id');

                if ($feeIds->isNotEmpty()) {
                    $paidFeeIds = Transaction::where('user_id', auth()->id())
                        ->where('status', 'completed')
                        ->whereIn('fee_id', $feeIds)
                        ->pluck('fee_id');

                    $unpaid = $feeIds->diff($paidFeeIds)->count();

                    if ($unpaid > 0) {
                        $components[] = View::make('filament.portal.pages.general-fees-alert')
                            ->viewData([
                                'unpaidCount'    => $unpaid,
                                'generalFeesUrl' => GeneralFees::getUrl(),
                            ]);
                    }
                }
            }
        }

        return $schema->components([
            ...$components,
            $this->getWidgetsContentComponent(),
        ]);
    }
}