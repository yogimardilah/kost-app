<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    // Define all available menus
    private function getAvailableMenus()
    {
        return [
            'dashboard' => 'Dashboard',
            'master_data' => 'Master Data',
            'kost' => 'Data Kost',
            'rooms' => 'Kamar',
            'addons' => 'Room Addons',
            'consumers' => 'Penyewa',
            'transaksi' => 'Transaksi',
            'occupancies' => 'Occupancies',
            'billings' => 'Billings',
            'payments' => 'Payments',
            'addon_transactions' => 'Transaksi Addon',
            'purchases' => 'Pembelian/Ops',
            'laporan' => 'Laporan',
            'reports_occupancy' => 'Laporan Hunian',
            'reports_finance' => 'Laporan Keuangan',
            'manajemen' => 'Manajemen',
            'users' => 'Users',
            'roles' => 'Roles',
            'role_permissions' => 'Role Permissions',
            'settings' => 'Settings',
        ];
    }

    public function index()
    {
        $roles = Role::orderBy('name')->get();
        $menus = $this->getAvailableMenus();
        
        return view('role_permissions.index', compact('roles', 'menus'));
    }

    public function edit(Role $role)
    {
        $menus = $this->getAvailableMenus();
        $permissions = RolePermission::where('role_id', $role->id)->get()->keyBy('menu_code');
        
        return view('role_permissions.edit', compact('role', 'menus', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $menus = $this->getAvailableMenus();
        
        // Clear existing permissions
        RolePermission::where('role_id', $role->id)->delete();
        
        // Create new permissions from request
        foreach ($menus as $code => $label) {
            $data = [
                'role_id' => $role->id,
                'menu_code' => $code,
                'can_view' => $request->has("menu_{$code}_view") ? 1 : 0,
                'can_create' => $request->has("menu_{$code}_create") ? 1 : 0,
                'can_update' => $request->has("menu_{$code}_update") ? 1 : 0,
                'can_delete' => $request->has("menu_{$code}_delete") ? 1 : 0,
            ];
            
            RolePermission::create($data);
        }
        
        return redirect()->route('role-permissions.index')
            ->with('success', 'Permissions untuk role ' . $role->name . ' berhasil diperbarui');
    }

    public function resetPermissions()
    {
        $menus = $this->getAvailableMenus();
        $roles = Role::all();

        foreach ($roles as $role) {
            RolePermission::where('role_id', $role->id)->delete();

            if (in_array(strtolower($role->name), ['owner', 'admin'])) {
                foreach ($menus as $code => $label) {
                    RolePermission::create([
                        'role_id' => $role->id,
                        'menu_code' => $code,
                        'can_view' => true,
                        'can_create' => true,
                        'can_update' => true,
                        'can_delete' => true,
                    ]);
                }
            } else {
                $limitedMenus = ['dashboard', 'occupancies', 'billings', 'payments'];
                foreach ($limitedMenus as $menuCode) {
                    RolePermission::create([
                        'role_id' => $role->id,
                        'menu_code' => $menuCode,
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
