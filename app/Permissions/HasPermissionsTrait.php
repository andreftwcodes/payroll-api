<?php

namespace App\Permissions;

use App\Models\{Role, Permission};

trait HasPermissionsTrait
{
    public function assignRole(...$role)
    {
        $roles = $this->getAllRoles($role);
        
        if ($roles === null) {
            return $this;
        }

        $this->roles()->saveMany($roles);

        return $this;
    }

    public function withdrawRoleTo(...$role)
    {
        $roles = $this->getAllRoles($role);
        
        if ($roles === null) {
            return $this;
        }

        $this->roles()->detach($roles);

        return $this;
    }

    public function updateRoles(...$roles)
    {
        $this->roles()->detach();

        $this->assignRole($roles);
    }

    public function givePermissionTo(...$permission)
    {
        $permissions = $this->getAllPermissions($permission);
        
        if ($permissions === null) {
            return $this;
        }

        $this->permissions()->saveMany($permissions);

        return $this;
    }
    
    public function withdrawPermissionTo(...$permission)
    {
        $permissions = $this->getAllPermissions($permission);
        
        $this->permissions()->detach($permissions);

        return $this;
    }

    public function updatePermissions(...$permissions)
    {
        $this->permissions()->detach();

        $this->givePermissionTo($permissions);
    }

    public function hasRole(...$roles)
    {
        foreach ($roles as $role) {
            if ($this->roles->contains('name', $role)) {
                return true;
            }
        }

        return false;
    }

    public function hasPermissionTo($permission)
    {
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
    }

    protected function hasPermissionThroughRole($permission)
    {
        foreach ($permission->roles as $role) {
            if ($this->roles->contains($role)) {
                return true;
            }
        }

        return false;
    }

    protected function hasPermission($permission)
    {
        return (bool) $this->permissions->where('name', $permission->name)->count();
    }

    protected function getAllRoles(array $roles)
    {
        return Role::whereIn('id', array_flatten($roles))->get();
    }

    protected function getAllPermissions(array $permissions)
    {
        return Permission::whereIn('name', array_flatten($permissions))->get();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'users_permissions');
    }
}
