<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Exceptions\ConflictException;
use Carbon\Carbon;

class ConflictCheckerService
{
    public function check(int $roomId, string|Carbon $date, string|Carbon $startTime, string|Carbon $endTime, int $excludeId = 0): void
    {
        $dateStr  = is_string($date)      ? $date      : $date->format('Y-m-d');
        $startStr = is_string($startTime) ? $startTime : $startTime->format('H:i');
        $endStr   = is_string($endTime)   ? $endTime   : $endTime->format('H:i');

        $overlapping = \App\Models\Reservation::where('room_id', $roomId)
            ->whereRaw("date(date) = ?", [$dateStr])
            ->where(function ($query) use ($startStr, $endStr) {
                $query->where('start_time', '<', $endStr)
                      ->where('end_time', '>', $startStr);
            })
            ->whereIn('status', [
                ReservationStatus::AUTHORIZED->value,
                ReservationStatus::PENDING->value,
            ])
            ->where('id', '!=', $excludeId)
            ->exists();

        if ($overlapping) {
            throw ConflictException::forRoom($roomId);
        }
    }
}
