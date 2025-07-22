<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Human Resources', 'description' => 'Handles hiring, employee relations, payroll, and leave.'],
            ['name' => 'Finance & Accounting', 'description' => 'Manages budgets, expenses, payroll, and tax compliance.'],
            ['name' => 'Sales', 'description' => 'Responsible for selling products or services and managing customer relationships.'],
            ['name' => 'Marketing', 'description' => 'Promotes the company, runs advertising campaigns, and manages branding.'],
            ['name' => 'Operations', 'description' => 'Oversees daily business activities and production workflows.'],
            ['name' => 'Customer Service', 'description' => 'Assists customers with inquiries, complaints, and support.'],
            ['name' => 'Information Technology', 'description' => 'Maintains IT infrastructure, systems, and network security.'],
            ['name' => 'Software Development', 'description' => 'Builds and maintains applications, websites, and software systems.'],
            ['name' => 'Product Management', 'description' => 'Defines product vision and coordinates development and launches.'],
            ['name' => 'UI/UX Design', 'description' => 'Designs user interfaces and improves user experience.'],
            ['name' => 'Quality Assurance', 'description' => 'Tests software to ensure quality and performance standards.'],
            ['name' => 'DevOps', 'description' => 'Manages deployments, automation, and infrastructure.'],
            ['name' => 'Cybersecurity', 'description' => 'Protects systems and data from cyber threats.'],
            ['name' => 'Legal', 'description' => 'Handles legal compliance, contracts, and company policies.'],
            ['name' => 'Administration', 'description' => 'Provides office management and administrative support.'],
            ['name' => 'Executive Management', 'description' => 'Leads company strategy and high-level decisions (e.g., CEO, CTO).'],
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
