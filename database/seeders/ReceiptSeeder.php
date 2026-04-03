<?php

namespace Database\Seeders;

use App\Models\ClassRegistration;
use App\Models\Installment;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ReceiptSeeder extends Seeder
{
    public function run(): void
    {
        // ── Registration receipts ─────────────────────────────────────────────

        Transaction::whereNotNull('kkiapay_reference')
            ->whereHas('fee', fn ($q) => $q->where('type', 'App\Models\RegistrationFee'))
            ->with(['user', 'fee'])
            ->each(function (Transaction $tx) {
                $reg = ClassRegistration::where('transaction_id', $tx->id)
                    ->with(['user', 'grade', 'transaction.fee'])
                    ->first();

                if (! $reg) {
                    return;
                }

                $pdf  = Pdf::loadView('pdf.registration-receipt', ['registration' => $reg]);
                $path = 'receipts/' . $tx->user_id . '/' . $reg->id . '.pdf';
                Storage::disk('supabase')->put($path, $pdf->output());
            });

        // ── Tuition receipts ──────────────────────────────────────────────────

        Transaction::whereNotNull('kkiapay_reference')
            ->whereHas('fee', fn ($q) => $q->where('type', 'App\Models\TuitionFee'))
            ->with(['user', 'fee.grade', 'installment'])
            ->each(function (Transaction $tx) {
                $installments = Installment::where('id', $tx->installment_id)->get()->keyBy('id');

                $pdf  = Pdf::loadView('pdf.tuition-receipt', [
                    'transactions' => collect([$tx]),
                    'installments' => $installments,
                    'fee'          => $tx->fee,
                    'user'         => $tx->user,
                ]);
                $path = 'receipts/' . $tx->user_id . '/tuition-' . $tx->kkiapay_reference . '.pdf';
                Storage::disk('supabase')->put($path, $pdf->output());
            });

        // ── General fee receipts ──────────────────────────────────────────────

        Transaction::whereNotNull('kkiapay_reference')
            ->whereHas('fee', fn ($q) => $q->where('type', 'App\Models\GeneralFee'))
            ->with(['user', 'fee'])
            ->each(function (Transaction $tx) {
                $pdf  = Pdf::loadView('pdf.general-fee-receipt', [
                    'transaction' => $tx,
                    'fee'         => $tx->fee,
                    'user'        => $tx->user,
                ]);
                $path = 'receipts/' . $tx->user_id . '/general-fee-' . $tx->kkiapay_reference . '.pdf';
                Storage::disk('supabase')->put($path, $pdf->output());
            });
    }
}
