<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendColisageFlorilegeUr extends Mailable
{
    use Queueable, SerializesModels;

    private $file;

    /**
     * Create a new message instance.
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('no-reply@federation-photo.fr', 'Département Publication'),
            replyTo: [
                new Address('dpt.publicat@federation-photo.fr', 'Département Publication')
            ],
            subject: 'FPF // Liste de colisage des Florilèges pour votre UR',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.SendColisageFlorilegeUr',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->file != null) {
            $tab_file = explode('/', $this->file);
            $name = $tab_file[count($tab_file) - 1];
            return [
                Attachment::fromPath($this->file)
                    ->as($name)
                    ->withMime('application/pdf'),
            ];
        } else {
            return [];
        }
    }
}
