<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Department;
use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            SuperAdminSeeder::class,
            DepartmentSeeder::class,
            LeaveTypeSeeder::class,
           

        ]);
    }
}
