<?php

namespace App\Events;

use App\Models\Reservation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationRejected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Reservation $reservation) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('reservations'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'reservation.rejected';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->reservation->id,
            'event_name' => $this->reservation->event_name,
            'status' => $this->reservation->status->value,
        ];
    }
}
