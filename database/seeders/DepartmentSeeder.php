<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'PALI Program',
                'description' => 'Focuses on leadership development, capacity building, and community empowerment initiatives.'
            ],
            [
                'name' => 'SACHAS Program',
                'description' => 'Provides support in health, social welfare, and awareness campaigns to improve community well-being.'
            ],
            [
                'name' => 'RITI Program',
                'description' => 'Drives research, innovation, training, and incubation activities to foster growth and development.'
            ],
            [
                'name' => 'MACOR Program',
                'description' => 'Works on monitoring, accountability, compliance, and organizational reporting for transparency and efficiency.'
            ],
        ];
        foreach ($departments as $department) {
            DB::table('departments')->insert([
                'name' => $department['name'],
                'description' => $department['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
