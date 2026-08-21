<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'user_id',
        'action',
        'changes',
        'reason',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Solicitação Criada',
            'approved' => 'Reserva Aprovada',
            'rejected' => 'Reserva Rejeitada',
            'updated' => 'Reserva Alterada',
            'reopened' => 'Reserva Reaberta',
            default => ucfirst($this->action),
        };
    }
}
