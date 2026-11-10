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

    private const SUBJECT_PERMISSION_MAP = [
        'math' => 'subject-math',
        'khmer' => 'subject-khmer',
        'english' => 'subject-english',
        'history' => 'subject-history',
        'geography' => 'subject-geography',
        'chemistry' => 'subject-chemistry',
        'physics' => 'subject-physics',
        'biology' => 'subject-biology',
        'ethics' => 'subject-morality',
        'sports' => 'subject-sport',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'phone',
        'position',
        'is_approved',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_approved' => 'boolean',
    ];

    /**
     * Get the primary role name of the user.
     */
    public function getRoleAttribute(): string
    {
        return $this->roles->first()?->name ?? '';
    }

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
     * Relationship with parent profile
     */
    public function parent()
    {
        return $this->hasOne(SchoolParent::class);
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
     * Check if user is Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
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
     * Check only direct user permissions, ignoring role permissions.
     *
     * @param string|array $permission
     * @return bool
     */
    public function hasDirectPermission($permission)
    {
        if (is_array($permission)) {
            return $this->permissions()->whereIn('name', $permission)->exists();
        }

        return $this->permissions()->where('name', $permission)->exists();
    }

    /**
     * Check whether the user has any permission whose name starts with the given prefix.
     */
    public function hasPermissionPrefix(string $prefix): bool
    {
        if ($this->permissions()->where('name', 'like', $prefix.'%')->exists()) {
            return true;
        }

        return $this->roles()->whereHas('permissions', function ($query) use ($prefix) {
            $query->where('name', 'like', $prefix.'%');
        })->exists();
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
     * Check whether the teacher profile is assigned to the given class.
     */
    public function hasAssignedClass(string $className): bool
    {
        if (!$this->teacher || !$this->teacher->class_assigned) {
            return false;
        }

        $assignedClasses = array_map('trim', explode(',', $this->teacher->class_assigned));

        return in_array($className, $assignedClasses, true);
    }

    /**
     * Check whether the student profile belongs to the given class.
     */
    public function hasStudentGrade(string $className): bool
    {
        return (string) optional($this->student)->grade === $className;
    }

    /**
     * Can the user manage teacher records?
     */
    public function canManageTeachers(): bool
    {
        return $this->hasRole('admin') || $this->hasPermission('manage-teacher');
    }

    /**
     * Can the user access the student list?
     */
    public function canViewStudents(): bool
    {
        return $this->hasRole('admin')
            || $this->hasPermission('view-student')
            || $this->hasPermission('add-student');
    }

    /**
     * Can the user create student records?
     */
    public function canManageStudents(): bool
    {
        return $this->hasRole('admin') || $this->hasPermission('add-student');
    }

    /**
     * Can the user open score entry and edit score data?
     */
    public function canManageScores(): bool
    {
        return $this->hasRole('admin')
            || $this->hasRole('teacher')
            || $this->hasPermission('add-scores')
            || $this->hasPermissionPrefix('subject-');
    }

    /**
     * Can the user access class listing pages?
     */
    public function canAccessClassesOverview(): bool
    {
        return $this->hasRole('admin')
            || $this->hasRole('teacher')
            || $this->hasPermission('view-scores')
            || $this->hasPermissionPrefix('class-');
    }

    /**
     * Can the user access result listing pages?
     */
    public function canAccessResultsOverview(): bool
    {
        return $this->hasRole('admin')
            || $this->hasRole('teacher')
            || $this->hasRole('student')
            || $this->hasPermission('view-scores')
            || $this->hasPermissionPrefix('class-');
    }

    /**
     * Can the user access a specific class page?
     */
    public function canAccessClass(string $className): bool
    {
        return $this->hasRole('admin')
            || $this->hasPermission('view-scores')
            || $this->hasPermission('class-'.$className)
            || $this->hasAssignedClass($className);
    }

    /**
     * Can the user access a specific class result page?
     */
    public function canAccessResultClass(string $className): bool
    {
        return $this->canAccessClass($className)
            || $this->hasStudentGrade($className);
    }

    /**
     * Can the user access the global score records area?
     */
    public function canViewAllScoreRecords(): bool
    {
        return $this->hasRole('admin')
            || $this->hasRole('teacher')
            || $this->hasPermission('view-scores');
    }

    /**
     * Can the user edit a specific subject field on the score form?
     */
    public function canEditSubjectField(string $field): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        $permission = self::SUBJECT_PERMISSION_MAP[$field] ?? null;

        return $permission !== null && $this->hasDirectPermission($permission);
    }

    /**
     * Determine whether the user can edit at least one subject score field.
     */
    public function canEditAnySubjectField(): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->editableSubjectFields() !== [];
    }

    /**
     * Return the score fields the user is allowed to edit.
     *
     * @return array<int, string>
     */
    public function editableSubjectFields(): array
    {
        return array_values(array_filter(
            array_keys(self::SUBJECT_PERMISSION_MAP),
            fn (string $field): bool => $this->canEditSubjectField($field)
        ));
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
