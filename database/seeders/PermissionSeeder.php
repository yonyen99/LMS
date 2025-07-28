<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Role management
            'create-role',
            'edit-role',
            'delete-role',
            'view-role',

            // User management
            'create-user',
            'edit-user',
            'delete-user',

            // Department management
            'create-department',
            'edit-department',
            'delete-department',

            // Request (LMS)
            'create-request',
            'edit-request',
            'delete-request',
            'view-request',
            'cancel-request',

            // Dashboard
            'view-dashboard',

            'create-non-working-day',
            'edit-non-working-day',
            'delete-non-working-day',
            'view-non-working-day'
        ];

        

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }
}