<?php

namespace Database\Seeders;

use App\Models\ClassRegistration;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Installment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $alice = User::where('email', 'alice.student@schoolfees.com')->first();
        $bob   = User::where('email', 'bob.scholar@schoolfees.com')->first();

        $grade6    = Grade::where('name', '6ème')->first();
        $grade3    = Grade::where('name', '3ème')->first();
        $termGrade = Grade::where('name', 'Terminale')->first();

        $regFee6    = Fee::where('title', "Frais d'inscription — 6ème")->first();
        $regFee3    = Fee::where('title', "Frais d'inscription — 3ème")->first();
        $regFeeTerm = Fee::where('title', "Frais d'inscription — Terminale")->first();
        $tuition6   = Fee::where('title', 'Scolarité 6ème 2025-2026')->first();
        $tuition3   = Fee::where('title', 'Scolarité 3ème 2025-2026')->first();
        $sport      = Fee::where('title', 'Activités sportives 2025-2026')->first();
        $outing     = Fee::where('title', 'Sortie pédagogique 2025-2026')->first();

        $tuition6Installments = Installment::where('tuition_fee_id', $tuition6->id)->orderBy('due_date')->get();
        $tuition3Installments = Installment::where('tuition_fee_id', $tuition3->id)->orderBy('due_date')->get();

        // ── Alice's transactions ──────────────────────────────────────────────

        // Registration fee — 6ème (completed)
        $aliceRegTx = Transaction::create([
            'user_id'           => $alice->id,
            'fee_id'            => $regFee6->id,
            'amount'            => 35000,
            'date'              => '2025-09-15',
            'status'            => 'completed',
            'kkiapay_reference' => 'ALICE-REG-2025-001',
            'phone_number'      => $alice->phone_number,
        ]);

        // Tuition 6ème — installment 1 (completed)
        Transaction::create([
            'user_id'           => $alice->id,
            'fee_id'            => $tuition6->id,
            'installment_id'    => $tuition6Installments[0]->id,
            'amount'            => 200000,
            'date'              => '2025-10-20',
            'status'            => 'completed',
            'kkiapay_reference' => 'ALICE-TUI-2025-001',
            'phone_number'      => $alice->phone_number,
        ]);

        // Tuition 6ème — installment 2 (completed)
        Transaction::create([
            'user_id'           => $alice->id,
            'fee_id'            => $tuition6->id,
            'installment_id'    => $tuition6Installments[1]->id,
            'amount'            => 150000,
            'date'              => '2026-01-18',
            'status'            => 'completed',
            'kkiapay_reference' => 'ALICE-TUI-2025-002',
            'phone_number'      => $alice->phone_number,
        ]);

        // Tuition 6ème — installment 3 (pending)
        Transaction::create([
            'user_id'        => $alice->id,
            'fee_id'         => $tuition6->id,
            'installment_id' => $tuition6Installments[2]->id,
            'amount'         => 100000,
            'date'           => '2026-04-01',
            'status'         => 'pending',
            'phone_number'   => $alice->phone_number,
        ]);

        // General fee — Activités sportives (completed)
        Transaction::create([
            'user_id'           => $alice->id,
            'fee_id'            => $sport->id,
            'amount'            => 15000,
            'date'              => '2025-11-05',
            'status'            => 'completed',
            'kkiapay_reference' => 'ALICE-GEN-2025-001',
            'phone_number'      => $alice->phone_number,
        ]);

        // General fee — Sortie pédagogique (failed)
        Transaction::create([
            'user_id'      => $alice->id,
            'fee_id'       => $outing->id,
            'amount'       => 25000,
            'date'         => '2026-02-10',
            'status'       => 'failed',
            'phone_number' => $alice->phone_number,
        ]);

        // Registration fee — Terminale (completed, but registration was refused)
        $aliceTermRegTx = Transaction::create([
            'user_id'           => $alice->id,
            'fee_id'            => $regFeeTerm->id,
            'amount'            => 40000,
            'date'              => '2025-09-10',
            'status'            => 'completed',
            'kkiapay_reference' => 'ALICE-REG-TERM-2025-001',
            'phone_number'      => $alice->phone_number,
        ]);

        // ── Bob's transactions ────────────────────────────────────────────────

        // Registration fee — 3ème (completed)
        $bobRegTx = Transaction::create([
            'user_id'           => $bob->id,
            'fee_id'            => $regFee3->id,
            'amount'            => 35000,
            'date'              => '2025-09-18',
            'status'            => 'completed',
            'kkiapay_reference' => 'BOB-REG-2025-001',
            'phone_number'      => $bob->phone_number,
        ]);

        // Tuition 3ème — installment 1 (completed)
        Transaction::create([
            'user_id'           => $bob->id,
            'fee_id'            => $tuition3->id,
            'installment_id'    => $tuition3Installments[0]->id,
            'amount'            => 210000,
            'date'              => '2025-10-22',
            'status'            => 'completed',
            'kkiapay_reference' => 'BOB-TUI-2025-001',
            'phone_number'      => $bob->phone_number,
        ]);

        // Tuition 3ème — installment 2 (refunded)
        Transaction::create([
            'user_id'           => $bob->id,
            'fee_id'            => $tuition3->id,
            'installment_id'    => $tuition3Installments[1]->id,
            'amount'            => 160000,
            'date'              => '2026-01-20',
            'status'            => 'refunded',
            'kkiapay_reference' => 'BOB-TUI-2025-002',
            'phone_number'      => $bob->phone_number,
        ]);

        // Tuition 3ème — installment 3 (pending)
        Transaction::create([
            'user_id'        => $bob->id,
            'fee_id'         => $tuition3->id,
            'installment_id' => $tuition3Installments[2]->id,
            'amount'         => 110000,
            'date'           => '2026-04-05',
            'status'         => 'pending',
            'phone_number'   => $bob->phone_number,
        ]);

        // General fee — Activités sportives (completed)
        Transaction::create([
            'user_id'           => $bob->id,
            'fee_id'            => $sport->id,
            'amount'            => 15000,
            'date'              => '2025-11-08',
            'status'            => 'completed',
            'kkiapay_reference' => 'BOB-GEN-2025-001',
            'phone_number'      => $bob->phone_number,
        ]);

        // Registration fee — 6ème (completed, but registration is pending review)
        $bobReg6Tx = Transaction::create([
            'user_id'           => $bob->id,
            'fee_id'            => $regFee6->id,
            'amount'            => 35000,
            'date'              => '2025-09-20',
            'status'            => 'completed',
            'kkiapay_reference' => 'BOB-REG-6-2025-001',
            'phone_number'      => $bob->phone_number,
        ]);

        // ── Back-fill transaction_id on all class registrations ───────────────

        // Accepted registrations
        ClassRegistration::where('user_id', $alice->id)
            ->where('grade_id', $grade6->id)
            ->update(['transaction_id' => $aliceRegTx->id]);

        ClassRegistration::where('user_id', $bob->id)
            ->where('grade_id', $grade3->id)
            ->update(['transaction_id' => $bobRegTx->id]);

        // Refused registration — Alice Terminale
        ClassRegistration::where('user_id', $alice->id)
            ->where('grade_id', $termGrade->id)
            ->update(['transaction_id' => $aliceTermRegTx->id]);

        // Pending registration — Bob 6ème
        ClassRegistration::where('user_id', $bob->id)
            ->where('grade_id', $grade6->id)
            ->update(['transaction_id' => $bobReg6Tx->id]);
    }
}
