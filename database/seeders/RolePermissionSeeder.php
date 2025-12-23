<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // All available menus
        $menus = [
            'dashboard',
            'master_data',
            'kost',
            'rooms',
            'addons',
            'consumers',
            'transaksi',
            'occupancies',
            'billings',
            'payments',
            'addon_transactions',
            'laporan',
            'reports_occupancy',
            'reports_finance',
            'manajemen',
            'users',
            'roles',
            'role_permissions',
            'settings',
        ];

        // Get all roles
        $roles = Role::all();

        foreach ($roles as $role) {
            // Clear existing permissions for this role
            RolePermission::where('role_id', $role->id)->delete();

            // For owner/admin roles - grant all permissions
            if (in_array(strtolower($role->name), ['owner', 'admin'])) {
                foreach ($menus as $menu) {
                    RolePermission::create([
                        'role_id' => $role->id,
                        'menu_code' => $menu,
                        'can_view' => true,
                        'can_create' => true,
                        'can_update' => true,
                        'can_delete' => true,
                    ]);
                }
            } 
            // For other roles - grant view-only for limited menus
            else {
                $limitedMenus = [
                    'dashboard',
                    'occupancies',
                    'billings',
                    'payments',
                ];
                foreach ($limitedMenus as $menu) {
                    RolePermission::create([
                        'role_id' => $role->id,
                        'menu_code' => $menu,
                        'can_view' => true,
                        'can_create' => false,
                        'can_update' => false,
                        'can_delete' => false,
                    ]);
                }
            }
        }

        $this->command->info('Role permissions seeded successfully!');
    }
}
