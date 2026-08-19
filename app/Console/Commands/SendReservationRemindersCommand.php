<?php

namespace App\Console\Commands;

use App\Enums\ReservationStatus;
use App\Mail\ReservationReminderMail;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReservationRemindersCommand extends Command
{
    protected $signature = 'reservations:send-reminders';
    protected $description = 'Envia lembretes para solicitantes com reservas nas proximas 24h';

    public function handle(): int
    {
        $tomorrow = Carbon::tomorrow();
        $dayAfterTomorrow = Carbon::tomorrow()->addDay();

        $reservations = Reservation::with('room')
            ->where('status', ReservationStatus::AUTHORIZED)
            ->whereDate('date', '>=', $tomorrow)
            ->whereDate('date', '<', $dayAfterTomorrow)
            ->whereNull('reminder_sent_at')
            ->get();

        if ($reservations->isEmpty()) {
            $this->info('Nenhum lembrete para enviar.');
            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($reservations as $reservation) {
            try {
                Mail::to($reservation->requester_email)
                    ->send(new ReservationReminderMail($reservation));

                $reservation->update(['reminder_sent_at' => Carbon::now()]);
                $sent++;

                $this->info("Lembrete enviado para {$reservation->requester_email} - {$reservation->event_name}");
            } catch (\Exception $e) {
                $this->error("Erro ao enviar lembrete para {$reservation->requester_email}: {$e->getMessage()}");
                \Log::error("Erro ao enviar lembrete: {$e->getMessage()}");
            }
        }

        $this->info("Total de lembretes enviados: {$sent}");

        return self::SUCCESS;
    }
}
