<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'keycloak_id',
        'is_active',
        'activated_at',
        'activated_by_id',
        'last_login_at',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'activated_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Vinculado a uma conta do SSO institucional.
     */
    public function isSsoUser(): bool
    {
        return $this->keycloak_id !== null;
    }

    /**
     * Libera o acesso de um usuario provisionado pelo SSO que estava aguardando.
     */
    public function activate(?User $by = null): void
    {
        $this->forceFill([
            'is_active' => true,
            'activated_at' => now(),
            'activated_by_id' => $by?->id,
        ])->save();
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->hasAnyRole(['admin', 'super-admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }
}
