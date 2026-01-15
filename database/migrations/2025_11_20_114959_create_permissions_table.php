<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            // Clé primaire
            $table->uuid('id')->primary();

            // Informations de base
            $table->string('name', 100)->comment('Nom affiché de la permission');
            $table->string('slug', 100)->unique()->comment('Identifiant unique pour la permission');
            $table->text('description')->comment('Description détaillée de la permission');

            // Informations de ressource et action
            $table->string('resource', 100)->comment('Entité/ressource concernée (ex: users, roles, appels_offres)');

            /**
             * Actions adaptées selon les spécifications du module de gestion des permissions
             * Référence: MODULES_DE_GESTION_DES_PERMISSIONS_ET_ACCES.docx
             */
            $table->enum('action', [
                // === Actions CRUD de base ===
                'create',           // Créer ou ajouter
                'read',             // Voir ou lire la liste
                'view-details',     // Voir les détails
                'update',           // Modifier ou mettre à jour
                'delete',           // Supprimer temporairement
                'force-delete',     // Supprimer définitivement

                // === Actions de duplication et versioning ===
                'duplicate',        // Dupliquer
                'create-version',   // Créer nouvelle version (pro-forma, etc.)

                // === Actions d'état et activation ===
                'activate',         // Activer
                'deactivate',       // Désactiver
                'toggle-status',    // Activer/Désactiver (toggle)

                // === Actions de corbeille et restauration ===
                'view-trash',       // Voir dans la corbeille
                'restore',          // Restaurer les éléments supprimés

                // === Actions d'historique ===
                'view-history',     // Voir les historiques

                // === Actions de validation et workflow ===
                'validate',         // Valider
                'reject',           // Rejeter
                'cancel',           // Annuler
                'pending',          // Remettre en attente
                'process',          // Traiter (paiements)
                'confirm',          // Confirmer (paiements traités)
                'complete',         // Terminer (évaluations)
                'resume',           // Reprendre (évaluations)

                // === Actions d'attribution et gestion ===
                'manage',           // Gestion complète (permissions des rôles)
                'assign',           // Attribuer (lots aux prestataires)
                'reassign',         // Réattribuer
                'withdraw',         // Retirer (attributions)
                'suspend',          // Suspendre (attributions)

                // === Actions d'évaluation ===
                'evaluate',         // Évaluer (attributions de lots)

                // === Actions d'import/export ===
                'export',           // Exporter
                'import',           // Importer
                'download',         // Télécharger (documents)
            ])->comment('Action autorisée sur la ressource');

            // Métadonnées de sécurité et groupement
            $table->string('guard_name', 50)->default('web')->comment('Guard utilisé (web, api, etc.)');
            $table->string('category', 100)->comment('Catégorie de permission pour groupement');
            $table->string('module', 100)->nullable()->comment('Module fonctionnel (roles, users, appels_offres, lots, prestataires, factures, paiements)');
            $table->unsignedTinyInteger('priority')->default(0)->comment('Priorité de la permission (0-255)');
            $table->unsignedSmallInteger('display_order')->default(0)->comment('Ordre d\'affichage dans les listes');

            // États et conditions
            $table->boolean('is_active')->default(true)->comment('Permission active/inactive');
            $table->boolean('is_system')->default(false)->comment('Permission système (non modifiable)');
            $table->boolean('requires_confirmation')->default(false)->comment('Action nécessitant une confirmation');
            $table->json('conditions')->nullable()->comment('Conditions supplémentaires en JSON');
            $table->json('dependencies')->nullable()->comment('Permissions dépendantes requises');

            // Audit et traçabilité
            $table->uuid('created_by')->nullable()->comment('Utilisateur qui a créé la permission');
            $table->uuid('updated_by')->nullable()->comment('Dernier utilisateur ayant modifié');
            $table->timestamp('last_used_at')->nullable()->comment('Dernière utilisation de la permission');

            // Timestamps standards
            $table->timestamps();
            $table->softDeletes();

            // Index pour les performances
            $table->index(['resource', 'action'], 'idx_permissions_resource_action');
            $table->index(['guard_name', 'is_active'], 'idx_permissions_guard_active');
            $table->index('category', 'idx_permissions_category');
            $table->index('module', 'idx_permissions_module');
            $table->index('slug', 'idx_permissions_slug');
            $table->index(['is_system', 'is_active'], 'idx_permissions_system_active');

            // Index composé pour les requêtes fréquentes
            $table->index(['resource', 'action', 'guard_name', 'is_active'], 'idx_permissions_complete');
            $table->index(['module', 'category', 'is_active'], 'idx_permissions_module_category');

            // Contraintes
            $table->unique(['slug', 'guard_name'], 'unique_permission_per_guard');
            $table->unique(['resource', 'action', 'guard_name'], 'unique_resource_action_guard');

            // Relations avec les utilisateurs pour l'audit
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

        // Commentaire sur la table (PostgreSQL)
        DB::statement("COMMENT ON TABLE permissions IS 'Table des permissions du système de contrôle d''accès - Module RBAC';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
