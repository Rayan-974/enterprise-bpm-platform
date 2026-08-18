<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Department;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['code' => 'US', 'name' => 'United States', 'region' => 'North America'],
            ['code' => 'UK', 'name' => 'United Kingdom', 'region' => 'Europe'],
            ['code' => 'DE', 'name' => 'Germany', 'region' => 'Europe'],
            ['code' => 'FR', 'name' => 'France', 'region' => 'Europe'],
            ['code' => 'SG', 'name' => 'Singapore', 'region' => 'Asia Pacific'],
            ['code' => 'JP', 'name' => 'Japan', 'region' => 'Asia Pacific'],
            ['code' => 'AU', 'name' => 'Australia', 'region' => 'Asia Pacific'],
            ['code' => 'CA', 'name' => 'Canada', 'region' => 'North America'],
            ['code' => 'PK', 'name' => 'Pakistan', 'region' => 'South Asia'],
            ['code' => 'AE', 'name' => 'United Arab Emirates', 'region' => 'Middle East'],
            ['code' => 'BR', 'name' => 'Brazil', 'region' => 'South America'],
            ['code' => 'ZA', 'name' => 'South Africa', 'region' => 'Africa'],
        ];

        foreach ($countries as $c) {
            Country::updateOrCreate(['code' => $c['code']], $c);
        }

        $departments = [
            ['code' => 'EXEC', 'name' => 'Executive Office', 'description' => 'C-Suite leadership'],
            ['code' => 'HR', 'name' => 'Human Resources', 'description' => 'HR and talent management'],
            ['code' => 'FIN', 'name' => 'Finance & Accounting', 'description' => 'Financial operations & audit'],
            ['code' => 'PROC', 'name' => 'Procurement & Supply Chain', 'description' => 'Vendor management & purchases'],
            ['code' => 'LEGAL', 'name' => 'Legal & Compliance', 'description' => 'Legal risk & contracts'],
            ['code' => 'IT', 'name' => 'Information Technology', 'description' => 'IT infrastructure & security'],
            ['code' => 'OPS', 'name' => 'Global Operations', 'description' => 'Day to day operational management'],
            ['code' => 'MKT', 'name' => 'Global Marketing', 'description' => 'Brand and advertising'],
        ];

        foreach ($departments as $d) {
            Department::updateOrCreate(['code' => $d['code']], $d);
        }
    }
}
