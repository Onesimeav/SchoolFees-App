<?php

namespace Database\Seeders;

use App\Models\ClassRegistration;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $alice = User::where('email', 'alice.student@schoolfees.com')->first();
        $bob   = User::where('email', 'bob.scholar@schoolfees.com')->first();

        $grade6    = Grade::where('name', '6ème')->first();
        $grade3    = Grade::where('name', '3ème')->first();
        $termGrade = Grade::where('name', 'Terminale')->first();

        // Alice — accepted into 6ème (transaction_id back-filled by TransactionSeeder)
        ClassRegistration::create([
            'user_id'  => $alice->id,
            'grade_id' => $grade6->id,
            'status'   => 'accepted',
            'notes'    => null,
        ]);

        // Alice — refused for Terminale
        ClassRegistration::create([
            'user_id'  => $alice->id,
            'grade_id' => $termGrade->id,
            'status'   => 'refused',
            'notes'    => 'Dossier incomplet — pièces justificatives manquantes.',
        ]);

        // Bob — accepted into 3ème (transaction_id back-filled by TransactionSeeder)
        ClassRegistration::create([
            'user_id'  => $bob->id,
            'grade_id' => $grade3->id,
            'status'   => 'accepted',
            'notes'    => null,
        ]);

        // Bob — pending for 6ème
        ClassRegistration::create([
            'user_id'  => $bob->id,
            'grade_id' => $grade6->id,
            'status'   => 'pending',
            'notes'    => 'En cours d\'examen.',
        ]);
    }
}
