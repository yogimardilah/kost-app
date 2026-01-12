<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Load role relationship if not already loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        $userRole = $user->role->nama ?? null;

        // Fallback: if role name doesn't match, also check by role_id
        // owner = role_id 1, admin = role_id 2
        $roleMapping = [
            'owner' => 1,
            'admin' => 2,
        ];

        $hasAccess = false;
        
        // Check by role name
        if ($userRole === $role) {
            $hasAccess = true;
        }
        
        // Check by role_id as fallback
        if (!$hasAccess && isset($roleMapping[$role]) && $user->role_id === $roleMapping[$role]) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'Akses ditolak. Hanya role ' . $role . ' yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
