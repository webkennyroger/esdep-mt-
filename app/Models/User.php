<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'role',
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
        ];
    }

    public function getIsAdminAttribute(): bool
    {
        return in_array($this->role, ['super-admin', 'super_admin', 'admin', 'manager']);
    }

    public function isSuperAdmin(): bool
    {
        // "admin" e "super-admin" sao mantidos como perfis globais legados.
        return in_array($this->role, ['super_admin', 'super-admin', 'admin'], true);
    }
}
