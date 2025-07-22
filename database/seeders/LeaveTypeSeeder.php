<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            ['name' => 'Annual Leave', 'description' => 'Paid time off for vacation or personal rest.'],
            ['name' => 'Sick Leave', 'description' => 'Time off for illness or medical treatment.'],
            ['name' => 'Public Holiday', 'description' => 'Official holidays as declared by the government.'],
            ['name' => 'Maternity Leave', 'description' => 'Leave for mothers around childbirth.'],
            ['name' => 'Paternity Leave', 'description' => 'Leave for fathers or partners during/after childbirth.'],
            ['name' => 'Parental Leave', 'description' => 'Leave for child care after birth or adoption.'],
            ['name' => 'Bereavement Leave', 'description' => 'Leave due to the death of a close relative.'],
            ['name' => 'Unpaid Leave', 'description' => 'Leave without pay for personal reasons.'],
            ['name' => 'Study Leave', 'description' => 'Leave for exams or educational purposes.'],
            ['name' => 'Marriage Leave', 'description' => 'Leave for getting married.'],
            ['name' => 'Religious Leave', 'description' => 'Leave for religious holidays or practices.'],
            ['name' => 'Jury Duty Leave', 'description' => 'Leave to perform jury or civic service.'],
            ['name' => 'Sabbatical Leave', 'description' => 'Extended leave for personal or professional development.'],
        ];

        foreach ($leaveTypes as $type) {
            DB::table('leave_types')->insert([
                'name' => $type['name'],
                'description' => $type['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
