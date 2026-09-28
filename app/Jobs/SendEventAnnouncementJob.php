<?php

namespace App\Jobs;

use App\Mail\EventAnnouncementMail;
use App\Models\EventAnnouncement;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Envio sincrono (nao implementa ShouldQueue): QUEUE_CONNECTION=database em
 * producao e nao ha worker configurado, entao um job enfileirado ficaria
 * parado na tabela `jobs` para sempre e nunca seria entregue.
 */
class SendEventAnnouncementJob
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct(
        private readonly int $announcementId,
        private readonly array $contactIds,
        private readonly array $extraEmails = [],
    ) {}

    public function handle(): void
    {
        $announcement = EventAnnouncement::with('reservation')->find($this->announcementId);

        if (! $announcement) {
            return;
        }

        $contacts = \App\Models\Contact::whereIn('id', $this->contactIds)->get(['id', 'name', 'email']);

        $sent = 0;
        $failed = 0;

        foreach ($contacts as $contact) {
            try {
                Mail::to($contact->email)->send(
                    new EventAnnouncementMail($announcement->reservation, $contact->name, $announcement->message)
                );

                $sent++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("Erro ao enviar email para {$contact->email}: ".$e->getMessage());
            }
        }

        foreach ($this->extraEmails as $email) {
            try {
                Mail::to($email)->send(
                    new EventAnnouncementMail($announcement->reservation, $email, $announcement->message)
                );

                $sent++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("Erro ao enviar email para {$email}: ".$e->getMessage());
            }
        }

        $announcement->update([
            'sent_count' => $announcement->sent_count + $sent,
            'failed_count' => $announcement->failed_count + $failed,
            'status' => 'completed',
            'sent_at' => now(),
        ]);
    }
}
