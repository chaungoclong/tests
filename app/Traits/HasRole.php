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
     * Get permission model
     *
     * @return mixed
     */
    protected function getPermissionModel()
    {
        return app(config('permissions.models.permission'));
    }

    /**
     * Get role model
     *
     * @return mixed
     */
    protected function getRoleModel()
    {
        return app(config('permissions.models.role'));
    }

    /**
     * Get all permissions of user
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection
    {
        $primaryKey = $this->getPermissionModel()->getKeyName();
        $permissions = collect([]);

        if (empty($this->roles) || $this->roles->count() === 0) {
            return $permissions;
        }

        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }

        return $permissions->unique($primaryKey);
    }

    /**
     * Get all actions
     *
     * @return array
     */
    public function getAllActions(): array
    {
        return $this->getAllPermissions()->pluck('action')->toArray();
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
        if ($this->isSuperAdmin()) {
            return true;
        }

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
        if ($this->isSuperAdmin()) {
            return true;
        }

        $permission = $this->getPermissionByKey($permission);

        if ($permission === null) {
            return false;
        }

        return $this->getAllPermissions()->contains($permission->getKeyName(), $permission->getKey());
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
        if ($this->isSuperAdmin()) {
            return true;
        }

        $role = $this->getRoleByKey($role);

        if ($role === null || empty($this->roles) || $this->roles->count() === 0) {
            return false;
        }

        return $this->roles->contains($role->getKeyName(), $role->getKey());
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
        $permissionModel = $this->getPermissionModel();

        /** Integer: find by primary key */
        if (is_int($key)) {
            return $permissionModel->find($key);
        }

        /** String: find by slug || primary key */
        if (is_string($key)) {
            $permission = $permissionModel->where('slug', $key)->first();

            return $permission ?? $permissionModel->where($permissionModel->getKeyName(), $key)->first();
        }

        /** Object: if not instance of Permission -> null */
        if (!$key instanceof Permission) {
            return null;
        }

        /** Find element has id equal $key primary key*/
        return $permissionModel->find($key->getKey() ?? null);
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
        $roleModel = $this->getRoleModel();

        /** Integer: find by id */
        if (is_int($key)) {
            return $roleModel->find($key);
        }

        /** String: find by slug || id */
        if (is_string($key)) {
            $role = $roleModel->where('slug', $key)->first();

            return $role ?? $roleModel->where($roleModel->getKeyName(), $key)->first();
        }

        /** Object: if not instance of Role -> null */
        if (!$key instanceof $roleModel) {
            return null;
        }

        /** Find element has primary key equal $key primary key*/
        return $roleModel->find($key->getKey() ?? null);
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
