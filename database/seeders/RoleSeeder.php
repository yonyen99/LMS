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
        $admin = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $hr = Role::create(['name' => 'HR', 'guard_name' => 'web']);
        $manager = Role::create(['name' => 'Manager', 'guard_name' => 'web']);
        $employee = Role::create(['name' => 'Employee', 'guard_name' => 'web']);

        // Assign all permissions to Super Admin
        $admin->givePermissionTo(Permission::all());

        $hr->givePermissionTo(Permission::all());

        $manager->givePermissionTo([
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
            'view-user',
            'edit-user',
        ]);
    }
}