<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Crée les rôles par défaut du système:
     * - Super Administrateur (niveau 100): Tous les droits sans exception
     * - Administrateur (niveau 80): Tous les droits SAUF sur le Super Admin
     */
    public function run(): void
    {
        DB::transaction(function () {
            // =====================================================
            // RÔLE SUPER ADMINISTRATEUR (Niveau 100)
            // =====================================================
            $superAdmin = Role::updateOrCreate(
                ['slug' => 'super-administrateur'],
                [
                    'name' => 'Super Administrateur',
                    'description' => 'Accès complet et illimité à toutes les fonctionnalités du système. Ce rôle ne peut être ni modifié ni supprimé. Le Super Administrateur a tous les droits sans aucune exception et peut gérer tous les utilisateurs y compris les autres Super Administrateurs.',
                    'level' => Role::LEVEL_SUPER_ADMIN, // 100
                    'is_system_role' => true,
                ]
            );

            // Le Super Admin a TOUTES les permissions automatiquement
            // via le trait HasPermissions (bypass dans hasPermission())
            // Mais on lui attribue quand même toutes les permissions pour la cohérence
            $allPermissions = Permission::where('is_active', true)->pluck('id')->toArray();
            $superAdmin->syncPermissions($allPermissions);

            $this->command->info('✅ Rôle Super Administrateur créé avec ' . count($allPermissions) . ' permissions');

            // =====================================================
            // RÔLE ADMINISTRATEUR (Niveau 80)
            // =====================================================
            $admin = Role::updateOrCreate(
                ['slug' => 'administrateur'],
                [
                    'name' => 'Administrateur',
                    'description' => 'Accès étendu aux fonctionnalités d\'administration. L\'Administrateur peut gérer les utilisateurs, rôles et permissions mais ne peut PAS modifier, supprimer ou voir les informations des Super Administrateurs. Il peut uniquement gérer les utilisateurs de niveau inférieur au sien.',
                    'level' => Role::LEVEL_ADMIN, // 80
                    'is_system_role' => true,
                ]
            );

            // L'Administrateur a toutes les permissions SAUF celles qui permettraient
            // d'agir sur les Super Administrateurs (géré par la logique métier dans les controllers)
            $adminPermissions = Permission::where('is_active', true)->pluck('id')->toArray();
            $admin->syncPermissions($adminPermissions);

            $this->command->info('✅ Rôle Administrateur créé avec ' . count($adminPermissions) . ' permissions');

            // =====================================================
            // RÔLE MANAGER (Niveau 60) - Optionnel
            // =====================================================
            $manager = Role::updateOrCreate(
                ['slug' => 'manager'],
                [
                    'name' => 'Manager',
                    'description' => 'Responsable de la gestion opérationnelle. Peut gérer les appels d\'offres, lots, prestataires et évaluations. Accès limité aux fonctions d\'administration.',
                    'level' => Role::LEVEL_MANAGER, // 60
                    'is_system_role' => false,
                ]
            );

            // Permissions du Manager (exclut l'administration des rôles et utilisateurs)
            $managerPermissions = Permission::where('is_active', true)
                ->where(function ($query) {
                    $query->whereNotIn('resource', ['roles', 'role_permissions'])
                        ->orWhere('action', 'read')
                        ->orWhere('action', 'view-details');
                })
                ->pluck('id')
                ->toArray();

            $manager->syncPermissions($managerPermissions);

            $this->command->info('✅ Rôle Manager créé avec ' . count($managerPermissions) . ' permissions');

            // =====================================================
            // RÔLE UTILISATEUR (Niveau 20) - Optionnel
            // =====================================================
            $user = Role::updateOrCreate(
                ['slug' => 'utilisateur'],
                [
                    'name' => 'Utilisateur',
                    'description' => 'Utilisateur standard avec accès en lecture aux principales fonctionnalités. Peut consulter les appels d\'offres, lots et prestataires.',
                    'level' => Role::LEVEL_USER, // 20
                    'is_system_role' => false,
                ]
            );

            // Permissions de l'Utilisateur (lecture seule sur les modules métier)
            $userPermissions = Permission::where('is_active', true)
                ->whereIn('action', ['read', 'view-details', 'download'])
                ->whereNotIn('resource', ['roles', 'role_permissions', 'users'])
                ->pluck('id')
                ->toArray();

            $user->syncPermissions($userPermissions);

            $this->command->info('✅ Rôle Utilisateur créé avec ' . count($userPermissions) . ' permissions');
        });

        $this->command->info('');
        $this->command->info('📋 Récapitulatif des rôles créés:');
        $this->command->table(
            ['Rôle', 'Slug', 'Niveau', 'Système', 'Permissions'],
            Role::orderBy('level', 'desc')->get()->map(function ($role) {
                return [
                    $role->name,
                    $role->slug,
                    $role->level . ' (' . $role->level_label . ')',
                    $role->is_system_role ? 'Oui' : 'Non',
                    $role->permissions()->count(),
                ];
            })->toArray()
        );
    }
}
