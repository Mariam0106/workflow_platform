<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * BR-45/46 : rappel quotidien à un Validateur ayant au moins une
 * Demande en attente de sa décision - évite l'oubli/la relance
 * manuelle systématique par le Demandeur.
 */
class PendingValidationsReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param Collection<int, \App\Models\Request> $pendingRequests
     */
    public function __construct(
        public readonly \App\Models\User $validator,
        public readonly Collection $pendingRequests,
    ) {}

    public function envelope(): Envelope
    {
        $count = $this->pendingRequests->count();

        return new Envelope(
            subject: $count === 1
                ? 'Une demande attend votre validation'
                : "{$count} demandes attendent votre validation",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.pending-validations-reminder',
        );
    }
}
