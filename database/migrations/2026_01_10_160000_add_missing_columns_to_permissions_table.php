<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ajoute les colonnes manquantes à la table permissions existante.
     */
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            // Vérifier et ajouter les colonnes manquantes
            if (!Schema::hasColumn('permissions', 'module')) {
                $table->string('module', 100)->nullable()->after('category')->comment('Module fonctionnel');
            }

            if (!Schema::hasColumn('permissions', 'priority')) {
                $table->unsignedTinyInteger('priority')->default(0)->after('module')->comment('Priorité de la permission (0-255)');
            }

            if (!Schema::hasColumn('permissions', 'display_order')) {
                $table->unsignedSmallInteger('display_order')->default(0)->after('priority')->comment('Ordre d\'affichage dans les listes');
            }

            if (!Schema::hasColumn('permissions', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('display_order')->comment('Permission active/inactive');
            }

            if (!Schema::hasColumn('permissions', 'is_system')) {
                $table->boolean('is_system')->default(false)->after('is_active')->comment('Permission système (non modifiable)');
            }

            if (!Schema::hasColumn('permissions', 'requires_confirmation')) {
                $table->boolean('requires_confirmation')->default(false)->after('is_system')->comment('Action nécessitant une confirmation');
            }

            if (!Schema::hasColumn('permissions', 'conditions')) {
                $table->json('conditions')->nullable()->after('requires_confirmation')->comment('Conditions supplémentaires en JSON');
            }

            if (!Schema::hasColumn('permissions', 'dependencies')) {
                $table->json('dependencies')->nullable()->after('conditions')->comment('Permissions dépendantes requises');
            }

            if (!Schema::hasColumn('permissions', 'created_by')) {
                $table->uuid('created_by')->nullable()->after('dependencies')->comment('Utilisateur qui a créé la permission');
            }

            if (!Schema::hasColumn('permissions', 'updated_by')) {
                $table->uuid('updated_by')->nullable()->after('created_by')->comment('Dernier utilisateur ayant modifié');
            }

            if (!Schema::hasColumn('permissions', 'last_used_at')) {
                $table->timestamp('last_used_at')->nullable()->after('updated_by')->comment('Dernière utilisation de la permission');
            }
        });

        // Ajouter l'index sur module (méthode compatible Laravel 11 sans Doctrine)
        $this->addIndexIfNotExists('permissions', 'module', 'idx_permissions_module');
    }

    /**
     * Vérifier si un index existe (compatible PostgreSQL et MySQL)
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            $result = DB::select("
                SELECT indexname
                FROM pg_indexes
                WHERE tablename = ? AND indexname = ?
            ", [$table, $indexName]);
        } elseif ($driver === 'mysql') {
            $result = DB::select("
                SHOW INDEX FROM {$table} WHERE Key_name = ?
            ", [$indexName]);
        } else {
            // SQLite ou autre - on suppose que l'index n'existe pas
            return false;
        }

        return count($result) > 0;
    }

    /**
     * Ajouter un index s'il n'existe pas
     */
    private function addIndexIfNotExists(string $table, string $column, string $indexName): void
    {
        if (!$this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $table) use ($column, $indexName) {
                $table->index($column, $indexName);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer l'index d'abord
        if ($this->indexExists('permissions', 'idx_permissions_module')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->dropIndex('idx_permissions_module');
            });
        }

        // Supprimer les colonnes
        Schema::table('permissions', function (Blueprint $table) {
            $columns = [
                'module', 'priority', 'display_order', 'is_active', 'is_system',
                'requires_confirmation', 'conditions', 'dependencies',
                'created_by', 'updated_by', 'last_used_at'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('permissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
