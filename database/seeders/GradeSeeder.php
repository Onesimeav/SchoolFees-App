<?php

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            ['name' => 'CM2',      'description' => 'Cours Moyen 2ème année'],
            ['name' => '6ème',     'description' => 'Sixième'],
            ['name' => '3ème',     'description' => 'Troisième'],
            ['name' => 'Seconde',  'description' => 'Seconde générale'],
            ['name' => 'Terminale','description' => 'Terminale générale'],
        ];

        foreach ($grades as $grade) {
            Grade::create($grade);
        }
    }
}
