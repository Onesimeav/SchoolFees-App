<?php

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

// ── Cron endpoint — triggered by cron-job.org once per day ──────────────────
Route::get('/cron/send-fee-reminders', function (Request $request) {
    if (! $request->query('token') || $request->query('token') !== config('app.cron_secret')) {
        abort(403, 'Unauthorized');
    }

    $exitCode = Artisan::call('fees:send-reminders');
    $output   = Artisan::output();

    return response()->json([
        'status' => $exitCode === 0 ? 'ok' : 'error',
        'output' => trim($output),
    ]);
});

Route::middleware(['auth'])->group(function () {
    // Admin / Staff — download any transaction receipt
    Route::get('/transactions/{transaction}/receipt', function (Transaction $transaction) {
        $tx = $transaction->load(['classRegistration', 'fee', 'installment']);

        if ($tx->classRegistration) {
            $path = 'receipts/' . $tx->user_id . '/' . $tx->classRegistration->id . '.pdf';
            $filename = 'recu-inscription-' . $tx->classRegistration->id . '.pdf';
        } elseif ($tx->installment_id && $tx->kkiapay_reference) {
            $path = 'receipts/' . $tx->user_id . '/tuition-' . $tx->kkiapay_reference . '.pdf';
            $filename = 'recu-scolarite.pdf';
        } elseif ($tx->fee_id && $tx->kkiapay_reference && $tx->fee?->type === 'App\Models\GeneralFee') {
            $path = 'receipts/' . $tx->user_id . '/general-fee-' . $tx->kkiapay_reference . '.pdf';
            $filename = 'recu-frais-general.pdf';
        } else {
            abort(404);
        }

        abort_if(! Storage::disk('supabase')->exists($path), 404);

        return response(Storage::disk('supabase')->get($path), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    })->name('transaction.receipt');


    Route::get('/refund-requests/{refundRequest}/receipt', function (\App\Models\RefundRequest $refundRequest) {
        $tx = $refundRequest->transaction;
        abort_if(! $tx || ! $tx->kkiapay_reference, 404);

        $path = 'receipts/' . $refundRequest->user_id . '/refund-' . $tx->kkiapay_reference . '.pdf';
        abort_if(! Storage::disk('supabase')->exists($path), 404);

        return response(Storage::disk('supabase')->get($path), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="recu-remboursement.pdf"',
        ]);
    })->name('refund.receipt');

    Route::get('/portal/transactions/{transaction}/receipt', function (Transaction $transaction) {
        abort_if($transaction->user_id !== auth()->id(), 403);

        $registration = $transaction->classRegistration;
        abort_if(! $registration, 404);

        $path = 'receipts/' . $transaction->user_id . '/' . $registration->id . '.pdf';

        abort_if(! Storage::disk('supabase')->exists($path), 404);

        $contents = Storage::disk('supabase')->get($path);

        return response($contents, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="recu-inscription-' . $registration->id . '.pdf"',
        ]);
    })->name('portal.transaction.receipt');

    Route::get('/portal/transactions/{transaction}/tuition-receipt', function (Transaction $transaction) {
        abort_if($transaction->user_id !== auth()->id(), 403);
        abort_if(! $transaction->kkiapay_reference, 404);

        $path = 'receipts/' . $transaction->user_id . '/tuition-' . $transaction->kkiapay_reference . '.pdf';

        abort_if(! Storage::disk('supabase')->exists($path), 404);

        $contents = Storage::disk('supabase')->get($path);

        return response($contents, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="recu-scolarite.pdf"',
        ]);
    })->name('portal.transaction.tuition-receipt');

    Route::get('/portal/transactions/{transaction}/general-fee-receipt', function (Transaction $transaction) {
        abort_if($transaction->user_id !== auth()->id(), 403);
        abort_if(! $transaction->kkiapay_reference, 404);

        $path = 'receipts/' . $transaction->user_id . '/general-fee-' . $transaction->kkiapay_reference . '.pdf';

        abort_if(! Storage::disk('supabase')->exists($path), 404);

        $contents = Storage::disk('supabase')->get($path);

        return response($contents, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="recu-frais-general.pdf"',
        ]);
    })->name('portal.transaction.general-fee-receipt');
});