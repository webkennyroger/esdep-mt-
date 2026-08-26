<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    /**
     * Permissoes exclusivas do papel "super-admin" (alem do acesso geral que
     * "admin" e "super-admin" compartilham para salas e reservas).
     */
    private const SUPER_ADMIN_PERMISSIONS = ['access-contacts', 'access-reports', 'access-activity-log', 'manage-users'];

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

    protected static function booted(): void
    {
        static::saved(function (User $user): void {
            $user->syncRoleFromLegacyColumn();
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Mantem o papel do Spatie sincronizado com a coluna legada `role`, criando
     * o cargo (e, no caso do super-admin, suas permissoes) se ainda nao existir.
     * Isso garante que o sistema funcione mesmo sem depender da ordem de seeders.
     */
    public function syncRoleFromLegacyColumn(): void
    {
        $roleName = match ($this->role) {
            'super_admin', 'super-admin' => 'super-admin',
            'admin', 'manager' => 'admin',
            default => null,
        };

        if (! $roleName) {
            $this->syncRoles([]);

            return;
        }

        $role = Role::findOrCreate($roleName, 'web');

        if ($roleName === 'super-admin') {
            $permissions = array_map(
                fn (string $name) => Permission::findOrCreate($name, 'web'),
                self::SUPER_ADMIN_PERMISSIONS
            );
            $role->syncPermissions($permissions);
        }

        $this->syncRoles([$role]);
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
