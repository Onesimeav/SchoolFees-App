<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
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
});