<?php

namespace App\Mail;

use App\Models\Contact;
use App\Models\NoteContact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlerteAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public Contact $contact;
    public NoteContact $noteContact;

    /**
     * Create a new message instance.
     */
    public function __construct(Contact $contact, NoteContact $noteContact)
    {
        $this->contact = $contact;
        $this->noteContact = $noteContact;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $sujet = $this->noteContact->typeService
            ? $this->noteContact->typeService->titre
            : 'Autre demande';

        return new Envelope(
            subject: "🔔 Nouvelle demande de contact - {$sujet} - {$this->contact->nom_complet}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contacts.alerte-admin',
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
