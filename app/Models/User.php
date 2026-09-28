<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property-read Contact|null $contact
 */
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

    /**
     * Cadastro de contato correspondente a esta conta, quando existir. E dele
     * que vem a lotacao (setor) e a responsabilidade por salas do setor.
     */
    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class);
    }

    /**
     * Salas dos setores aos quais a pessoa pertence — o que ela pode enxergar.
     */
    public function managedRoomIds(): Builder
    {
        return $this->contact?->managedRoomIds() ?? Room::query()->whereRaw('1 = 0')->select('rooms.id');
    }

    /**
     * Salas dos setores pelos quais a pessoa e responsavel — o que ela pode
     * gerenciar (aprovar, rejeitar, editar reservas).
     */
    public function responsibleRoomIds(): Builder
    {
        return $this->contact?->responsibleRoomIds() ?? Room::query()->whereRaw('1 = 0')->select('rooms.id');
    }

    public function canManageReservation(Reservation $reservation): bool
    {
        if ($this->is_admin) {
            return true;
        }

        return $this->responsibleRoomIds()->whereKey($reservation->room_id)->exists();
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->hasAnyRole(['admin', 'super-admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Alem de admin/super-admin, o "gerenciador" tambem entra no painel — mas
     * so alcanca as salas do proprio setor (via managedRoomIds/responsibleRoomIds).
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->is_admin || $this->hasRole('gerenciador');
    }
}
