<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @property-read Room|null $room */
class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocol',
        'sort_order',
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
        'google_event_id',
        'google_meet_url',
        'google_sync_status',
        'google_sync_error',
        'google_synced_at',
        'status_notification_sent_for',
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
        'google_synced_at' => 'datetime',
    ];

    protected function setRequesterEmailAttribute(string $value): void
    {
        $this->attributes['requester_email'] = strtolower(trim($value));
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function scopeAccessibleTo(Builder $query, ?User $user): Builder
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->is_admin) {
            return $query;
        }

        return $query->whereRaw('1 = 0');
    }
}
