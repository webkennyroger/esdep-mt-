<?php

namespace App\Jobs;

use App\Mail\EventAnnouncementMail;
use App\Models\EventAnnouncement;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEventAnnouncementEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public int $announcementId,
        public int $reservationId,
        public string $email,
        public string $name
    ) {}

    public function handle(): void
    {
        $announcement = EventAnnouncement::findOrFail($this->announcementId);

        if ($announcement->status === 'cancelled') {
            return;
        }

        $reservation = Reservation::with('room')->findOrFail($this->reservationId);

        try {
            Mail::to($this->email)->send(
                new EventAnnouncementMail($reservation, $this->name, $announcement->message)
            );

            $announcement->increment('sent_count');

            $this->checkCompletion($announcement);
        } catch (\Exception $e) {
            $announcement->increment('failed_count');

            $this->checkCompletion($announcement);

            throw $e;
        }
    }

    protected function checkCompletion(EventAnnouncement $announcement): void
    {
        $processed = $announcement->sent_count + $announcement->failed_count;

        if ($processed >= $announcement->total_recipients) {
            $announcement->update([
                'status' => 'completed',
                'sent_at' => now(),
            ]);
        }
    }
}
