<?php

namespace App\Menu\Filters;

use App\Services\MenuPermissionService;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Illuminate\Support\Facades\Auth;

class RolePermissionFilter implements FilterInterface
{
    /**
     * Transforms a menu item. Add the active state to a menu item.
     *
     * @param  array  $item  A menu item
     * @return array The transformed menu item
     */
    public function transform($item)
    {
        // Skip jika user belum login
        if (!Auth::check()) {
            $item['restricted'] = true;
            return $item;
        }

        // Ambil menu_code dari item
        $menuCode = $item['menu_code'] ?? $this->extractMenuCode($item);

        // Jika tidak ada menu_code, izinkan (untuk menu tanpa permission check)
        if (!$menuCode) {
            return $item;
        }

        // Check permission
        if (!MenuPermissionService::canView($menuCode)) {
            $item['restricted'] = true;
        }

        return $item;
    }

    /**
     * Extract menu code from URL or text
     */
    private function extractMenuCode($item)
    {
        // Jika ada URL, extract dari URL
        if (isset($item['url'])) {
            $url = trim($item['url'], '/');
            // Ambil segment pertama sebagai menu_code
            $segments = explode('/', $url);
            return $segments[0] ?? null;
        }

        // Fallback ke text yang di-lowercase dan replace space dengan underscore
        if (isset($item['text'])) {
            return strtolower(str_replace(' ', '_', $item['text']));
        }

        return null;
    }
}
