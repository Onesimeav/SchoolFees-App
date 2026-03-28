<?php

namespace App\Console\Commands;

use App\Mail\FeeReminderMail;
use App\Models\ClassRegistration;
use App\Models\Fee;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendFeeReminders extends Command
{
    protected $signature = 'fees:send-reminders';

    protected $description = 'Envoie les rappels d\'échéance de frais aux élèves';

    public function handle(): int
    {
        $sent = 0;

        $registrations = ClassRegistration::where('status', 'accepted')
            ->with(['user', 'grade', 'transaction.fee'])
            ->get();

        foreach ($registrations as $registration) {
            $user         = $registration->user;
            $academicYear = $registration->transaction?->fee?->academic_year;

            if (! $user || ! $academicYear) {
                continue;
            }

            // ── General fees ────────────────────────────────────────────────
            $generalFees = \App\Models\Fee::where('type', 'App\Models\GeneralFee')
                ->where('grade_id', $registration->grade_id)
                ->where('academic_year', $academicYear)
                ->whereNotNull('due_before')
                ->get();

            $paidFeeIds = Transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereIn('fee_id', $generalFees->pluck('id'))
                ->pluck('fee_id');

            foreach ($generalFees->whereNotIn('id', $paidFeeIds) as $fee) {
                $daysUntilDue = (int) now()->startOfDay()->diffInDays($fee->due_before->startOfDay(), false);

                if ($daysUntilDue === 7 || $daysUntilDue === -1) {
                    Mail::to($user->email)->queue(new FeeReminderMail(
                        user:         $user,
                        feeTitle:     $fee->title,
                        academicYear: $academicYear,
                        gradeName:    $registration->grade->name,
                        amount:       (float) $fee->total_amount,
                        dueDate:      $fee->due_before->format('d/m/Y'),
                        type:         $daysUntilDue >= 0 ? 'near_due' : 'past_due',
                        portalUrl:    config('app.url') . '/portal/frais-generaux',
                    ));
                    $sent++;
                }
            }

            // ── Tuition installments ─────────────────────────────────────────
            $tuitionFee = Fee::where('type', 'App\Models\TuitionFee')
                ->where('grade_id', $registration->grade_id)
                ->where('academic_year', $academicYear)
                ->with('installments')
                ->first();

            if (! $tuitionFee) {
                continue;
            }

            $paidInstallmentIds = Transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('installment_id')
                ->whereHas('installment', fn ($q) => $q->where('tuition_fee_id', $tuitionFee->id))
                ->pluck('installment_id');

            foreach ($tuitionFee->installments->whereNotIn('id', $paidInstallmentIds) as $installment) {
                $daysUntilDue = (int) now()->startOfDay()->diffInDays($installment->due_date->startOfDay(), false);

                if ($daysUntilDue === 7 || $daysUntilDue === -1) {
                    Mail::to($user->email)->queue(new FeeReminderMail(
                        user:                $user,
                        feeTitle:            $tuitionFee->title,
                        academicYear:        $academicYear,
                        gradeName:           $registration->grade->name,
                        amount:              (float) $installment->amount,
                        dueDate:             $installment->due_date->format('d/m/Y'),
                        type:                $daysUntilDue >= 0 ? 'near_due' : 'past_due',
                        installmentNumber:   $installment->number,
                        portalUrl:           config('app.url') . '/portal/scolarite',
                    ));
                    $sent++;
                }
            }
        }

        $this->info("Rappels envoyés : {$sent}");

        return self::SUCCESS;
    }
}