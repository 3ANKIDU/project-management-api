<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'create project', 'view project', 'update project', 'delete project',
            'create task', 'view task', 'update task', 'assign task', 'delete task',
            'manage users', 'assign roles', 'view users',
            'manage notifications'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $superAdminRole = Role::create(['name' => 'super admin']);
        $admin = Role::create(['name' => 'admin']);
        $projectManager = Role::create(['name' => 'project_manager']);
        $developer = Role::create(['name' => 'developer']);
        $client = Role::create(['name' => 'client']);

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());
        $projectManager->givePermissionTo(['create project', 'view project', 'update project', 'create task', 'view task', 'assign task']);
        $developer->givePermissionTo(['view project', 'view task', 'update task']);
        $client->givePermissionTo(['view project', 'view task']);
    }
}
