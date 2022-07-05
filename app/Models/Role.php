<?php

namespace App\Models;

use App\Traits\RecursiveRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Role extends Model
{
    use HasFactory, RecursiveRelationship;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'status',
        'description'
    ];

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'parent_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Role::class, 'parent_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'role_user',
            'role_id',
            'user_id'
        );
    }

    /**
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_role',
            'role_id',
            'permission_id'
        );
    }

    public function getAllAction(): array
    {
        return $this->getAllPermission()->map(function ($permission) {
            return $permission->action;
        })->toArray();
    }

    /**
     * @return Collection
     */
    public function getAllPermission(): Collection
    {
        $roles = $this->getItWithAllParent();

        $allPermissions = collect([]);

        foreach ($roles as $role) {
            $allPermissions = $allPermissions->merge($role->permissions);
        }

        return $allPermissions;
    }
}
