<?php

namespace App\Services;

use App\Services\MenuPermissionService;

class AdminLteMenuBuilder
{
    /**
     * Build filtered menu based on user permissions
     */
    public static function buildMenu()
    {
        $baseMenu = [
            [
                'text' => 'Dashboard',
                'url'  => 'dashboard',
                'icon' => 'fas fa-home',
                'menu_code' => 'dashboard',
            ],

            // Master Data group
            [
                'text'    => 'Master Data',
                'icon'    => 'fas fa-database',
                'menu_code' => 'master_data',
                'submenu' => [
                    [ 'text' => 'Data Kost', 'url' => 'kost', 'icon' => 'fas fa-building', 'menu_code' => 'kost' ],
                    [ 'text' => 'Kamar', 'url' => 'rooms', 'icon' => 'fas fa-door-closed', 'menu_code' => 'rooms' ],
                    [ 'text' => 'Room Addons', 'url' => 'addons', 'icon' => 'fas fa-puzzle-piece', 'menu_code' => 'addons' ],
                    [ 'text' => 'Penyewa', 'url' => 'consumers', 'icon' => 'fas fa-users', 'menu_code' => 'consumers' ],
                ],
            ],

            // Transaksi group
            [
                'text'    => 'Transaksi',
                'icon'    => 'fas fa-exchange-alt',
                'menu_code' => 'transaksi',
                'submenu' => [
                    [ 'text' => 'Occupancies', 'url' => 'occupancies', 'icon' => 'fas fa-bed', 'menu_code' => 'occupancies' ],
                    [ 'text' => 'Billings', 'url' => 'billings', 'icon' => 'fas fa-file-invoice', 'menu_code' => 'billings' ],
                    [ 'text' => 'Payments', 'url' => 'payments', 'icon' => 'fas fa-credit-card', 'menu_code' => 'payments' ],
                ],
            ],

            // Laporan group
            [
                'text'    => 'Laporan',
                'icon'    => 'fas fa-chart-line',
                'menu_code' => 'laporan',
                'submenu' => [
                    [ 'text' => 'Laporan Hunian', 'url' => 'reports/occupancy', 'icon' => 'fas fa-bed', 'menu_code' => 'reports_occupancy' ],
                    [ 'text' => 'Laporan Keuangan', 'url' => 'reports/finance', 'icon' => 'fas fa-money-bill-wave', 'menu_code' => 'reports_finance' ],
                ],
            ],

            // Manajemen group
            [
                'text'    => 'Manajemen',
                'icon'    => 'fas fa-user-cog',
                'menu_code' => 'manajemen',
                'submenu' => [
                    [ 'text' => 'Users', 'url' => 'users', 'icon' => 'fas fa-users-cog', 'menu_code' => 'users' ],
                    [ 'text' => 'Roles', 'url' => 'roles', 'icon' => 'fas fa-user-shield', 'menu_code' => 'roles' ],
                    [ 'text' => 'Role Permissions', 'url' => 'role-permissions', 'icon' => 'fas fa-lock', 'menu_code' => 'role_permissions' ],
                ],
            ],
        ];

        return self::filterMenuByPermissions($baseMenu);
    }

    /**
     * Filter menu items based on user permissions
     */
    private static function filterMenuByPermissions($menu)
    {
        $filtered = [];

        foreach ($menu as $item) {
            $menuCode = $item['menu_code'] ?? null;

            // Check parent permission
            if ($menuCode && !MenuPermissionService::canView($menuCode)) {
                continue;
            }

            // Filter submenu items
            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $submenu = [];
                foreach ($item['submenu'] as $subitem) {
                    $subMenuCode = $subitem['menu_code'] ?? null;
                    if ($subMenuCode && MenuPermissionService::canView($subMenuCode)) {
                        $submenu[] = $subitem;
                    }
                }
                $item['submenu'] = $submenu;
                // Don't show parent if no accessible submenus
                if (empty($submenu)) {
                    continue;
                }
            }

            $filtered[] = $item;
        }

        return $filtered;
    }
}
