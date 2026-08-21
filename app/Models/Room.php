<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Room extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Room $room): void {
            if ($room->code) {
                return;
            }

            $baseCode = Str::upper(Str::limit(Str::slug($room->name, '-'), 40, '')) ?: 'SALA';
            $code = $baseCode;
            $suffix = 1;

            while (static::where('code', $code)->exists()) {
                $code = $baseCode.'-'.str_pad((string) $suffix, 3, '0', STR_PAD_LEFT);
                $suffix++;
            }

            $room->code = $code;
        });
    }

    protected $fillable = [
        'contact_group_id',
        'sort_order',
        'name',
        'code',
        'capacity',
        'theme_color',
        'description',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function contactGroup(): BelongsTo
    {
        return $this->belongsTo(ContactGroup::class);
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
