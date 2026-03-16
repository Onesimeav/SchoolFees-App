<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin user
        $admin = User::create([
            'name' => 'Admin',
            'surname' => 'User',
            'email' => 'admin@schoolfees.com',
            'phone_number' => '+2290123456789',
            'password' => Hash::make('password'),
            'verified' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create Accountant user
        $accountant = User::create([
            'name' => 'John',
            'surname' => 'Accountant',
            'email' => 'accountant@schoolfees.com',
            'phone_number' => '+2290100000089',
            'password' => Hash::make('password'),
            'verified' => true,
            'email_verified_at' => now(),
        ]);
        $accountant->assignRole('accountant');

        // Create Secretary user
        $secretary = User::create([
            'name' => 'Jane',
            'surname' => 'Secretary',
            'email' => 'secretary@schoolfees.com',
            'phone_number' => '+2290100000074',
            'password' => Hash::make('password'),
            'verified' => true,
            'email_verified_at' => now(),
        ]);
        $secretary->assignRole('secretary');

        // Create Employee user
        $employee = User::create([
            'name' => 'Mike',
            'surname' => 'Employee',
            'email' => 'employee@schoolfees.com',
            'phone_number' => '+2290100000009',
            'password' => Hash::make('password'),
            'verified' => true,
            'email_verified_at' => now(),
        ]);
        $employee->assignRole('employee');

        // Create Parent/Student users (with student data)
        $student1 = User::create([
            'name' => 'Alice',
            'surname' => 'Student',
            'email' => 'alice.student@schoolfees.com',
            'phone_number' => '+2290100000012',
            'password' => Hash::make('password'),
            'verified' => true,
            'email_verified_at' => now(),
            'matricule' => 'STU2024001',
            'classroom' => 'Grade 10A',
            'academic_year' => '2024-2025',
            'parent1_name' => 'Robert',
            'parent1_surname' => 'Student',
            'parent1_phone' => '+2290100000010',
            'parent2_name' => 'Maria',
            'parent2_surname' => 'Student',
            'parent2_phone' => '+2290100000041',
        ]);
        $student1->assignRole('parent_student');

        $student2 = User::create([
            'name' => 'Bob',
            'surname' => 'Scholar',
            'email' => 'bob.scholar@schoolfees.com',
            'phone_number' => '+2290100000093',
            'password' => Hash::make('password'),
            'verified' => true,
            'email_verified_at' => now(),
            'matricule' => 'STU2024002',
            'classroom' => 'Grade 11B',
            'academic_year' => '2024-2025',
            'parent1_name' => 'David',
            'parent1_surname' => 'Scholar',
            'parent1_phone' => '+2290100000026',
            'parent2_name' => 'Sarah',
            'parent2_surname' => 'Scholar',
            'parent2_phone' => '+2290100000027',
        ]);
        $student2->assignRole('parent_student');
    }
}