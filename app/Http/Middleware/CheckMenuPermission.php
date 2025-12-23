<?php

namespace App\Http\Middleware;

use App\Services\MenuPermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $menuCode): Response
    {
        if (!MenuPermissionService::canView($menuCode)) {
            abort(403, 'Anda tidak memiliki akses ke menu ini');
        }

        return $next($request);
    }
}
