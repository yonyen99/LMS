<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Super Admin']);
        $admin = Role::create(['name' => 'Admin']);
        $departmentManager = Role::create(['name' => 'Department Manager']);

        $admin->givePermissionTo([
            'create-user',
            'edit-user',
            'delete-user',
            'create-department',
            'edit-department',
            'delete-department'
        ]);

        $departmentManager->givePermissionTo([
            'create-department',
            'edit-department',
            'delete-department'
        ]);
    }
}
