<?php

namespace App\Mail;

use App\Models\Partenaire;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// class PartenaireCredentialsMail extends Mailable
// {
//     use Queueable, SerializesModels;

//     public Partenaire $partenaire;
//     public string $password;

//     /**
//      * Create a new message instance.
//      */
//     public function __construct(Partenaire $partenaire, string $password)
//     {
//         $this->partenaire = $partenaire;
//         $this->password = $password;
//     }

//     /**
//      * Get the message envelope.
//      */
//     public function envelope(): Envelope
//     {
//         return new Envelope(
//             subject: 'Accès à votre compte - ' . config('app.name'),
//         );
//     }

//     /**
//      * Get the message content definition.
//      */
//     public function content(): Content
//     {
//         return new Content(
//             view: 'emails.partenaires.access',
//             with: [
//                 'partenaire' => $this->partenaire,
//                 'password' => $this->password
//             ],
//         );
//     }

//     /**
//      * Get the attachments for the message.
//      *
//      * @return array<int, \Illuminate\Mail\Mailables\Attachment>
//      */
//     public function attachments(): array
//     {
//         return [];
//     }
// }


class PartenaireCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public Partenaire $partenaire;
    public string $password;

    /**
     * Create a new message instance.
     */
    public function __construct(Partenaire $partenaire, string $password)
    {
        $this->partenaire = $partenaire;
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Vos identifiants de connexion - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.partenaires.credentials',
            with: [
                'partenaire' => $this->partenaire,
                'password' => $this->password,
                'loginUrl' => route('partenaires.login'),
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
