<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for granular control
        // User management permissions
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);

        // Fee management permissions
        Permission::create(['name' => 'view fees']);
        Permission::create(['name' => 'create fees']);
        Permission::create(['name' => 'edit fees']);
        Permission::create(['name' => 'delete fees']);
        Permission::create(['name' => 'approve fees']);

        // Transaction permissions
        Permission::create(['name' => 'view transactions']);
        Permission::create(['name' => 'process transactions']);
        Permission::create(['name' => 'refund transactions']);

        // Report permissions
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'export reports']);

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $accountantRole = Role::create(['name' => 'accountant']);
        $parentStudentRole = Role::create(['name' => 'parent_student']);
        $employeeRole = Role::create(['name' => 'employee']);
        $secretaryRole = Role::create(['name' => 'secretary']);

        // Assign permissions to roles
        // Admin gets all permissions
        $adminRole->givePermissionTo(Permission::all());

        // Accountant - financial operations
        $accountantRole->givePermissionTo([
            'view fees',
            'create fees',
            'edit fees',
            'view transactions',
            'process transactions',
            'refund transactions',
            'view reports',
            'export reports',
        ]);

        // Secretary - administrative operations
        $secretaryRole->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'view fees',
            'view transactions',
            'view reports',
        ]);

        // Employee - limited view access
        $employeeRole->givePermissionTo([
            'view users',
            'view fees',
            'view transactions',
        ]);

        // Parent/Student - view own data only (handled in policies)
        $parentStudentRole->givePermissionTo([
            'view fees',
            'view transactions',
        ]);
    }
}