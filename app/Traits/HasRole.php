<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRole
{
    /**
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_user',
            'user_id',
            'permission_id'
        );
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_user',
            'user_id',
            'role_id'
        );
    }

    /**
     * Get user's permissions via role
     *
     * @return Collection
     */
    public function getPermissionsViaRoles(): Collection
    {
        $permissions = collect([]);

        if (empty($this->roles) || $this->roles->count() === 0) {
            return $permissions;
        }

        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }

        return $permissions->unique('id');
    }

    /**
     * Get all user's permissions
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection
    {
        $permissions = $this->getPermissionsViaRoles();

        if (empty($this->permissions) || $this->permissions->count() === 0) {
            return $permissions;
        }

        return $permissions->merge($this->permissions)->unique('id');
    }

    /**
     * Get all actions
     *
     * @return array
     */
    public function getAllActions(): array
    {
        return $this->getAllPermissions()->map(function ($permission) {
            return $permission->action;
        })->toArray();
    }

    /**
     * Get actions via roles
     *
     * @return array
     */
    public function getActionsViaRoles(): array
    {
        return $this->getPermissionsViaRoles()->map(function ($permission) {
            return $permission->action;
        })->toArray();
    }

    /**
     * Check user has a specific action
     *
     * @param $action
     *
     * @return bool
     */
    public function hasAction($action): bool
    {
        return in_array($action, $this->getAllActions());
    }

    /**
     * Check user has a all action
     *
     * @param mixed ...$actions
     *
     * @return bool
     */
    public function hasAllAction(...$actions): bool
    {
        $actions = flatArray($actions);

        return empty(array_diff($actions, $this->getAllActions()));
    }

    /**
     * Check user has a all action
     *
     * @param mixed ...$actions
     *
     * @return bool
     */
    public function hasAnyAction(...$actions): bool
    {
        $actions = flatArray($actions);

        return !empty(array_intersect($actions, $this->getAllActions()));
    }

    /**
     * Check user has all permission in list
     *
     * @param mixed ...$permissions
     *
     * @return bool
     */
    public function hasAllPermission(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten();

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check user has any permission in list
     *
     * @param mixed ...$permissions
     *
     * @return bool
     */
    public function hasAnyPermission(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten();

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check user has specific permission
     *
     * @param $permission
     *
     * @return bool
     */
    public function hasPermission($permission): bool
    {
        $permission = $this->getPermissionByKey($permission);

        if ($permission === null) {
            return false;
        }

        return $this->getAllPermissions()->contains('id', $permission->id);
    }

    /**
     * Check user has all role in list
     *
     * @param mixed ...$roles
     *
     * @return bool
     */
    public function hasAllRole(...$roles): bool
    {
        $roles = collect($roles)->flatten();

        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check user has any role in list
     *
     * @param mixed ...$roles
     *
     * @return bool
     */
    public function hasAnyRole(...$roles): bool
    {
        $roles = collect($roles)->flatten();

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $role
     *
     * @return bool
     */
    public function hasRole($role): bool
    {
        $role = $this->getRoleByKey($role);

        if ($role === null || empty($this->roles) || $this->roles->count() === 0) {
            return false;
        }

        return $this->roles->contains('id', $role->id);
    }

    /**
     * Get permission by key(id, slug)
     *
     * @param $key
     *
     * @return null
     */
    public function getPermissionByKey($key)
    {
        /** Integer: find by id */
        if (is_int($key)) {
            return Permission::find($key);
        }

        /** String: find by slug || id */
        if (is_string($key)) {
            return (Permission::where('slug', $key)->first() ?? Permission::where('id', $key)->first());
        }

        /** Object: if not instance of Permission -> null */
        if (!$key instanceof Permission) {
            return null;
        }

        /** Find element has id equal $key id*/
        return Permission::find($key->id ?? null);
    }

    /**
     * Get role by key(id, slug)
     *
     * @param $key
     *
     * @return null
     */
    public function getRoleByKey($key)
    {
        /** Integer: find by id */
        if (is_int($key)) {
            return Role::find($key);
        }

        /** String: find by slug || id */
        if (is_string($key)) {
            return (Role::where('slug', $key)->first() ?? Role::where('id', $key)->first());
        }

        /** Object: if not instance of Role -> null */
        if (!$key instanceof Role) {
            return null;
        }

        /** Find element has id equal $key id*/
        return Role::find($key->id ?? null);
    }

    /**
     * Check user is super admin
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        if (empty($this->roles) || $this->roles->count() === 0) {
            return false;
        }

        foreach ($this->roles as $role) {
            if (strtolower($role->slug) === strtolower(Role::SUPER_ADMIN)) {
                return true;
            }
        }

        return false;
    }
}
