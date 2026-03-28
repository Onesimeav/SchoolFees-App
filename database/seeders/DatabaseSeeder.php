<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call seeders in correct order
        $this->call([
            RoleAndPermissionSeeder::class,
            TestUserSeeder::class,
            GradeSeeder::class,
            FeeSeeder::class,
            ClassRegistrationSeeder::class,
            TransactionSeeder::class,
            RefundRequestSeeder::class,
            ReceiptSeeder::class,
        ]);
    }
}
