<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\ReservationStatus;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocol',
        'room_id',
        'event_name',
        'description',
        'reject_reason',
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
        'projetor',
        'attachment_path',
        'attachment_name',
        'reminder_sent_at',
        'terms_accepted',
        'terms_accepted_at',
    ];

    protected $casts = [
        'date' => 'date',
        'attendees_count' => 'integer',
        'status' => ReservationStatus::class,
        'coffee_break' => 'boolean',
        'sonorizacao' => 'boolean',
        'transmissao' => 'boolean',
        'projetor' => 'boolean',
        'reminder_sent_at' => 'datetime',
        'terms_accepted' => 'boolean',
        'terms_accepted_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ReservationLog::class)->latest();
    }
}
