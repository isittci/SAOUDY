<?php

namespace App\Mail;


use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Mail\Mailables\Attachment;

class AdminMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public Message $message;
    public $ticket;
    public $partenaire;
    public $admin;
    public $replyUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;

        // Charger les relations nécessaires
        $this->message->load(['ticket', 'user', 'conversation.partenaire']);

        $this->ticket = $this->message->ticket;
        $this->partenaire = $this->message->conversation->partenaire;
        $this->admin = $this->message->user;

        // URL pour répondre au message
        $this->replyUrl = route('partenaires.tickets.show', $this->ticket->id);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            replyTo: [
                new Address(
                    config('mail.reply_to.address', config('mail.from.address')),
                    $this->admin->nom_complet
                ),
            ],
            subject: "Nouveau message sur le ticket #{$this->ticket->reference}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.messages.admin-message',
            text: 'emails.messages.admin-message-text',
            with: [
                'msg' => $this->message,
                'ticket' => $this->ticket,
                'partenaire' => $this->partenaire,
                'admin' => $this->admin,
                'replyUrl' => $this->replyUrl,
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
        $attachments = [];

        // Si le message contient une pièce jointe, l'ajouter à l'email
        if ($this->message->attachment_url && $this->message->type === 'file') {
            // Extraire le chemin relatif depuis l'URL
            $path = str_replace('/storage/', '', parse_url($this->message->attachment_url, PHP_URL_PATH));

            // Vérifier si le fichier existe
            if (Storage::disk('public')->exists($path)) {
                $attachments[] = Attachment::fromStorageDisk('public', $path)
                    ->as(basename($path));
            }
        }

        return $attachments;
    }
}
