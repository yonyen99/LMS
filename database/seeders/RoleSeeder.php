<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $hr = Role::create(['name' => 'HR', 'guard_name' => 'web']);
        $departmentManager = Role::create(['name' => 'Manager', 'guard_name' => 'web']);
        $employee = Role::create(['name' => 'Employee', 'guard_name' => 'web']);

        // Assign all permissions to Super Admin
        $superAdmin->givePermissionTo(Permission::all());

        $admin->givePermissionTo([
            'create-user',
            'edit-user',
            'delete-user',
            'create-department',
            'edit-department',
            'delete-department'
        ]);

        $hr->givePermissionTo([

            // Dashboard
            'view-dashboard',
            
            // User management
            'create-user',
            'edit-user',
            'delete-user',

            // Department management
            'create-department',
            'edit-department',
            'delete-department',

            // Role management
            'create-role',
            'edit-role',
            'delete-role',
            'view-role',

            // Request (LMS)
            'create-request',
            'edit-request',
            'delete-request',
            'view-request',
            'cancel-request',
        ]);

        $departmentManager->givePermissionTo([
            'create-user',
            'edit-user',
            'delete-user',
            'create-request',
            'edit-request',
            'delete-request',
            'view-request',
            'cancel-request',
        ]);

        $employee->givePermissionTo([
            'create-request',
            'edit-request',
            'delete-request',
            'view-request',
            'cancel-request',
        ]);
    }
}