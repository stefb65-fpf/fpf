<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    private $file;

    /**
     * Create a new message instance.
     */
    public function __construct($invoice, $file)
    {
        $this->invoice = $invoice;
        $this->file = $file;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'FPF // Facture émise par la FPF',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.SendInvoice',
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
