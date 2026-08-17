<?php

namespace App\Services;

use Carbon\Carbon;

class ConflictCheckerService
{
    public function check(int $roomId, string|Carbon $date, string|Carbon $startTime, string|Carbon $endTime, int $excludeId = 0): void
    {
        $dateStr = is_string($date) ? $date : $date->format('Y-m-d');
        $startStr = is_string($startTime) ? $startTime : $startTime->format('H:i');
        $endStr = is_string($endTime) ? $endTime : $endTime->format('H:i');

        $overlapping = \App\Models\Reservation::where('room_id', $roomId)
            ->whereRaw("date(date) = ?", [$dateStr])
            ->where(function ($query) use ($startStr, $endStr) {
                $query->where('start_time', '<', $endStr)
                    ->where('end_time', '>', $startStr);
            })
            ->whereIn('status', ['authorized', 'pending'])
            ->where('id', '!=', $excludeId)
            ->exists();

        if ($overlapping) {
            throw new \Exception(
                "Conflito de horario: Esta sala ja possui uma reserva agendada ".
                "no mesmo periodo. Escolha outro horario."
            );
        }
    }
}
