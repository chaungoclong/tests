<?php

namespace App\Models;

use App\Traits\RecursiveRelationship;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, RecursiveRelationship;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'parent_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(User::class, 'parent_id', 'id');
    }

    /**
     * @return array
     */
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
        $users = $this->getItWithAllParent();

        $allPermissions = collect([]);

        foreach ($users as $user) {
            $allPermissions = $allPermissions->merge($user->getAllPermissionByRole());
            $allPermissions = $allPermissions->merge($user->permissions);
        }

        return $allPermissions->unique('id');
    }

    /**
     * @return Collection
     */
    public function getAllPermissionByRole(): Collection
    {
        $roles = $this->roles;

        $allPermissions = collect([]);

        foreach ($roles as $role) {
            $allPermissions = $allPermissions->merge($role->getAllPermission());
        }

        return $allPermissions->unique('id');
    }

    public function hasRole
}
