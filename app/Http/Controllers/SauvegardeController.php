<?php
// app/Http/Controllers/Admin/SauvegardeController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\SauvegardeNotification;
use App\Models\Sauvegarde;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class SauvegardeController extends Controller
{
    // =========================================================================
    // AUTORISATIONS
    // =========================================================================

    private function autoriserSuperAdmin(): void
    {
        /** @var User $user */
        // Auth::user()->hasAnyRole(['conducteur'])
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->isAdmin() /*&& !$user->hasPermission('admin.sauvegarde')*/) {
            abort(403, 'Accès réservé aux administrateurs système.');
        }
    }

    // =========================================================================
    // INDEX
    // =========================================================================

    public function index(Request $request)
    {
        $this->autoriserSuperAdmin();

        $query = Sauvegarde::with('creeePar')
            ->orderByDesc('created_at');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $sauvegardes = $query->paginate(15)->withQueryString();

        $stats = [
            'total'      => Sauvegarde::count(),
            'terminées'  => Sauvegarde::where('statut', Sauvegarde::STATUT_TERMINEE)->count(),
            'echecs'     => Sauvegarde::where('statut', Sauvegarde::STATUT_ECHEC)->count(),
            'taille_totale' => Sauvegarde::where('statut', Sauvegarde::STATUT_TERMINEE)->sum('taille_octets'),
            'derniere'   => Sauvegarde::where('statut', Sauvegarde::STATUT_TERMINEE)->latest()->first(),
        ];

        return view('sauvegardes.index', compact('sauvegardes', 'stats'));
    }

    // =========================================================================
    // CRÉER UNE SAUVEGARDE MANUELLE
    // =========================================================================

    public function store(Request $request)
    {
        $this->autoriserSuperAdmin();

        DB::beginTransaction();
        try {
            $nomFichier    = 'saoudy_backup_' . now()->format('Y-m-d_H-i-s') . '_' . Str::random(6) . '.sql';
            $dossier       = 'sauvegardes';
            $cheminRelatif = "{$dossier}/{$nomFichier}";
            $cheminAbsolu  = storage_path("app/{$cheminRelatif}");

            // Créer le dossier si nécessaire
            Storage::makeDirectory($dossier);

            // Enregistrer la sauvegarde en base (statut : en_cours)
            $sauvegarde = Sauvegarde::create([
                'nom_fichier'     => $nomFichier,
                'chemin_stockage' => $cheminRelatif,
                'type'            => Sauvegarde::TYPE_MANUELLE,
                'statut'          => Sauvegarde::STATUT_EN_COURS,
                'creee_par_id'    => auth()->id(),
                'ip_declencheur'  => $request->ip(),
                'expire_a'        => now()->addDays(Sauvegarde::RETENTION_JOURS_DEFAUT),
            ]);

            DB::commit();

            // Lancer le dump PostgreSQL
            $this->executerDump($sauvegarde, $cheminAbsolu);

            // Notifier les super administrateurs
            $this->notifierSuperAdmins($sauvegarde->fresh());

            return redirect()
                ->route('sauvegardes.index')
                ->with('success', "Sauvegarde « {$nomFichier} » créée avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌ Erreur création sauvegarde', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Notifier les super admins de l'échec si la sauvegarde a été créée en base
            if (isset($sauvegarde)) {
                $this->notifierSuperAdmins($sauvegarde->fresh());
            }

            return back()->with('error', 'Erreur lors de la création de la sauvegarde : ' . $e->getMessage());
        }
    }

    // =========================================================================
    // NOTIFIER LES SUPER ADMINISTRATEURS
    // =========================================================================

    private function notifierSuperAdmins(Sauvegarde $sauvegarde): void
    {
        $superAdmins = User::whereHas('role', function ($q) {
            $q->whereIn('roles.slug', ['super-administrateur', 'administrateur']);
        })->get();


        if ($superAdmins->isEmpty()) {
            Log::warning('Sauvegarde : aucun super-admin trouvé pour la notification e-mail.');
            return;
        }

        $mailable = new SauvegardeNotification($sauvegarde);

        foreach ($superAdmins as $admin) {
            try {
                Mail::to($admin->email)->send($mailable);
            } catch (\Exception $e) {
                dd($e->getMessage());
                Log::error('❌ Échec envoi e-mail sauvegarde (manuelle)', [
                    'destinataire' => $admin->email,
                    'error'        => $e->getMessage(),
                ]);
            }
        }

        Log::info('📧 Notification sauvegarde manuelle envoyée aux super-admins.', [
            'sauvegarde' => $sauvegarde->nom_fichier,
            'destinataires' => $superAdmins->pluck('email'),
        ]);
    }

    // =========================================================================
    // TÉLÉCHARGER
    // =========================================================================

    public function download(Sauvegarde $sauvegarde)
    {
        $this->autoriserSuperAdmin();

        if ($sauvegarde->statut !== Sauvegarde::STATUT_TERMINEE) {
            return back()->with('error', 'Seules les sauvegardes terminées peuvent être téléchargées.');
        }

        if (!Storage::exists($sauvegarde->chemin_stockage)) {
            return back()->with('error', 'Le fichier de sauvegarde est introuvable sur le disque.');
        }

        Log::info('⬇️ Téléchargement sauvegarde', [
            'sauvegarde_id' => $sauvegarde->id,
            'nom_fichier'   => $sauvegarde->nom_fichier,
            'user_id'       => auth()->id(),
        ]);

        return Storage::download($sauvegarde->chemin_stockage, $sauvegarde->nom_fichier);
    }

    // =========================================================================
    // SUPPRIMER
    // =========================================================================

    public function destroy(Sauvegarde $sauvegarde)
    {
        $this->autoriserSuperAdmin();

        try {
            // Supprimer le fichier physique
            if (Storage::exists($sauvegarde->chemin_stockage)) {
                Storage::delete($sauvegarde->chemin_stockage);
            }

            $nomFichier = $sauvegarde->nom_fichier;
            $sauvegarde->delete();

            Log::info('🗑️ Sauvegarde supprimée', [
                'nom_fichier' => $nomFichier,
                'user_id'     => auth()->id(),
            ]);

            return back()->with('success', "Sauvegarde « {$nomFichier} » supprimée.");
        } catch (\Exception $e) {
            Log::error('❌ Erreur suppression sauvegarde', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    // =========================================================================
    // PURGER LES SAUVEGARDES EXPIRÉES
    // =========================================================================

    public function purger(Request $request)
    {
        $this->autoriserSuperAdmin();

        try {
            $expirees = Sauvegarde::expirees()->get();
            $compte   = 0;

            foreach ($expirees as $sauvegarde) {
                if (Storage::exists($sauvegarde->chemin_stockage)) {
                    Storage::delete($sauvegarde->chemin_stockage);
                }
                $sauvegarde->delete();
                $compte++;
            }

            Log::info("🧹 Purge sauvegardes expirées : {$compte} supprimée(s)");

            return back()->with('success', "{$compte} sauvegarde(s) expirée(s) purgée(s).");
        } catch (\Exception $e) {
            Log::error('❌ Erreur purge sauvegardes', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erreur lors de la purge.');
        }
    }

    // =========================================================================
    // VÉRIFIER INTÉGRITÉ
    // =========================================================================

    public function verifier(Sauvegarde $sauvegarde)
    {
        $this->autoriserSuperAdmin();

        if (!Storage::exists($sauvegarde->chemin_stockage)) {
            return response()->json([
                'valide'   => false,
                'message'  => 'Fichier introuvable sur le disque.',
            ]);
        }

        $cheminAbsolu  = storage_path('app/' . $sauvegarde->chemin_stockage);
        $checksumActuel = md5_file($cheminAbsolu);
        $valide         = $checksumActuel === $sauvegarde->checksum_md5;

        return response()->json([
            'valide'          => $valide,
            'checksum_stocke' => $sauvegarde->checksum_md5,
            'checksum_actuel' => $checksumActuel,
            'message'         => $valide
                ? 'Intégrité vérifiée — fichier intact.'
                : 'Attention : le checksum ne correspond pas (fichier potentiellement corrompu).',
        ]);
    } 

    // =========================================================================
    // DUMP PostgreSQL (méthode privée)
    // =========================================================================

    private function executerDump(Sauvegarde $sauvegarde, string $cheminAbsolu): void
    {
        $config = config('database.connections.pgsql');
        $pgDump = $this->trouverPgDump();

        $host     = $config['host']     ?? '127.0.0.1';
        $port     = $config['port']     ?? '5432';
        $username = $config['username'] ?? 'postgres';
        $password = $config['password'] ?? '';
        $database = $config['database'];

        // Chemin fichier : séparateurs Unix pour pg_dump
        $cheminFichier = str_replace('\\', '/', $cheminAbsolu);

        // ✅ Fichier pgpass dans storage/app (garanti accessible par le process PHP)
        $pgpassFichier = $this->creerFichierPgpass($host, $port, $database, $username, $password);

        // Debug : log le chemin réel utilisé
        Log::debug('🔑 pgpass écrit', [
            'chemin'     => $pgpassFichier,
            'existe'     => file_exists($pgpassFichier),
            'contenu_ok' => filesize($pgpassFichier) > 0,
            'APPDATA'    => getenv('APPDATA') ?: 'NON DÉFINI',
        ]);

        $commande = [
            $pgDump,
            "--host={$host}",
            "--port={$port}",
            "--username={$username}",
            '--format=plain',
            '--no-password',
            "--file={$cheminFichier}",
            $database,
        ];

        // ✅ Construire un environnement propre pour le sous-processus
        // Inclure les variables système essentielles + PGPASSFILE
        $env = [
            'PGPASSFILE'  => str_replace('\\', '/', $pgpassFichier),
            'PGPASSWORD'  => '',             // neutraliser si existant
            'SYSTEMROOT'  => getenv('SYSTEMROOT')  ?: 'C:\\Windows',
            'SYSTEMDRIVE' => getenv('SYSTEMDRIVE') ?: 'C:',
            'TEMP'        => getenv('TEMP')        ?: sys_get_temp_dir(),
            'TMP'         => getenv('TMP')         ?: sys_get_temp_dir(),
            'USERNAME'    => getenv('USERNAME')    ?: 'SYSTEM',
            'USERPROFILE' => getenv('USERPROFILE') ?: 'C:\\Windows\\System32\\config\\systemprofile',
            'APPDATA'     => getenv('APPDATA')     ?: getenv('USERPROFILE') . '\\AppData\\Roaming',
            'PATH'        => getenv('PATH')        ?: '',
        ];

        $process = new Process($commande, null, $env);
        $process->setTimeout(300);

        try {
            $process->mustRun();

            $taille   = filesize($cheminAbsolu);
            $checksum = md5_file($cheminAbsolu);

            $sauvegarde->update([
                'statut'        => Sauvegarde::STATUT_TERMINEE,
                'taille_octets' => $taille,
                'checksum_md5'  => $checksum,
            ]);

            Log::info('✅ Sauvegarde PostgreSQL terminée', [
                'fichier'  => $sauvegarde->nom_fichier,
                'taille'   => $taille,
                'checksum' => $checksum,
            ]);
        } catch (\Exception $e) {
            $messageErreur = $this->nettoyerUtf8($e->getMessage());
            $messageErreur = str_replace($password, '***', $messageErreur);

            $sauvegarde->update([
                'statut'         => Sauvegarde::STATUT_ECHEC,
                'message_erreur' => $messageErreur,
            ]);

            Log::error('❌ Échec sauvegarde PostgreSQL', [
                'fichier'      => $sauvegarde->nom_fichier,
                'error'        => $messageErreur,
                'pgpass_existe' => file_exists($pgpassFichier),
            ]);

            throw new \Exception($messageErreur);
        } finally {
            if (!empty($pgpassFichier) && file_exists($pgpassFichier)) {
                @unlink($pgpassFichier);
            }
        }
    }


    private function creerFichierPgpass(
        string $host,
        string $port,
        string $database,
        string $username,
        string $password
    ): string {
        // ✅ Utiliser storage/app/sauvegardes — accessible par le process PHP
        $dossier = storage_path('app/sauvegardes');
        if (!is_dir($dossier)) {
            mkdir($dossier, 0700, true);
        }

        $fichier = $dossier . DIRECTORY_SEPARATOR . '.pgpass_' . uniqid() . '.conf';

        // Échapper uniquement \ et : (format pgpass)
        $passwordEscape = str_replace(['\\', ':'], ['\\\\', '\\:'], $password);
        $contenu = "{$host}:{$port}:{$database}:{$username}:{$passwordEscape}";

        file_put_contents($fichier, $contenu . PHP_EOL);
        chmod($fichier, 0600);

        return $fichier;
    }

    /**
     * Trouver l'exécutable pg_dump selon l'OS et la config.
     */
    private function trouverPgDump(): string
    {
        // 1. Priorité absolue : chemin dans .env
        $cheminEnv = env('PGSQL_BIN_PATH');
        if ($cheminEnv) {
            $candidat = rtrim($cheminEnv, '/\\') . DIRECTORY_SEPARATOR . 'pg_dump'
                . (PHP_OS_FAMILY === 'Windows' ? '.exe' : '');
            if (file_exists($candidat)) {
                return $candidat;
            }
            // .env défini mais chemin invalide → exception explicite
            throw new \RuntimeException(
                "PGSQL_BIN_PATH défini dans .env mais pg_dump introuvable : {$candidat}"
            );
        }

        // 2. Windows : scan dynamique de Program Files
        if (PHP_OS_FAMILY === 'Windows') {
            $racines = [
                'C:\\Program Files\\PostgreSQL',
                'C:\\Program Files (x86)\\PostgreSQL',
            ];

            foreach ($racines as $racine) {
                if (!is_dir($racine)) {
                    continue;
                }

                // Lister toutes les versions installées (dossiers numériques)
                $versions = array_filter(
                    scandir($racine),
                    fn($entry) => is_dir("{$racine}\\{$entry}") && is_numeric($entry)
                );

                // Trier par version décroissante (17 > 16 > 15…)
                rsort($versions);

                foreach ($versions as $version) {
                    $candidat = "{$racine}\\{$version}\\bin\\pg_dump.exe";
                    if (file_exists($candidat)) {
                        Log::info("✅ pg_dump trouvé : {$candidat}");
                        return $candidat;
                    }
                }
            }

            throw new \RuntimeException(
                "pg_dump introuvable. Ajoutez PGSQL_BIN_PATH dans votre .env.\n"
                    . "Exemple : PGSQL_BIN_PATH=\"C:\\Program Files\\PostgreSQL\\17\\bin\""
            );
        }

        // 3. Linux / Mac : vérifier que pg_dump est accessible dans le PATH
        $which = shell_exec('which pg_dump 2>/dev/null');
        if ($which && file_exists(trim($which))) {
            return trim($which);
        }

        return 'pg_dump';
    }


    /**
     * Supprimer les octets invalides UTF-8 d'une chaîne (ex: encodage Windows CP1252).
     */
    private function nettoyerUtf8(string $texte): string
    {
        // Tenter une conversion depuis CP1252 si disponible
        if (function_exists('mb_convert_encoding')) {
            $converti = @mb_convert_encoding($texte, 'UTF-8', 'Windows-1252');
            if ($converti !== false && mb_check_encoding($converti, 'UTF-8')) {
                return $converti;
            }
        }

        // Fallback : supprimer les octets non-UTF8
        return mb_convert_encoding($texte, 'UTF-8', 'UTF-8');
    }
}
