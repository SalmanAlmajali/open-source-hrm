<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $softDeleteResources = [
            'employees',
        ];

        $resources = [
            'admins',
            'departments',
            'positions',
            'projects',
            'roles',
            'permissions',
        ];

        $customPermissions = [
            'view_dashboard_stats',
            'view_project_overview',
        ];

        $crudActions = [
            'view_any',     // Melihat daftar
            'view',         // Melihat detail
            'create',       // Membuat baru
            'update',       // Mengedit
            'delete',       // Menghapus
            'delete_any',   // Menghapus banyak sekaligus
        ];

        $softDeleteActions = [
            'restore',
            'force_delete',
        ];

        // 1. Generate untuk Resource Soft Deletes
        foreach ($softDeleteResources as $resource) {
            $actions = array_merge($crudActions, $softDeleteActions);

            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action}_{$resource}", 'guard_name' => 'web']);
            }
        }

        // 2. Generate untuk Resource Standar
        foreach ($resources as $resource) {
            foreach ($crudActions as $action) {
                Permission::firstOrCreate(['name' => "{$action}_{$resource}", 'guard_name' => 'web']);
            }
        }

        // 3. Generate Permission Custom
        foreach ($customPermissions as $permission) {
            Permission::firstOrCreate(['name' => "{$permission}", 'guard_name' => 'web']);
        }

        // SETUP ROLE & ASSIGMENT

        // Role 1: Super Admin
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // Role 2: HR Manager
        $hrManager = Role::firstOrCreate(['name' => 'hr_manager']);
        $hrManager->syncPermissions([
            // Pegawai & Struktur Organisasi
            'view_any_employees', 'view_employees', 'create_employees', 'update_employees', 'delete_employees',
            'view_any_departments', 'create_departments', 'update_departments',
            'view_any_positions', 'create_positions', 'update_positions',

            // Project
            'view_any_projects', 'view_projects', 'create_projects', 'update_projects', 'delete_projects',

            // Custom Permissions
            'view_dashboard_stats', 'view_project_overview',
        ]);

        // Role 3: Karyawan
        $staff = Role::firstOrCreate(['name' => 'karyawan']);
        $staff->syncPermissions([
            'view_project_overview', 
        ]);

        $this->command->info('Permissions generated successfully (Soft Deletes separated)!');
    }
}
