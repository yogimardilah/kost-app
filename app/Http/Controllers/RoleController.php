<?php

namespace App\Http\Controllers;

use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('id','desc')->get();
        return view('roles.index', compact('roles'));
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }
}
