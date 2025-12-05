<?php

namespace App\Mail;

use App\Models\Partenaire;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartenaireResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Partenaire $partenaire;
    public string $code;

    /**
     * Create a new message instance.
     */
    public function __construct(Partenaire $partenaire, string $code)
    {
        $this->partenaire = $partenaire;
        $this->code = $code;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Réinitialisation de mot de passe - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.partenaires.reset-code',
            with: [
                'partenaire' => $this->partenaire,
                'code' => $this->code,
                'expiresIn' => 30, // minutes
            ],
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
