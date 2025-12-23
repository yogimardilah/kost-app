<?php

namespace App\Services;

use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;

class MenuPermissionService
{
    /**
     * Check if current user can view a menu
     */
    public static function canView($menuCode)
    {
        return self::hasPermission($menuCode, 'can_view');
    }

    /**
     * Check if current user can create in a menu
     */
    public static function canCreate($menuCode)
    {
        return self::hasPermission($menuCode, 'can_create');
    }

    /**
     * Check if current user can update in a menu
     */
    public static function canUpdate($menuCode)
    {
        return self::hasPermission($menuCode, 'can_update');
    }

    /**
     * Check if current user can delete in a menu
     */
    public static function canDelete($menuCode)
    {
        return self::hasPermission($menuCode, 'can_delete');
    }

    /**
     * Core permission check
     */
    private static function hasPermission($menuCode, $permissionField)
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        if (!$user->role_id) {
            return false;
        }

        $permission = RolePermission::where('role_id', $user->role_id)
            ->where('menu_code', $menuCode)
            ->first();

        return $permission && $permission->{$permissionField};
    }

    /**
     * Get all accessible menus for current user
     */
    public static function getAccessibleMenus()
    {
        if (!Auth::check() || !Auth::user()->role_id) {
            return [];
        }

        return RolePermission::where('role_id', Auth::user()->role_id)
            ->where('can_view', true)
            ->pluck('menu_code')
            ->toArray();
    }
}
