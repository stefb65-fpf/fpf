<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendSupportNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $contenu;
    public $objet;

    /**
     * Create a new message instance.
     */
    public function __construct($contenu, $objet)
    {
        $this->contenu = $contenu;
        $this->objet = $objet;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enregistrement de votre demande de support',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.SendNewSupportDemand',
        );
    }

}
