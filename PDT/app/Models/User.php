<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Permission;


use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    use Notifiable;
    //   use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relationship with roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
                    ->withTimestamps();
    }

    /**
     * Relationship with permissions (direct user permissions)
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user')
                    ->withTimestamps();
    }

    /**
     * Relationship with teacher profile
     */
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Relationship with student profile
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Check if user has a specific role
     * 
     * @param string|array $role
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            return $this->roles()->whereIn('name', $role)->exists();
        }
        
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Check if user has any of the given roles
     * 
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles)
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * Check if user has all of the given roles
     * 
     * @param array $roles
     * @return bool
     */
    public function hasAllRoles(array $roles)
    {
        return $this->roles()->whereIn('name', $roles)->count() === count($roles);
    }

    /**
     * Check if user has a specific permission
     * Can check through roles or direct permissions
     * 
     * @param string|array $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        // Check direct user permissions
        if (is_array($permission)) {
            if ($this->permissions()->whereIn('name', $permission)->exists()) {
                return true;
            }
        } else {
            if ($this->permissions()->where('name', $permission)->exists()) {
                return true;
            }
        }

        // Check permissions through roles
        foreach ($this->roles as $role) {
            if (is_array($permission)) {
                if ($role->permissions()->whereIn('name', $permission)->exists()) {
                    return true;
                }
            } else {
                if ($role->permissions()->where('name', $permission)->exists()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions
     * 
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission(array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if user has all of the given permissions
     * 
     * @param array $permissions
     * @return bool
     */
    public function hasAllPermissions(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get all permissions (direct + through roles)
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getAllPermissions()
    {
        // Get direct permissions
        $permissions = $this->permissions;

        // Get permissions through roles
        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }

        // Remove duplicates
        return $permissions->unique('id');
    }

    /**
     * Assign a role to the user
     * 
     * @param string|Role $role
     * @return void
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role && !$this->hasRole($role->name)) {
            $this->roles()->attach($role);
        }
    }

    /**
     * Remove a role from the user
     * 
     * @param string|Role $role
     * @return void
     */
    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role) {
            $this->roles()->detach($role);
        }
    }

    /**
     * Give permission directly to the user
     * 
     * @param string|Permission $permission
     * @return void
     */
    public function givePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        if ($permission && !$this->permissions->contains($permission)) {
            $this->permissions()->attach($permission);
        }
    }

    /**
     * Revoke permission from the user
     * 
     * @param string|Permission $permission
     * @return void
     */
    public function revokePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->detach($permission);
        }
    }
}