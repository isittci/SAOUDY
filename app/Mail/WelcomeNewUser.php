<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeNewUser extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * L'utilisateur nouvellement créé.
     */
    public User $user;

    /**
     * Le mot de passe en clair (temporaire, pour l'envoi uniquement).
     */
    public string $plainPassword;

    /**
     * L'URL de connexion.
     */
    public string $loginUrl;

    /**
     * Le nom de l'application.
     */
    public string $appName;

    /**
     * L'administrateur qui a Enregistré le compte.
     */
    public ?User $createdBy;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $plainPassword, ?User $createdBy = null)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
        $this->loginUrl = route('auth.index');
        $this->appName = config('app.name', 'SAODY');
        $this->createdBy = $createdBy;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Bienvenue sur {$this->appName} - Vos identifiants de connexion",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.users.welcome',
            text: 'emails.users.welcome-text',
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
