<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $mod = Role::create(['name' => 'mod']);

        // Create permissions
        $permissions = [
            'view_movies',
            'create_movies',
            'edit_movies',
            'delete_movies',
            'manage_users',
            'manage_settings'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());
        $mod->givePermissionTo(['view_movies', 'create_movies', 'edit_movies']);
    }
}
