<?php

namespace Database\Seeders;

use App\Models\GeneralFee;
use App\Models\Grade;
use App\Models\Installment;
use App\Models\RegistrationFee;
use App\Models\TuitionFee;
use Illuminate\Database\Seeder;

class FeeSeeder extends Seeder
{
    public function run(): void
    {
        $grade6    = Grade::where('name', '6ème')->first();
        $grade3    = Grade::where('name', '3ème')->first();
        $termGrade = Grade::where('name', 'Terminale')->first();

        // ── Registration Fees ────────────────────────────────────────────────

        RegistrationFee::create([
            'type'          => 'App\\Models\\RegistrationFee',
            'title'         => "Frais d'inscription — 6ème",
            'academic_year' => '2025-2026',
            'total_amount'  => 35000,
            'due_before'    => '2025-10-15',
            'grade_id'      => $grade6->id,
            'description'   => "Frais d'inscription pour l'entrée en 6ème.",
        ]);

        RegistrationFee::create([
            'type'          => 'App\\Models\\RegistrationFee',
            'title'         => "Frais d'inscription — 3ème",
            'academic_year' => '2025-2026',
            'total_amount'  => 35000,
            'due_before'    => '2025-10-15',
            'grade_id'      => $grade3->id,
            'description'   => "Frais d'inscription pour l'entrée en 3ème.",
        ]);

        RegistrationFee::create([
            'type'          => 'App\\Models\\RegistrationFee',
            'title'         => "Frais d'inscription — Terminale",
            'academic_year' => '2025-2026',
            'total_amount'  => 40000,
            'due_before'    => '2025-10-15',
            'grade_id'      => $termGrade->id,
            'description'   => "Frais d'inscription pour l'entrée en Terminale.",
        ]);

        // ── Tuition Fees ─────────────────────────────────────────────────────

        $tuition6 = TuitionFee::create([
            'type'                   => 'App\\Models\\TuitionFee',
            'title'                  => 'Scolarité 6ème 2025-2026',
            'academic_year'          => '2025-2026',
            'total_amount'           => 450000,
            'number_of_installments' => 3,
            'late_fine_per_week'     => 5000,
            'grade_id'               => $grade6->id,
            'description'            => 'Frais de scolarité annuels — 6ème, répartis en 3 versements.',
        ]);

        Installment::create(['tuition_fee_id' => $tuition6->id, 'number' => 1, 'amount' => 200000, 'due_date' => '2025-10-31']);
        Installment::create(['tuition_fee_id' => $tuition6->id, 'number' => 2, 'amount' => 150000, 'due_date' => '2026-01-31']);
        Installment::create(['tuition_fee_id' => $tuition6->id, 'number' => 3, 'amount' => 100000, 'due_date' => '2026-04-30']);

        $tuition3 = TuitionFee::create([
            'type'                   => 'App\\Models\\TuitionFee',
            'title'                  => 'Scolarité 3ème 2025-2026',
            'academic_year'          => '2025-2026',
            'total_amount'           => 480000,
            'number_of_installments' => 3,
            'late_fine_per_week'     => 5000,
            'grade_id'               => $grade3->id,
            'description'            => 'Frais de scolarité annuels — 3ème, répartis en 3 versements.',
        ]);

        Installment::create(['tuition_fee_id' => $tuition3->id, 'number' => 1, 'amount' => 210000, 'due_date' => '2025-10-31']);
        Installment::create(['tuition_fee_id' => $tuition3->id, 'number' => 2, 'amount' => 160000, 'due_date' => '2026-01-31']);
        Installment::create(['tuition_fee_id' => $tuition3->id, 'number' => 3, 'amount' => 110000, 'due_date' => '2026-04-30']);

        $tuitionTerm = TuitionFee::create([
            'type'                   => 'App\\Models\\TuitionFee',
            'title'                  => 'Scolarité Terminale 2025-2026',
            'academic_year'          => '2025-2026',
            'total_amount'           => 600000,
            'number_of_installments' => 3,
            'late_fine_per_week'     => 7500,
            'grade_id'               => $termGrade->id,
            'description'            => 'Frais de scolarité annuels — Terminale, répartis en 3 versements.',
        ]);

        Installment::create(['tuition_fee_id' => $tuitionTerm->id, 'number' => 1, 'amount' => 250000, 'due_date' => '2025-10-31']);
        Installment::create(['tuition_fee_id' => $tuitionTerm->id, 'number' => 2, 'amount' => 200000, 'due_date' => '2026-01-31']);
        Installment::create(['tuition_fee_id' => $tuitionTerm->id, 'number' => 3, 'amount' => 150000, 'due_date' => '2026-04-30']);

        // ── General Fees ─────────────────────────────────────────────────────

        GeneralFee::create([
            'type'          => 'App\\Models\\GeneralFee',
            'title'         => 'Activités sportives 2025-2026',
            'academic_year' => '2025-2026',
            'total_amount'  => 15000,
            'due_before'    => '2025-11-30',
            'required'      => true,
            'description'   => 'Cotisation annuelle pour la participation aux activités sportives scolaires.',
        ]);

        GeneralFee::create([
            'type'          => 'App\\Models\\GeneralFee',
            'title'         => 'Sortie pédagogique 2025-2026',
            'academic_year' => '2025-2026',
            'total_amount'  => 25000,
            'due_before'    => '2026-02-28',
            'required'      => false,
            'description'   => 'Participation à la sortie pédagogique de fin de premier semestre.',
        ]);
    }
}
