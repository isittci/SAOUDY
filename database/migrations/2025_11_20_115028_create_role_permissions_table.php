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
        Schema::create('role_permissions', function (Blueprint $table) {
            // Relations principales (table pivot)
            $table->uuid('role_id')->comment('ID du rôle');
            $table->uuid('permission_id')->comment('ID de la permission');

            // Métadonnées d'attribution
            $table->uuid('attribue_par')->nullable()->comment('ID de l\'utilisateur qui a attribué cette permission au rôle');
            $table->timestamp('attribue_le')->useCurrent()->comment('Date et heure d\'attribution de la permission');
            $table->timestamp('expire_le')->nullable()->comment('Date d\'expiration (pour permissions temporaires)');

            // État
            $table->boolean('actif')->default(true)->comment('Permission active pour ce rôle');

            // Conditions spécifiques
            $table->json('conditions')->nullable()->comment('Conditions spécifiques pour cette attribution');
            $table->text('notes')->nullable()->comment('Notes sur l\'attribution de cette permission');

            $table->foreignUuid('created_by')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('updated_by')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->foreignUuid('deleted_by')->nullable()->references('id')->on('users')->onDelete('set null');

            // Timestamps
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable();

            // Soft delete
            $table->softDeletes();

            // Clé primaire composée
            $table->primary(['role_id', 'permission_id'], 'pk_role_permissions');

            // Contraintes de clés étrangères
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->foreign('attribue_par')->references('id')->on('users')->onDelete('set null');

            // Index pour les performances
            $table->index(['role_id', 'actif'], 'idx_role_permissions_role_actif');
            $table->index(['permission_id', 'actif'], 'idx_role_permissions_perm_actif');
            $table->index('attribue_par', 'idx_role_permissions_attribue_par');
            $table->index('expire_le', 'idx_role_permissions_expiration');
            $table->index(['actif', 'deleted_at'], 'idx_role_permissions_actif_deleted');
        });

        // Commentaire sur la table
        DB::statement("COMMENT ON TABLE role_permissions IS 'Table pivot : association entre rôles et permissions';");

        // Vue pour les permissions actives par rôle
        DB::statement("
            CREATE VIEW role_permissions_actifs AS
            SELECT
                rp.role_id,
                rp.permission_id,
                rp.attribue_le,
                rp.expire_le,
                r.name AS nom_role,
                r.slug AS code_role,
                r.level AS niveau_role,
                p.name AS nom_permission,
                p.slug AS code_permission,
                p.resource,
                p.action,
                p.category AS categorie_permission
            FROM role_permissions rp
            INNER JOIN roles r ON rp.role_id = r.id
            INNER JOIN permissions p ON rp.permission_id = p.id
            WHERE rp.actif = true
                AND rp.deleted_at IS NULL
                AND r.deleted_at IS NULL
                AND p.is_active = true
                AND p.deleted_at IS NULL
                AND (rp.expire_le IS NULL OR rp.expire_le > NOW())
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Suppression de la vue
        DB::statement("DROP VIEW IF EXISTS role_permissions_actifs");

        // Suppression de la table
        Schema::dropIfExists('role_permissions');
    }
};
