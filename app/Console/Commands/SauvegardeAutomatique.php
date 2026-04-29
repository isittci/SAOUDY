<?php
// app/Console/Commands/SauvegardeAutomatique.php

namespace App\Console\Commands;

use App\Mail\SauvegardeNotification;
use App\Models\Sauvegarde;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class SauvegardeAutomatique extends Command
{
    protected $signature   = 'saoudy:sauvegarde {--retention=30 : Jours de rétention}';
    protected $description = 'Crée une sauvegarde automatique de la base de données PostgreSQL';

    public function handle(): int
    {
        $this->info('🔄 Lancement sauvegarde automatique…');

        $nomFichier    = 'saoudy_auto_' . now()->format('Y-m-d_H-i-s') . '_' . Str::random(4) . '.sql';
        $dossier       = 'sauvegardes';
        $cheminRelatif = "{$dossier}/{$nomFichier}";
        $cheminAbsolu  = storage_path("app/{$cheminRelatif}");
        $retention     = (int) $this->option('retention');

        Storage::makeDirectory($dossier);

        $sauvegarde = Sauvegarde::create([
            'nom_fichier'     => $nomFichier,
            'chemin_stockage' => $cheminRelatif,
            'type'            => Sauvegarde::TYPE_AUTOMATIQUE,
            'statut'          => Sauvegarde::STATUT_EN_COURS,
            'expire_a'        => now()->addDays($retention),
        ]);

        $config   = config('database.connections.pgsql');
        $commande = [
            'pg_dump',
            '--host='     . ($config['host'] ?? '127.0.0.1'),
            '--port='     . ($config['port'] ?? '5432'),
            '--username=' . ($config['username'] ?? 'postgres'),
            '--format=plain',
            '--no-password',
            '--file='     . $cheminAbsolu,
            $config['database'],
        ];

        $process = new Process($commande, null, array_merge($_ENV, [
            'PGPASSWORD' => $config['password'] ?? '',
        ]));
        $process->setTimeout(300);

        try {
            $process->mustRun();

            $sauvegarde->update([
                'statut'        => Sauvegarde::STATUT_TERMINEE,
                'taille_octets' => filesize($cheminAbsolu),
                'checksum_md5'  => md5_file($cheminAbsolu),
            ]);

            $this->info("✅ Sauvegarde créée : {$nomFichier}");

            // Purger les sauvegardes automatiques expirées
            $nombrePurgees = $this->purgerExpirees();

            // Notifier les super administrateurs par e-mail
            $this->notifierSuperAdmins($sauvegarde->fresh(), $nombrePurgees);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $sauvegarde->update([
                'statut'         => Sauvegarde::STATUT_ECHEC,
                'message_erreur' => $e->getMessage(),
            ]);

            $this->error("❌ Échec : " . $e->getMessage());
            Log::error('❌ Sauvegarde automatique échouée', ['error' => $e->getMessage()]);

            // Notifier également en cas d'échec
            $this->notifierSuperAdmins($sauvegarde->fresh(), 0);

            return Command::FAILURE;
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Purge les sauvegardes expirées et retourne le nombre supprimé.
     */
    private function purgerExpirees(): int
    {
        $expirees = Sauvegarde::expirees()->get();

        foreach ($expirees as $s) {
            if (Storage::exists($s->chemin_stockage)) {
                Storage::delete($s->chemin_stockage);
            }
            $s->delete();
        }

        $nombre = $expirees->count();

        if ($nombre > 0) {
            $this->line("🧹 {$nombre} sauvegarde(s) expirée(s) purgée(s).");
        }

        return $nombre;
    }

    /**
     * Envoie un e-mail de rapport à tous les super administrateurs.
     */
    private function notifierSuperAdmins(Sauvegarde $sauvegarde, int $nombrePurgees): void
    {
        $superAdmins = User::whereHas('role', function ($q) {
            $q->where('roles.slug', 'super-administrateur');
        })->get();

        if ($superAdmins->isEmpty()) {
            $this->warn('⚠️ Aucun super administrateur trouvé — notification ignorée.');
            Log::warning('Sauvegarde : aucun super administrateur trouvé pour la notification e-mail.');
            return;
        }

        $mailable = new SauvegardeNotification($sauvegarde, $nombrePurgees);
        $envoyes  = 0;

        foreach ($superAdmins as $admin) {
            try {
                Mail::to($admin->email)->send($mailable);
                $envoyes++;
            } catch (\Exception $e) {
                Log::error('❌ Échec envoi e-mail sauvegarde', [
                    'destinataire' => $admin->email,
                    'error'        => $e->getMessage(),
                ]);
                $this->warn("⚠️ Impossible d'envoyer l'e-mail à {$admin->email} : " . $e->getMessage());
            }
        }

        if ($envoyes > 0) {
            $this->info("📧 Notification envoyée à {$envoyes} super-admin(s).");
            Log::info("📧 Notification sauvegarde envoyée à {$envoyes} super-admin(s).");
        }
    }
}
