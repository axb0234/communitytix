<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'platform_role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tenantUsers(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

    public function isPlatformAdmin(): bool
    {
        return $this->platform_role === 'PLATFORM_ADMIN';
    }

    public function roleInTenant(int $tenantId): ?string
    {
        $tu = $this->tenantUsers()->where('tenant_id', $tenantId)->first();
        return $tu?->role_in_tenant;
    }

    public function isGoverningIn(int $tenantId): bool
    {
        return $this->roleInTenant($tenantId) === 'TENANT_GOVERNING';
    }
}
