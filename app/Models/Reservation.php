<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ReservationStatus;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'event_name',
        'description',
        'requester_name',
        'requester_email',
        'date',
        'start_time',
        'end_time',
        'attendees_count',
        'status',
        'coffee_break',
        'sonorizacao',
        'transmissao',
    ];

    protected $casts = [
        'date' => 'date',
        'attendees_count' => 'integer',
        'status' => ReservationStatus::class,
        'coffee_break' => 'boolean',
        'sonorizacao' => 'boolean',
        'transmissao' => 'boolean',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
