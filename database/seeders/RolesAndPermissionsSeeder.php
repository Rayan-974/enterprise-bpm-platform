<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'workflows.view',
            'workflows.create',
            'workflows.edit',
            'workflows.delete',
            'tasks.view',
            'tasks.approve',
            'tasks.reject',
            'tasks.delegate',
            'forms.manage',
            'analytics.view',
            'audit.view',
            'users.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm);
        }

        // Roles
        $superAdmin = Role::findOrCreate('Super Admin');
        $superAdmin->givePermissionTo(Permission::all());

        $deptAdmin = Role::findOrCreate('Department Admin');
        $deptAdmin->givePermissionTo([
            'workflows.view', 'workflows.create', 'workflows.edit',
            'tasks.view', 'tasks.approve', 'tasks.reject', 'tasks.delegate',
            'forms.manage', 'analytics.view', 'audit.view'
        ]);

        $manager = Role::findOrCreate('Manager');
        $manager->givePermissionTo([
            'workflows.view', 'workflows.create',
            'tasks.view', 'tasks.approve', 'tasks.reject', 'tasks.delegate',
            'analytics.view'
        ]);

        $employee = Role::findOrCreate('Employee');
        $employee->givePermissionTo([
            'workflows.view', 'workflows.create',
            'tasks.view'
        ]);

        $auditor = Role::findOrCreate('Auditor');
        $auditor->givePermissionTo([
            'workflows.view', 'tasks.view', 'analytics.view', 'audit.view'
        ]);
    }
}
