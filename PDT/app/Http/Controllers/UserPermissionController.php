<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPermissionController extends Controller
{
    /**
     * Show the form for editing user permissions.
     */
    public function edit($userId)
    {
        // Only admins can manage user permissions
        $current = Auth::user();
        if (!$current || !$current->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Find user with permissions loaded
        $user = User::with('permissions', 'roles')->findOrFail($userId);
        
        // Get all available permissions
        $permissions = Permission::all();
        
        // Get user's current permission IDs as an array
        $userPermissions = $user->permissions->pluck('id')->toArray();
        
        return view('users.permissions.edit', compact('user', 'permissions', 'userPermissions'));
    }

    /**
     * Update user permissions.
     */
    public function update(Request $request, $userId)
    {
        // Only admins can manage user permissions
        $current = Auth::user();
        if (!$current || !$current->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($userId);
        
        // Validate
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);
        
        // Sync permissions (add new, remove unchecked)
        $user->permissions()->sync($request->input('permissions', []));
        
        // Determine redirect based on user type
        if ($user->teacher) {
            return redirect()->route('teacher.index')
                ->with('success', 'ការផ្តល់សិទ្ធិបានជោគជ័យសម្រាប់ ' . $user->name . '!');
        } elseif ($user->student) {
            return redirect()->route('students.index')
                ->with('success', 'ការផ្តល់សិទ្ធិបានជោគជ័យសម្រាប់ ' . $user->name . '!');
        }
        
        return redirect()->back()
            ->with('success', 'ការផ្តល់សិទ្ធិបានជោគជ័យ!');
    }
}