<?php

namespace App\Jobs;

use App\Models\Reservation;
use App\Services\GoogleCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncGoogleCalendarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly int $reservationId) {}

    public function handle(GoogleCalendarService $service): void
    {
        $reservation = Reservation::with(['room.contactGroup.responsibleContacts'])->find($this->reservationId);

        if (! $reservation) {
            return;
        }

        $service->sync($reservation);
    }
}
