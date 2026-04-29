<?php
// app/Mail/SauvegardeNotification.php

namespace App\Mail;

use App\Models\Sauvegarde;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class SauvegardeNotification extends Mailable
{
    use Queueable, SerializesModels;

    /** Chemin absolu du .zip temporaire, conservé pour le __destruct. */
    private ?string $cheminZip = null;

    public function __construct(
        public readonly Sauvegarde $sauvegarde,
        public readonly int        $nombrePurgees = 0,
    ) {}

    public function envelope(): Envelope
    {
        $statut = $this->sauvegarde->statut === Sauvegarde::STATUT_TERMINEE
            ? '✅ Succès'
            : '❌ Échec';

        return new Envelope(
            subject: env('APP_NAME') . " — {$statut} — " . now()->format('d/m/Y H:i'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sauvegardes.notification',
            with: [
                'sauvegarde'    => $this->sauvegarde,
                'nombrePurgees' => $this->nombrePurgees,
                'estSucces'     => $this->sauvegarde->statut === Sauvegarde::STATUT_TERMINEE,
                'tailleMo'      => $this->sauvegarde->taille_octets
                    ? round($this->sauvegarde->taille_octets / 1024 / 1024, 2)
                    : null,
            ],
        );
    }

    /**
     * Compresse le fichier SQL en .zip et l'attache à l'e-mail.
     */
    public function attachments(): array
    {
        return [];
    }

    // public function attachments(): array
    // {
    //     if ($this->sauvegarde->statut !== Sauvegarde::STATUT_TERMINEE) {
    //         return [];
    //     }

    //     if (! Storage::exists($this->sauvegarde->chemin_stockage)) {
    //         Log::warning('SauvegardeNotification : fichier SQL introuvable pour la pièce jointe', [
    //             'chemin' => $this->sauvegarde->chemin_stockage,
    //         ]);
    //         return [];
    //     }

    //     $this->cheminZip = $this->compresserEnZip();

    //     if ($this->cheminZip === null) {
    //         return [];
    //     }

    //     return [
    //         Attachment::fromPath($this->cheminZip)
    //             ->as($this->sauvegarde->nom_fichier . '.zip')
    //             ->withMime('application/zip'),
    //     ];
    // }

    /**
     * Crée un fichier .zip temporaire contenant le .sql.
     * Retourne le chemin absolu du .zip, ou null en cas d'erreur.
     */
    private function compresserEnZip(): ?string
    {
        $cheminSql = storage_path('app/' . $this->sauvegarde->chemin_stockage);
        $cheminZip = $cheminSql . '.zip';

        try {
            $zip = new ZipArchive();

            $resultat = $zip->open($cheminZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            if ($resultat !== true) {
                throw new \RuntimeException("ZipArchive::open a échoué (code {$resultat}).");
            }

            // Ajoute le .sql dans le zip sous son nom d'origine (sans le chemin)
            $zip->addFile($cheminSql, $this->sauvegarde->nom_fichier);
            $zip->close();

            $tailleOriginale  = filesize($cheminSql);
            $tailleZip        = filesize($cheminZip);
            $ratio            = $tailleOriginale > 0
                ? round((1 - $tailleZip / $tailleOriginale) * 100, 1)
                : 0;

            Log::info('📦 Fichier SQL compressé en ZIP pour e-mail', [
                'fichier'           => $this->sauvegarde->nom_fichier . '.zip',
                'taille_originale'  => round($tailleOriginale / 1024 / 1024, 2) . ' Mo',
                'taille_zip'        => round($tailleZip        / 1024 / 1024, 2) . ' Mo',
                'compression'       => "{$ratio}%",
            ]);

            return $cheminZip;
        } catch (\Exception $e) {
            Log::error('❌ Échec compression ZIP pour e-mail', [
                'fichier' => $this->sauvegarde->chemin_stockage,
                'error'   => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Supprime le .zip temporaire après l'envoi de l'e-mail.
     */
    // public function __destruct()
    // {
    //     if ($this->cheminZip && file_exists($this->cheminZip)) {
    //         @unlink($this->cheminZip);
    //     }
    // }
}
