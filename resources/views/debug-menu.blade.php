@extends('layouts.app')

@section('title', 'Debug Menu')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h3>🔧 DEBUG MENU - SIDEBAR KOSONG</h3>
        </div>
        <div class="card-body">
            @php
                $user = auth()->user();
                $canViewDashboard = \App\Services\MenuPermissionService::canView('dashboard');
                $baseMenu = [
                    ['text' => 'Dashboard', 'menu_code' => 'dashboard'],
                    ['text' => 'Master Data', 'menu_code' => 'master_data'],
                    ['text' => 'Transaksi', 'menu_code' => 'transaksi'],
                ];
            @endphp

            <h5 class="mb-3">1️⃣ AUTH STATUS</h5>
            <div class="alert {{ auth()->check() ? 'alert-success' : 'alert-danger' }}">
                <strong>Authenticated:</strong> {{ auth()->check() ? '✅ YES' : '❌ NO' }}<br>
                @if(auth()->check())
                    <strong>User:</strong> {{ $user->id }} - {{ $user->name }}<br>
                    <strong>Role ID:</strong> {{ $user->role_id ?? 'NULL ❌' }}<br>
                    <strong>Role Object:</strong> {{ $user->role ? $user->role->name : 'NULL ❌' }}
                @endif
            </div>

            <h5 class="mb-3">2️⃣ DATABASE PERMISSIONS</h5>
            @php
                $roleId = $user->role_id ?? null;
                $allPerms = \App\Models\RolePermission::where('role_id', $roleId)->get();
            @endphp
            <div class="alert {{ $allPerms->count() > 0 ? 'alert-success' : 'alert-danger' }}">
                <strong>Role ID {{ $roleId }}:</strong> {{ $allPerms->count() }} permissions found
                @if($allPerms->count() == 0)
                    <br>❌ <strong>MASALAH:</strong> Tidak ada permission untuk role ini!
                @endif
            </div>

            @if($allPerms->count() > 0)
                <table class="table table-sm table-striped">
                    <tr>
                        <th>Menu Code</th>
                        <th>Can View</th>
                    </tr>
                    @foreach($allPerms->take(5) as $p)
                        <tr>
                            <td><code>{{ $p->menu_code }}</code></td>
                            <td>{{ $p->can_view ? '✅' : '❌' }}</td>
                        </tr>
                    @endforeach
                    @if($allPerms->count() > 5)
                        <tr><td colspan="2" class="text-muted">... dan {{ $allPerms->count() - 5 }} lagi</td></tr>
                    @endif
                </table>
            @endif

            <h5 class="mb-3">3️⃣ TEST PERMISSION CHECK</h5>
            <div class="alert alert-info">
                <code>MenuPermissionService::canView('dashboard')</code> = 
                <strong>{{ $canViewDashboard ? '✅ TRUE' : '❌ FALSE' }}</strong>
            </div>

            <h5 class="mb-3">4️⃣ MENU RESULT</h5>
            @php
                $menu = \App\Services\AdminLteMenuBuilder::buildMenu();
            @endphp
            <div class="alert {{ count($menu) > 0 ? 'alert-success' : 'alert-danger' }}">
                <strong>Menu Items:</strong> {{ count($menu) }} items
                @if(count($menu) == 0)
                    <br>❌ <strong>MASALAH:</strong> buildMenu() kosong!
                @endif
            </div>

            @if(count($menu) > 0)
                <ul class="list-group">
                    @foreach($menu as $item)
                        <li class="list-group-item">
                            {{ $item['text'] }}
                            @if(isset($item['submenu']))
                                ({{ count($item['submenu']) }} sub)
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif

            <hr class="my-4">

            <h5 class="bg-warning p-2">⚡ DIAGNOSIS</h5>
            @if(!auth()->check())
                <div class="alert alert-danger">❌ User tidak terlogin. Login dulu!</div>
            @elseif(!$user->role_id)
                <div class="alert alert-danger">❌ User tidak punya role_id. Hubungi admin.</div>
            @elseif($allPerms->count() == 0)
                <div class="alert alert-danger">❌ Database permissions kosong. Jalankan seeder:</div>
                <code>php artisan db:seed --class=RolePermissionSeeder</code>
            @elseif(!$canViewDashboard)
                <div class="alert alert-danger">❌ MenuPermissionService::canView() return false (ada bug)</div>
            @elseif(count($menu) == 0)
                <div class="alert alert-danger">❌ buildMenu() kosong meski permission ada (ada bug di filter)</div>
            @else
                <div class="alert alert-success">✅ Semuanya terlihat OK. Cek browser cache atau settings menu.</div>
            @endif

        </div>
    </div>
</div>
@endsection
