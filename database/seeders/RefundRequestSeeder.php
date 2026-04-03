<?php

namespace Database\Seeders;

use App\Models\RefundRequest;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class RefundRequestSeeder extends Seeder
{
    public function run(): void
    {
        $alice = User::where('email', 'alice.student@schoolfees.com')->first();
        $bob   = User::where('email', 'bob.scholar@schoolfees.com')->first();

        $aliceTui1  = Transaction::where('kkiapay_reference', 'ALICE-TUI-2025-001')->first();
        $aliceSport = Transaction::where('kkiapay_reference', 'ALICE-GEN-2025-001')->first();
        $bobReg     = Transaction::where('kkiapay_reference', 'BOB-REG-2025-001')->first();
        $bobTui2    = Transaction::where('kkiapay_reference', 'BOB-TUI-2025-002')->first();

        // Alice — installment 1 refund (pending, awaiting staff review)
        RefundRequest::create([
            'transaction_id' => $aliceTui1->id,
            'user_id'        => $alice->id,
            'reason'         => 'Double paiement effectué par erreur. Le même versement a été débité deux fois depuis mon compte mobile money.',
            'status'         => 'pending',
            'notes'          => null,
        ]);

        // Alice — sports fee refund (accepted by accountant)
        RefundRequest::create([
            'transaction_id' => $aliceSport->id,
            'user_id'        => $alice->id,
            'reason'         => "Activité annulée par l'établissement en cours d'année scolaire.",
            'status'         => 'accepted',
            'notes'          => "Remboursement approuvé — l'activité sportive a été annulée par l'administration. Montant intégral restitué.",
        ]);

        // Bob — registration fee refund (refused by admin)
        RefundRequest::create([
            'transaction_id' => $bobReg->id,
            'user_id'        => $bob->id,
            'reason'         => "Inscription annulée avant la validation officielle du dossier par la scolarité.",
            'status'         => 'refused',
            'notes'          => "Les frais d'inscription ne sont pas remboursables une fois le dossier transmis au service de scolarité et traité.",
        ]);

        // Bob — installment 2 refund (accepted — transaction already marked refunded)
        RefundRequest::create([
            'transaction_id' => $bobTui2->id,
            'user_id'        => $bob->id,
            'reason'         => "Changement d'établissement scolaire en cours d'année suite à un déménagement familial.",
            'status'         => 'accepted',
            'notes'          => 'Remboursement partiel approuvé au prorata du trimestre non effectué. Dossier transmis au service comptable.',
        ]);
    }
}
