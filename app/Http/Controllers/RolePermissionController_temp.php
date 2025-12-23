<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    // ... existing code ...

    public function resetPermissions()
    {
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
            'laporan',
            'reports_occupancy',
            'reports_finance',
            'manajemen',
            'users',
            'roles',
            'role_permissions',
            'settings',
        ];

        $roles = Role::all();

        foreach ($roles as $role) {
            RolePermission::where('role_id', $role->id)->delete();

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
            } else {
                $limitedMenus = ['dashboard', 'occupancies', 'billings', 'payments'];
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

        return redirect()->route('role-permissions.index')
            ->with('success', 'Semua role permissions telah di-reset ke default');
    }
}
