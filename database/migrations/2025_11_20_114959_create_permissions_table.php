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
            $table->text('description')->nullable()->comment('Description détaillée de la permission');

            // Informations de ressource et action
            $table->string('resource', 100)->nullable()->comment('Entité/ressource concernée (ex: users, posts)');
            $table->enum('action', [
                'create',    // Créer
                'read',      // Lire/consulter
                'update',    // Modifier
                'delete',    // Supprimer
                'export',    // Exporter
                'import',    // Importer
                'validate',  // Valider
                'reject',    // Rejeter
                'restore',   // Restaurer
                'manage',    // Gestion complète
                'download',  // Télécharger
                'duplicate' // Dupliquer
            ])->comment('Action autorisée sur la ressource');

            // Métadonnées de sécurité et groupement
            $table->string('guard_name', 50)->default('web')->comment('Guard utilisé (web, api, etc.)');
            $table->string('category', 100)->nullable()->comment('Catégorie de permission pour groupement');
            $table->unsignedTinyInteger('priority')->default(0)->comment('Priorité de la permission (0-255)');

            // États et conditions
            $table->boolean('is_active')->default(true)->comment('Permission active/inactive');
            $table->boolean('is_system')->default(false)->comment('Permission système (non modifiable)');
            $table->json('conditions')->nullable()->comment('Conditions supplémentaires en JSON');

            // Audit et traçabilité
            $table->uuid('created_by')->nullable()->comment('Membres qui a créé la permission');
            $table->uuid('updated_by')->nullable()->comment('Dernier membres ayant modifié');
            $table->timestamp('last_used_at')->nullable()->comment('Dernière utilisation de la permission');

            // Timestamps standards
            $table->timestamps();
            $table->softDeletes();

            // Index pour les performances
            $table->index(['resource', 'action'], 'idx_permissions_resource_action');
            $table->index(['guard_name', 'is_active'], 'idx_permissions_guard_active');
            $table->index('category', 'idx_permissions_category');
            $table->index('slug', 'idx_permissions_slug');
            $table->index(['is_system', 'is_active'], 'idx_permissions_system_active');

            // Index composé pour les requêtes fréquentes
            $table->index(['resource', 'action', 'guard_name', 'is_active'], 'idx_permissions_complete');

            // Contraintes
            $table->unique(['slug', 'guard_name'], 'unique_permission_per_guard');

            // Relations avec les membres pour l'audit
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

        // Ajout de commentaires sur la table
        // DB::statement("ALTER TABLE permissions COMMENT = 'Table des permissions du système de contrôle d\'accès'");
         DB::statement("COMMENT ON TABLE permissions IS 'Table des permissions du système de contrôle daccès';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
