<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Contact extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sort_order',
        'email',
        'password',
        'cpf',
        'tipo',
        'user_id',
        'setor',
        'autorizado_at',
        'last_login_at',
        'is_active',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
        'autorizado_at' => 'datetime',
        'last_login_at' => 'datetime',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_contact_group')->withPivot(['is_responsible', 'sort_order']);
    }

    /**
     * Conta de login vinculada a este contato, quando existir.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isResponsibleForGroup(ContactGroup $group): bool
    {
        return $this->groups()
            ->where('contact_contact_group.contact_group_id', $group->id)
            ->where('contact_contact_group.is_responsible', true)
            ->exists();
    }

    public function responsibleGroups()
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_contact_group')
            ->wherePivot('is_responsible', true)
            ->withPivot('is_responsible');
    }

    public function managedRoomIds()
    {
        return Room::query()->whereIn(
            'contact_group_id',
            $this->groups()->select('contact_groups.id')
        )->select('rooms.id');
    }

    public function responsibleRoomIds()
    {
        return Room::query()->whereIn(
            'contact_group_id',
            $this->responsibleGroups()->select('contact_groups.id')
        )->select('rooms.id');
    }

    public function canManageReservation(Reservation $reservation): bool
    {
        return $this->responsibleRoomIds()->whereKey($reservation->room_id)->exists();
    }

    public function accessibleReservations(): Builder
    {
        return Reservation::query()->where(function (Builder $query): void {
            $query->where('requester_email', strtolower($this->email))
                ->orWhereIn('room_id', $this->managedRoomIds());
        });
    }

    public function subscribe(): void
    {
        $this->update([
            'is_active' => true,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);
    }

    public function unsubscribe(): void
    {
        $this->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);
    }
}
