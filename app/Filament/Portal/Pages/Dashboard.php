<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Pages\Auth\VerifyEmail;
use App\Models\ClassRegistration;
use App\Models\Fee;
use App\Models\Installment;
use App\Models\Transaction;
use App\Models\TuitionFee;
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
                ->with('transaction.fee', 'grade')
                ->latest()
                ->first();

            $academicYear = $accepted?->transaction?->fee?->academic_year;

            $unpaid   = 0;
            $feeIds   = collect();

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

            // Build student dashboard data
            $studentData = [
                'user'              => auth()->user(),
                'registration'      => $accepted,
                'academicYear'      => $academicYear,
                'totalInstallments' => 0,
                'paidInstallments'  => 0,
                'tuitionAmount'     => 0,
                'tuitionPaid'       => 0,
                'generalFeeTotal'   => $feeIds->count(),
                'generalFeePaid'    => $feeIds->count() - $unpaid,
                'lastTransaction'   => null,
            ];

            if ($accepted && $academicYear) {
                $tuitionFee = TuitionFee::where('grade_id', $accepted->grade_id)
                    ->where('academic_year', $academicYear)
                    ->first();

                if ($tuitionFee) {
                    $studentData['totalInstallments'] = $tuitionFee->number_of_installments ?? 0;
                    $studentData['tuitionAmount']     = $tuitionFee->total_amount;

                    $paidInstallmentIds = Transaction::where('user_id', auth()->id())
                        ->where('status', 'completed')
                        ->whereNotNull('installment_id')
                        ->pluck('installment_id');

                    $studentData['paidInstallments'] = Installment::where('tuition_fee_id', $tuitionFee->id)
                        ->whereIn('id', $paidInstallmentIds)
                        ->count();

                    $studentData['tuitionPaid'] = Transaction::where('user_id', auth()->id())
                        ->where('status', 'completed')
                        ->whereNotNull('installment_id')
                        ->whereHas('installment', fn ($q) => $q->where('tuition_fee_id', $tuitionFee->id))
                        ->sum('amount');
                }
            }

            $studentData['lastTransaction'] = Transaction::where('user_id', auth()->id())
                ->where('status', 'completed')
                ->latest('date')
                ->first();

            $components[] = View::make('filament.portal.pages.dashboard-student')
                ->viewData($studentData);
        }

        return $schema->components([
            ...$components,
            $this->getWidgetsContentComponent(),
        ]);
    }
}