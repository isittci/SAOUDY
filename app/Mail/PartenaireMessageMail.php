<?php

namespace App\Mail;

use App\Models\Message;
use App\Models\Partenaire;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Mail\Mailables\Attachment;

class PartenaireMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public Partenaire $partenaire;
    public Message $message;
    public $ticket;
    public $conversation;
    public $assignedUser;
    public $viewUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Partenaire $partenaire, Message $message)
    {
        $this->partenaire = $partenaire;
        $this->message = $message;

        // Charger les relations nécessaires
        $this->message->load(['ticket.assignedUser', 'conversation']);

        $this->ticket = $this->message->ticket;
        $this->conversation = $this->message->conversation;
        $this->assignedUser = $this->ticket->assignedUser;

        // URL pour voir le message (côté admin/user)
        $this->viewUrl = route('tickets.show', $this->ticket->id);
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
                    $this->ticket->assignedUser ? $this->ticket->assignedUser->email : env('MAIL_REPLY_TO_ADDRESS'),
                    $this->ticket->assignedUser ? $this->ticket->assignedUser->nom_complet : env('MAIL_REPLY_TO_NAME')
                ),
            ],
            subject: "[Ticket #{$this->ticket->reference}] Nouveau message de {$this->partenaire->nom_complet}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.messages.partenaire-message',
            text: 'emails.messages.partenaire-message-text',
            with: [
                'partenaire' => $this->partenaire,
                'msg' => $this->message,
                'ticket' => $this->ticket,
                'conversation' => $this->conversation,
                'assignedUser' => $this->assignedUser,
                'viewUrl' => $this->viewUrl,
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
