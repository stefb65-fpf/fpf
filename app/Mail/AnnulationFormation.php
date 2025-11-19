<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnnulationFormation extends Mailable
{
    use Queueable, SerializesModels;

    public $session;
    public $type;

    /**
     * @param $session
     * @param int $type type du message à envoyer - 0: message adhérent, 1: message club ou UR, 2: message formateur
     */
    public function __construct($session, int $type)
    {
        $this->session = $session;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'FPF // Annulation de la formation '.$this->session->formation->name.' en date du '.date("d/m/Y",strtotime($this->session->start_date)),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.AnnulationFormation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
