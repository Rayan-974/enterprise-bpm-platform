<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            OrganizationSeeder::class,
            UserSeeder::class,
            WorkflowSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
