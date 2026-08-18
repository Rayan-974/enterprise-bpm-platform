<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        $hr = Department::where('code', 'HR')->first();
        $fin = Department::where('code', 'FIN')->first();
        $proc = Department::where('code', 'PROC')->first();
        $legal = Department::where('code', 'LEGAL')->first();
        $it = Department::where('code', 'IT')->first();

        // 1. Super Admin
        $admin = User::updateOrCreate(['email' => 'admin@enterprise.com'], [
            'name' => 'Alexander Vance (Super Admin)',
            'password' => $password,
            'department_id' => $it?->id,
            'country_code' => 'US',
            'employee_id' => 'EMP-0001',
            'is_active' => true,
        ]);
        $admin->assignRole('Super Admin');

        // 2. Department Admins / Managers
        $hrManager = User::updateOrCreate(['email' => 'hr.head@enterprise.com'], [
            'name' => 'Sarah Connor (HR Head)',
            'password' => $password,
            'department_id' => $hr?->id,
            'country_code' => 'US',
            'employee_id' => 'EMP-0002',
            'is_active' => true,
        ]);
        $hrManager->assignRole('Department Admin');
        if ($hr) {
            $hr->update(['head_user_id' => $hrManager->id]);
        }

        $finManager = User::updateOrCreate(['email' => 'finance.head@enterprise.com'], [
            'name' => 'Marcus Sterling (CFO & Finance Head)',
            'password' => $password,
            'department_id' => $fin?->id,
            'country_code' => 'UK',
            'employee_id' => 'EMP-0003',
            'is_active' => true,
        ]);
        $finManager->assignRole('Manager');
        if ($fin) {
            $fin->update(['head_user_id' => $finManager->id]);
        }

        $procManager = User::updateOrCreate(['email' => 'procurement.head@enterprise.com'], [
            'name' => 'Elena Rostova (Procurement Lead)',
            'password' => $password,
            'department_id' => $proc?->id,
            'country_code' => 'DE',
            'employee_id' => 'EMP-0004',
            'is_active' => true,
        ]);
        $procManager->assignRole('Manager');
        if ($proc) {
            $proc->update(['head_user_id' => $procManager->id]);
        }

        $legalManager = User::updateOrCreate(['email' => 'legal.head@enterprise.com'], [
            'name' => 'Jonathan Reed (Legal Counsel)',
            'password' => $password,
            'department_id' => $legal?->id,
            'country_code' => 'SG',
            'employee_id' => 'EMP-0005',
            'is_active' => true,
        ]);
        $legalManager->assignRole('Manager');
        if ($legal) {
            $legal->update(['head_user_id' => $legalManager->id]);
        }

        // 3. Regular Employees
        $emp1 = User::updateOrCreate(['email' => 'john.doe@enterprise.com'], [
            'name' => 'John Doe (Senior Software Engineer)',
            'password' => $password,
            'department_id' => $it?->id,
            'country_code' => 'US',
            'manager_id' => $admin->id,
            'employee_id' => 'EMP-0101',
            'is_active' => true,
        ]);
        $emp1->assignRole('Employee');

        $emp2 = User::updateOrCreate(['email' => 'maria.garcia@enterprise.com'], [
            'name' => 'Maria Garcia (Marketing Specialist)',
            'password' => $password,
            'department_id' => $hr?->id,
            'country_code' => 'FR',
            'manager_id' => $hrManager->id,
            'employee_id' => 'EMP-0102',
            'is_active' => true,
        ]);
        $emp2->assignRole('Employee');

        // 4. Auditor
        $auditor = User::updateOrCreate(['email' => 'auditor@enterprise.com'], [
            'name' => 'David Kim (Global Risk Auditor)',
            'password' => $password,
            'department_id' => $fin?->id,
            'country_code' => 'JP',
            'employee_id' => 'EMP-0999',
            'is_active' => true,
        ]);
        $auditor->assignRole('Auditor');
    }
}
