<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
   public function index()
{
    $roles = Role::with('permissions')->get();
    return view('role.index', compact('roles'));
}
public function edit(Role $role)
{
    $permissions = Permission::all();
    $rolePermissions = $role->permissions->pluck('id')->toArray();

    return view('role.edit', compact('role', 'permissions', 'rolePermissions'));
}

public function update(Request $request, Role $role)
{
    $role->permissions()->sync($request->input('permissions', []));
    return redirect()->route('role.index')->with('success', 'Permissions updated successfully!');
}



}
