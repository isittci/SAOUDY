<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Génère toutes les permissions du système selon les spécifications
     * du module de gestion des permissions et accès.
     */
    public function run(): void
    {
        // Désactiver les événements pour accélérer l'insertion
        Permission::unsetEventDispatcher();

        $permissions = $this->getPermissions();
        $displayOrder = 0;

        foreach ($permissions as $permission) {
            $displayOrder++;

            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                array_merge($permission, [
                    'display_order' => $displayOrder,
                    'guard_name' => 'web',
                    'is_active' => true,
                    'is_system' => true,
                    'created_by' => null,
                ])
            );
        }

        $this->command->info('✅ ' . count($permissions) . ' permissions créées avec succès.');
    }

    /**
     * Retourne la liste complète des permissions du système.
     */
    private function getPermissions(): array
    {
        return array_merge(
            $this->getAdministrationPermissions(),
            $this->getTypeAppelsOffresPermissions(),  // ← NOUVEAU MODULE
            $this->getAppelsOffresPermissions(),
            $this->getLotsEvaluationsPermissions(),
            $this->getProformaPermissions(),
            $this->getPrestatairesPermissions(),
            $this->getAttributionsPermissions(),
            $this->getFacturationPaiementsPermissions()
        );
    }


    /**

     *
     * À ajouter dans PermissionsSeeder.php
     */
    private function getTypeAppelsOffresPermissions(): array
    {
        return [
            // === TYPES D'APPELS D'OFFRES ===
            [
                'name' => 'Voir les types d\'appels d\'offres',
                'slug' => 'type_appels_offres.read',
                'description' => 'Permet de consulter la liste des types d\'appels d\'offres',
                'resource' => 'type_appels_offres',
                'action' => 'read',
                'category' => 'Appels d\'offres',
                'module' => 'Types d\'appels d\'offres',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'un type',
                'slug' => 'type_appels_offres.view-details',
                'description' => 'Permet de consulter les détails complets d\'un type d\'appel d\'offres',
                'resource' => 'type_appels_offres',
                'action' => 'view-details',
                'category' => 'Appels d\'offres',
                'module' => 'Types d\'appels d\'offres',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Créer un type d\'appel d\'offres',
                'slug' => 'type_appels_offres.create',
                'description' => 'Permet de créer un nouveau type d\'appel d\'offres dans le système',
                'resource' => 'type_appels_offres',
                'action' => 'create',
                'category' => 'Appels d\'offres',
                'module' => 'Types d\'appels d\'offres',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier un type d\'appel d\'offres',
                'slug' => 'type_appels_offres.update',
                'description' => 'Permet de modifier les informations d\'un type d\'appel d\'offres existant',
                'resource' => 'type_appels_offres',
                'action' => 'update',
                'category' => 'Appels d\'offres',
                'module' => 'Types d\'appels d\'offres',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Supprimer un type d\'appel d\'offres',
                'slug' => 'type_appels_offres.delete',
                'description' => 'Permet de supprimer un type d\'appel d\'offres du système',
                'resource' => 'type_appels_offres',
                'action' => 'delete',
                'category' => 'Appels d\'offres',
                'module' => 'Types d\'appels d\'offres',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Activer/Désactiver un type',
                'slug' => 'type_appels_offres.toggle-status',
                'description' => 'Permet d\'activer ou désactiver un type d\'appel d\'offres',
                'resource' => 'type_appels_offres',
                'action' => 'toggle-status',
                'category' => 'Appels d\'offres',
                'module' => 'Types d\'appels d\'offres',
                'requires_confirmation' => false,
            ],


            // === DOCUMENTS DES LOTS ===
            [
                'name' => 'Voir les documents des lots',
                'slug' => 'documents_lots.read',
                'description' => 'Permet de consulter la liste des documents liés aux lots',
                'resource' => 'documents_lots',
                'action' => 'read',
                'category' => 'Lots et Évaluations',
                'module' => 'Documents',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'un document',
                'slug' => 'documents_lots.view-details',
                'description' => 'Permet de consulter les détails complets d\'un document',
                'resource' => 'documents_lots',
                'action' => 'view-details',
                'category' => 'Lots et Évaluations',
                'module' => 'Documents',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Créer un document',
                'slug' => 'documents_lots.create',
                'description' => 'Permet d\'ajouter un nouveau document à un lot',
                'resource' => 'documents_lots',
                'action' => 'create',
                'category' => 'Lots et Évaluations',
                'module' => 'Documents',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier un document',
                'slug' => 'documents_lots.update',
                'description' => 'Permet de modifier les informations d\'un document',
                'resource' => 'documents_lots',
                'action' => 'update',
                'category' => 'Lots et Évaluations',
                'module' => 'Documents',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Supprimer un document',
                'slug' => 'documents_lots.delete',
                'description' => 'Permet de supprimer un document du système',
                'resource' => 'documents_lots',
                'action' => 'delete',
                'category' => 'Lots et Évaluations',
                'module' => 'Documents',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Valider un document',
                'slug' => 'documents_lots.validate',
                'description' => 'Permet de valider un document pour approbation',
                'resource' => 'documents_lots',
                'action' => 'validate',
                'category' => 'Lots et Évaluations',
                'module' => 'Documents',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Annuler la validation',
                'slug' => 'documents_lots.cancel',
                'description' => 'Permet d\'annuler la validation d\'un document',
                'resource' => 'documents_lots',
                'action' => 'cancel',
                'category' => 'Lots et Évaluations',
                'module' => 'Documents',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Télécharger un document',
                'slug' => 'documents_lots.download',
                'description' => 'Permet de télécharger un document',
                'resource' => 'documents_lots',
                'action' => 'download',
                'category' => 'Lots et Évaluations',
                'module' => 'Documents',
                'requires_confirmation' => false,
            ],

            [
                'name' => 'Activer/Désactiver un document',
                'slug' => 'documents_lots.toggle-status',
                'description' => 'Permet d\'activer ou désactiver un document',
                'resource' => 'documents_lots',
                'action' => 'toggle-status',
                'category' => 'Lots et Évaluations',
                'module' => 'Documents',
                'requires_confirmation' => false,
            ],
        ];
    }









    /**
     * Permissions d'administration (rôles, permissions, utilisateurs).
     */
    private function getAdministrationPermissions(): array
    {
        return [
            // === RÔLES ===
            [
                'name' => 'Voir la liste des rôles',
                'slug' => 'roles.read',
                'description' => 'Permet de consulter la liste de tous les rôles du système',
                'resource' => 'roles',
                'action' => 'read',
                'category' => 'Administration',
                'module' => 'Rôles',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'un rôle',
                'slug' => 'roles.view-details',
                'description' => 'Permet de consulter les détails complets d\'un rôle',
                'resource' => 'roles',
                'action' => 'view-details',
                'category' => 'Administration',
                'module' => 'Rôles',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Créer un rôle',
                'slug' => 'roles.create',
                'description' => 'Permet de créer un nouveau rôle dans le système',
                'resource' => 'roles',
                'action' => 'create',
                'category' => 'Administration',
                'module' => 'Rôles',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier un rôle',
                'slug' => 'roles.update',
                'description' => 'Permet de modifier les informations d\'un rôle existant',
                'resource' => 'roles',
                'action' => 'update',
                'category' => 'Administration',
                'module' => 'Rôles',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Supprimer un rôle',
                'slug' => 'roles.delete',
                'description' => 'Permet de supprimer un rôle du système',
                'resource' => 'roles',
                'action' => 'delete',
                'category' => 'Administration',
                'module' => 'Rôles',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Dupliquer un rôle',
                'slug' => 'roles.duplicate',
                'description' => 'Permet de dupliquer un rôle avec ses permissions',
                'resource' => 'roles',
                'action' => 'duplicate',
                'category' => 'Administration',
                'module' => 'Rôles',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Gérer les permissions d\'un rôle',
                'slug' => 'roles.manage',
                'description' => 'Permet d\'attribuer ou retirer des permissions à un rôle',
                'resource' => 'roles',
                'action' => 'manage',
                'category' => 'Administration',
                'module' => 'Rôles',
                'requires_confirmation' => false,
            ],

            // === PERMISSIONS DES RÔLES ===
            [
                'name' => 'Voir les attributions de permissions',
                'slug' => 'role_permissions.read',
                'description' => 'Permet de consulter la matrice des permissions par rôle',
                'resource' => 'role_permissions',
                'action' => 'read',
                'category' => 'Administration',
                'module' => 'Permissions',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'une attribution',
                'slug' => 'role_permissions.view-details',
                'description' => 'Permet de consulter les détails d\'une attribution de permission',
                'resource' => 'role_permissions',
                'action' => 'view-details',
                'category' => 'Administration',
                'module' => 'Permissions',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier une attribution',
                'slug' => 'role_permissions.update',
                'description' => 'Permet de modifier les paramètres d\'une attribution (expiration, conditions)',
                'resource' => 'role_permissions',
                'action' => 'update',
                'category' => 'Administration',
                'module' => 'Permissions',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Supprimer une attribution',
                'slug' => 'role_permissions.delete',
                'description' => 'Permet de retirer une permission d\'un rôle',
                'resource' => 'role_permissions',
                'action' => 'delete',
                'category' => 'Administration',
                'module' => 'Permissions',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Activer/Désactiver une attribution',
                'slug' => 'role_permissions.toggle-status',
                'description' => 'Permet d\'activer ou désactiver temporairement une attribution',
                'resource' => 'role_permissions',
                'action' => 'toggle-status',
                'category' => 'Administration',
                'module' => 'Permissions',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir l\'historique des attributions',
                'slug' => 'role_permissions.view-history',
                'description' => 'Permet de consulter l\'historique des attributions et expirations',
                'resource' => 'role_permissions',
                'action' => 'view-history',
                'category' => 'Administration',
                'module' => 'Permissions',
                'requires_confirmation' => false,
            ],

            // === UTILISATEURS ===
            [
                'name' => 'Voir la liste des utilisateurs',
                'slug' => 'users.read',
                'description' => 'Permet de consulter la liste de tous les utilisateurs',
                'resource' => 'users',
                'action' => 'read',
                'category' => 'Administration',
                'module' => 'Utilisateurs',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'un utilisateur',
                'slug' => 'users.view-details',
                'description' => 'Permet de consulter les détails complets d\'un utilisateur',
                'resource' => 'users',
                'action' => 'view-details',
                'category' => 'Administration',
                'module' => 'Utilisateurs',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Créer un utilisateur',
                'slug' => 'users.create',
                'description' => 'Permet de créer un nouveau compte utilisateur',
                'resource' => 'users',
                'action' => 'create',
                'category' => 'Administration',
                'module' => 'Utilisateurs',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier un utilisateur',
                'slug' => 'users.update',
                'description' => 'Permet de modifier les informations d\'un utilisateur',
                'resource' => 'users',
                'action' => 'update',
                'category' => 'Administration',
                'module' => 'Utilisateurs',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Supprimer un utilisateur',
                'slug' => 'users.delete',
                'description' => 'Permet de supprimer un compte utilisateur',
                'resource' => 'users',
                'action' => 'delete',
                'category' => 'Administration',
                'module' => 'Utilisateurs',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Activer/Désactiver un utilisateur',
                'slug' => 'users.toggle-status',
                'description' => 'Permet d\'activer ou désactiver un compte utilisateur',
                'resource' => 'users',
                'action' => 'toggle-status',
                'category' => 'Administration',
                'module' => 'Utilisateurs',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir la corbeille des utilisateurs',
                'slug' => 'users.view-trash',
                'description' => 'Permet de consulter les utilisateurs supprimés',
                'resource' => 'users',
                'action' => 'view-trash',
                'category' => 'Administration',
                'module' => 'Utilisateurs',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Restaurer un utilisateur',
                'slug' => 'users.restore',
                'description' => 'Permet de restaurer un utilisateur supprimé',
                'resource' => 'users',
                'action' => 'restore',
                'category' => 'Administration',
                'module' => 'Utilisateurs',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Supprimer définitivement un utilisateur',
                'slug' => 'users.force-delete',
                'description' => 'Permet de supprimer définitivement un utilisateur',
                'resource' => 'users',
                'action' => 'force-delete',
                'category' => 'Administration',
                'module' => 'Utilisateurs',
                'requires_confirmation' => true,
            ],
        ];
    }

    /**
     * Permissions des appels d'offres.
     */
    private function getAppelsOffresPermissions(): array
    {
        return [
            // === TYPES D'APPELS D'OFFRES ===
            [
                'name' => 'Voir les types d\'appels d\'offres',
                'slug' => 'types_appels_offres.read',
                'description' => 'Permet de consulter la liste des types d\'appels d\'offres',
                'resource' => 'types_appels_offres',
                'action' => 'read',
                'category' => 'Appels d\'offres',
                'module' => 'Types d\'appels d\'offres',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Créer un type d\'appel d\'offres',
                'slug' => 'types_appels_offres.create',
                'description' => 'Permet de créer un nouveau type d\'appel d\'offres',
                'resource' => 'types_appels_offres',
                'action' => 'create',
                'category' => 'Appels d\'offres',
                'module' => 'Types d\'appels d\'offres',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'un type',
                'slug' => 'types_appels_offres.view-details',
                'description' => 'Permet de consulter les détails complets d\'un type d\'appel d\'offres',
                'resource' => 'types_appels_offres',
                'action' => 'view-details',
                'category' => 'Appels d\'offres',
                'module' => 'Types d\'appels d\'offres',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier un type d\'appel d\'offres',
                'slug' => 'types_appels_offres.update',
                'description' => 'Permet de modifier un type d\'appel d\'offres (crée une nouvelle version)',
                'resource' => 'types_appels_offres',
                'action' => 'update',
                'category' => 'Appels d\'offres',
                'module' => 'Types d\'appels d\'offres',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Supprimer un type d\'appel d\'offres',
                'slug' => 'types_appels_offres.delete',
                'description' => 'Permet de supprimer un type d\'appel d\'offres',
                'resource' => 'types_appels_offres',
                'action' => 'delete',
                'category' => 'Appels d\'offres',
                'module' => 'Types d\'appels d\'offres',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Activer/Désactiver un type',
                'slug' => 'types_appels_offres.toggle-status',
                'description' => 'Permet d\'activer ou désactiver un type d\'appel d\'offres',
                'resource' => 'types_appels_offres',
                'action' => 'toggle-status',
                'category' => 'Appels d\'offres',
                'module' => 'Types d\'appels d\'offres',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir l\'historique des types',
                'slug' => 'types_appels_offres.view-history',
                'description' => 'Permet de consulter l\'historique des versions des types',
                'resource' => 'types_appels_offres',
                'action' => 'view-history',
                'category' => 'Appels d\'offres',
                'module' => 'Types d\'appels d\'offres',
                'requires_confirmation' => false,
            ],

            // === APPELS D'OFFRES ===
            [
                'name' => 'Voir les appels d\'offres',
                'slug' => 'appels_offres.read',
                'description' => 'Permet de consulter la liste des appels d\'offres',
                'resource' => 'appels_offres',
                'action' => 'read',
                'category' => 'Appels d\'offres',
                'module' => 'Appels d\'offres',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'un appel d\'offres',
                'slug' => 'appels_offres.view-details',
                'description' => 'Permet de consulter les détails complets d\'un appel d\'offres',
                'resource' => 'appels_offres',
                'action' => 'view-details',
                'category' => 'Appels d\'offres',
                'module' => 'Appels d\'offres',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Créer un appel d\'offres',
                'slug' => 'appels_offres.create',
                'description' => 'Permet de créer un nouvel appel d\'offres',
                'resource' => 'appels_offres',
                'action' => 'create',
                'category' => 'Appels d\'offres',
                'module' => 'Appels d\'offres',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier un appel d\'offres',
                'slug' => 'appels_offres.update',
                'description' => 'Permet de modifier un appel d\'offres existant',
                'resource' => 'appels_offres',
                'action' => 'update',
                'category' => 'Appels d\'offres',
                'module' => 'Appels d\'offres',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Supprimer un appel d\'offres',
                'slug' => 'appels_offres.delete',
                'description' => 'Permet de supprimer un appel d\'offres',
                'resource' => 'appels_offres',
                'action' => 'delete',
                'category' => 'Appels d\'offres',
                'module' => 'Appels d\'offres',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Valider un appel d\'offres',
                'slug' => 'appels_offres.validate',
                'description' => 'Permet de valider un appel d\'offres pour publication',
                'resource' => 'appels_offres',
                'action' => 'validate',
                'category' => 'Appels d\'offres',
                'module' => 'Appels d\'offres',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Annuler un appel d\'offres',
                'slug' => 'appels_offres.cancel',
                'description' => 'Permet d\'annuler un appel d\'offres',
                'resource' => 'appels_offres',
                'action' => 'cancel',
                'category' => 'Appels d\'offres',
                'module' => 'Appels d\'offres',
                'requires_confirmation' => true,
            ],

            [
                'name' => 'Voir les caractéristiques',
                'slug' => 'caracteristiques_appels_offres.read',
                'description' => 'Permet de consulter les caractéristiques d\'un appel d\'offres',
                'resource' => 'caracteristiques_appels_offres',
                'action' => 'read',
                'category' => 'Appels d\'offres',
                'module' => 'Caractéristiques',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'une caractéristique',
                'slug' => 'caracteristiques_appels_offres.view-details',
                'description' => 'Permet de consulter les détails complets d\'une catactéristique',
                'resource' => 'caracteristiques_appels_offres',
                'action' => 'view-details',
                'category' => 'Appels d\'offres',
                'module' => 'Caractéristiques',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Créer des caractéristiques',
                'slug' => 'caracteristiques_appels_offres.create',
                'description' => 'Permet d\'ajouter des caractéristiques à un appel d\'offres',
                'resource' => 'caracteristiques_appels_offres',
                'action' => 'create',
                'category' => 'Appels d\'offres',
                'module' => 'Caractéristiques',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier des caractéristiques',
                'slug' => 'caracteristiques_appels_offres.update',
                'description' => 'Permet de modifier les caractéristiques d\'un appel d\'offres',
                'resource' => 'caracteristiques_appels_offres',
                'action' => 'update',
                'category' => 'Appels d\'offres',
                'module' => 'Caractéristiques',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Supprimer des caractéristiques',
                'slug' => 'caracteristiques_appels_offres.delete',
                'description' => 'Permet de supprimer des caractéristiques d\'un appel d\'offres',
                'resource' => 'caracteristiques_appels_offres',
                'action' => 'delete',
                'category' => 'Appels d\'offres',
                'module' => 'Caractéristiques',
                'requires_confirmation' => true,
            ],

            [
                'name' => 'Voir l\'historique des caractéristiques',
                'slug' => 'caracteristiques_appels_offres.view-history',
                'description' => 'Permet de consulter l\'historique des versions des caractéristiques des appels d\'offres',
                'resource' => 'caracteristiques_appels_offres',
                'action' => 'view-history',
                'category' => 'Appels d\'offres',
                'module' => 'Caractéristiques',
                'requires_confirmation' => false,
            ],
        ];
    }

    /**
     * Permissions des lots et évaluations.
     */
    private function getLotsEvaluationsPermissions(): array
    {
        return [
            // === LOTS ===
            [
                'name' => 'Voir les lots',
                'slug' => 'lots.read',
                'description' => 'Permet de consulter la liste des lots d\'un appel d\'offres',
                'resource' => 'lots',
                'action' => 'read',
                'category' => 'Lots et Évaluations',
                'module' => 'Lots',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'un lot',
                'slug' => 'lots.view-details',
                'description' => 'Permet de consulter les détails complets d\'un lot',
                'resource' => 'lots',
                'action' => 'view-details',
                'category' => 'Lots et Évaluations',
                'module' => 'Lots',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Créer un lot',
                'slug' => 'lots.create',
                'description' => 'Permet de créer un nouveau lot dans un appel d\'offres',
                'resource' => 'lots',
                'action' => 'create',
                'category' => 'Lots et Évaluations',
                'module' => 'Lots',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier un lot',
                'slug' => 'lots.update',
                'description' => 'Permet de modifier un lot existant',
                'resource' => 'lots',
                'action' => 'update',
                'category' => 'Lots et Évaluations',
                'module' => 'Lots',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Supprimer un lot',
                'slug' => 'lots.delete',
                'description' => 'Permet de supprimer un lot',
                'resource' => 'lots',
                'action' => 'delete',
                'category' => 'Lots et Évaluations',
                'module' => 'Lots',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Activer/Désactiver un lot',
                'slug' => 'lots.toggle-status',
                'description' => 'Permet d\'activer ou désactiver un lot',
                'resource' => 'lots',
                'action' => 'toggle-status',
                'category' => 'Lots et Évaluations',
                'module' => 'Lots',
                'requires_confirmation' => false,
            ],

            [
                'name' => 'Voir l\'historique des lots',
                'slug' => 'lots.view-history',
                'description' => 'Permet de consulter l\'historique des versions des lots',
                'resource' => 'lots',
                'action' => 'view-history',
                'category' => 'Lots et Évaluations',
                'module' => 'Lots',
                'requires_confirmation' => false,
            ],

            [
                'name' => 'Dupliquer un lot',
                'slug' => 'lots.duplicate',
                'description' => 'Permet de dupliquer un lot existant',
                'resource' => 'lots',
                'action' => 'duplicate',
                'category' => 'Lots et Évaluations',
                'module' => 'Lots',
                'requires_confirmation' => false,
            ],

            // === CRITÈRES D'ÉVALUATION ===
            [
                'name' => 'Voir les critères d\'évaluation',
                'slug' => 'criteres_evaluations.read',
                'description' => 'Permet de consulter les critères d\'évaluation d\'un lot',
                'resource' => 'criteres_evaluations',
                'action' => 'read',
                'category' => 'Lots et Évaluations',
                'module' => 'Critères d\'évaluation',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Créer un critère d\'évaluation',
                'slug' => 'criteres_evaluations.create',
                'description' => 'Permet de créer un nouveau critère d\'évaluation',
                'resource' => 'criteres_evaluations',
                'action' => 'create',
                'category' => 'Lots et Évaluations',
                'module' => 'Critères d\'évaluation',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier un critère d\'évaluation',
                'slug' => 'criteres_evaluations.update',
                'description' => 'Permet de modifier un critère d\'évaluation',
                'resource' => 'criteres_evaluations',
                'action' => 'update',
                'category' => 'Lots et Évaluations',
                'module' => 'Critères d\'évaluation',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Supprimer un critère d\'évaluation',
                'slug' => 'criteres_evaluations.delete',
                'description' => 'Permet de supprimer un critère d\'évaluation',
                'resource' => 'criteres_evaluations',
                'action' => 'delete',
                'category' => 'Lots et Évaluations',
                'module' => 'Critères d\'évaluation',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Voir les détails d\'un critère d\'évaluation',
                'slug' => 'criteres_evaluations.view-details',
                'description' => 'Permet de consulter les détails complets d\'un critère d\'évaluation',
                'resource' => 'criteres_evaluations',
                'action' => 'view-details',
                'category' => 'Lots et Évaluations',
                'module' => 'Critères d\'évaluation',
                'requires_confirmation' => false,
            ],

            [
                'name' => 'Dupliquer un critère d\'évaluation',
                'slug' => 'criteres_evaluations.duplicate',
                'description' => 'Permet de dupliquer un critère d\'évaluation existant',
                'resource' => 'criteres_evaluations',
                'action' => 'duplicate',
                'category' => 'Lots et Évaluations',
                'module' => 'Critères d\'évaluation',
                'requires_confirmation' => false,
            ],






        ];
    }

    /**
     * Permissions des proformas.
     */
    private function getProformaPermissions(): array
    {
        return [
            [
                'name' => 'Voir les proformas',
                'slug' => 'proformas.read',
                'description' => 'Permet de consulter la liste des proformas',
                'resource' => 'proformas',
                'action' => 'read',
                'category' => 'Pro-forma',
                'module' => 'Proformas',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'une proforma',
                'slug' => 'proformas.view-details',
                'description' => 'Permet de consulter les détails complets d\'une proforma',
                'resource' => 'proformas',
                'action' => 'view-details',
                'category' => 'Pro-forma',
                'module' => 'Proformas',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Créer une proforma',
                'slug' => 'proformas.create',
                'description' => 'Permet de créer une nouvelle proforma',
                'resource' => 'proformas',
                'action' => 'create',
                'category' => 'Pro-forma',
                'module' => 'Proformas',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier une proforma',
                'slug' => 'proformas.update',
                'description' => 'Permet de modifier une proforma existante',
                'resource' => 'proformas',
                'action' => 'update',
                'category' => 'Pro-forma',
                'module' => 'Proformas',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Supprimer une proforma',
                'slug' => 'proformas.delete',
                'description' => 'Permet de supprimer une proforma',
                'resource' => 'proformas',
                'action' => 'delete',
                'category' => 'Pro-forma',
                'module' => 'Proformas',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Créer une nouvelle version',
                'slug' => 'proformas.create-version',
                'description' => 'Permet de créer une nouvelle version d\'une proforma',
                'resource' => 'proformas',
                'action' => 'create-version',
                'category' => 'Pro-forma',
                'module' => 'Proformas',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir l\'historique des proformas',
                'slug' => 'proformas.view-history',
                'description' => 'Permet de consulter l\'historique des versions des proformas',
                'resource' => 'proformas',
                'action' => 'view-history',
                'category' => 'Pro-forma',
                'module' => 'Proformas',
                'requires_confirmation' => false,
            ],
        ];
    }

    /**
     * Permissions des prestataires.
     */
    private function getPrestatairesPermissions(): array
    {
        return [
            // === PRESTATAIRES ===
            [
                'name' => 'Voir les prestataires',
                'slug' => 'prestataires.read',
                'description' => 'Permet de consulter la liste des prestataires',
                'resource' => 'prestataires',
                'action' => 'read',
                'category' => 'Prestataires',
                'module' => 'Prestataires',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'un prestataire',
                'slug' => 'prestataires.view-details',
                'description' => 'Permet de consulter les détails complets d\'un prestataire',
                'resource' => 'prestataires',
                'action' => 'view-details',
                'category' => 'Prestataires',
                'module' => 'Prestataires',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Créer un prestataire',
                'slug' => 'prestataires.create',
                'description' => 'Permet de créer un nouveau prestataire',
                'resource' => 'prestataires',
                'action' => 'create',
                'category' => 'Prestataires',
                'module' => 'Prestataires',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier un prestataire',
                'slug' => 'prestataires.update',
                'description' => 'Permet de modifier les informations d\'un prestataire',
                'resource' => 'prestataires',
                'action' => 'update',
                'category' => 'Prestataires',
                'module' => 'Prestataires',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Supprimer un prestataire',
                'slug' => 'prestataires.delete',
                'description' => 'Permet de supprimer un prestataire',
                'resource' => 'prestataires',
                'action' => 'delete',
                'category' => 'Prestataires',
                'module' => 'Prestataires',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Activer/Désactiver un prestataire',
                'slug' => 'prestataires.toggle-status',
                'description' => 'Permet d\'activer ou désactiver un prestataire',
                'resource' => 'prestataires',
                'action' => 'toggle-status',
                'category' => 'Prestataires',
                'module' => 'Prestataires',
                'requires_confirmation' => false,
            ],

            // === BANQUES DES PRESTATAIRES ===
            [
                'name' => 'Voir les informations bancaires',
                'slug' => 'banques_prestataires.read',
                'description' => 'Permet de consulter les informations bancaires des prestataires',
                'resource' => 'banques_prestataires',
                'action' => 'read',
                'category' => 'Prestataires',
                'module' => 'Banques',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Ajouter une banque',
                'slug' => 'banques_prestataires.create',
                'description' => 'Permet d\'ajouter une banque à un prestataire',
                'resource' => 'banques_prestataires',
                'action' => 'create',
                'category' => 'Prestataires',
                'module' => 'Banques',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier une banque',
                'slug' => 'banques_prestataires.update',
                'description' => 'Permet de modifier les informations bancaires',
                'resource' => 'banques_prestataires',
                'action' => 'update',
                'category' => 'Prestataires',
                'module' => 'Banques',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Supprimer une banque',
                'slug' => 'banques_prestataires.delete',
                'description' => 'Permet de supprimer une banque d\'un prestataire',
                'resource' => 'banques_prestataires',
                'action' => 'delete',
                'category' => 'Prestataires',
                'module' => 'Banques',
                'requires_confirmation' => true,
            ],
             [
                'name' => 'Activer/Désactiver un compte bancaire',
                'slug' => 'banques_prestataires.toggle-status',
                'description' => 'Permet d\'activer ou désactiver un compte bancaire',
                'resource' => 'banques_prestataires',
                'action' => 'toggle-status',
                'category' => 'Prestataires',
                'module' => 'Banques',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'un compte bancaire',
                'slug' => 'banques_prestataires.view-details',
                'description' => 'Permet de consulter les détails complets d\'un compte bancaire',
                'resource' => 'banques_prestataires',
                'action' => 'view-details',
                'category' => 'Prestataires',
                'module' => 'Banques',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Dupliquer un compte bancaire',
                'slug' => 'banques_prestataires.duplicate',
                'description' => 'Permet de dupliquer un compte bancaire existant',
                'resource' => 'banques_prestataires',
                'action' => 'duplicate',
                'category' => 'Prestataires',
                'module' => 'Banques',
                'requires_confirmation' => false,
            ],



            // === CAPACITÉS TECHNIQUES ===
            [
                'name' => 'Voir les capacités techniques',
                'slug' => 'capacites_techniques.read',
                'description' => 'Permet de consulter les capacités techniques des prestataires',
                'resource' => 'capacites_techniques',
                'action' => 'read',
                'category' => 'Prestataires',
                'module' => 'Capacités',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Gérer les capacités techniques',
                'slug' => 'capacites_techniques.manage',
                'description' => 'Permet de gérer les capacités techniques d\'un prestataire',
                'resource' => 'capacites_techniques',
                'action' => 'manage',
                'category' => 'Prestataires',
                'module' => 'Capacités',
                'requires_confirmation' => false,
            ],

            // === SITUATIONS FINANCIÈRES ===
            [
                'name' => 'Voir les situations financières',
                'slug' => 'situations_financieres.read',
                'description' => 'Permet de consulter les situations financières des prestataires',
                'resource' => 'situations_financieres',
                'action' => 'read',
                'category' => 'Prestataires',
                'module' => 'Finances',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Gérer les situations financières',
                'slug' => 'situations_financieres.manage',
                'description' => 'Permet de gérer les situations financières d\'un prestataire',
                'resource' => 'situations_financieres',
                'action' => 'manage',
                'category' => 'Prestataires',
                'module' => 'Finances',
                'requires_confirmation' => false,
            ],
        ];
    }

    /**
     * Permissions des attributions.
     */
    private function getAttributionsPermissions(): array
    {
        return [
            // === ATTRIBUTIONS DE LOTS ===
            [
                'name' => 'Voir les attributions',
                'slug' => 'attributions_lots.read',
                'description' => 'Permet de consulter les attributions de lots aux prestataires',
                'resource' => 'attributions_lots',
                'action' => 'read',
                'category' => 'Attributions',
                'module' => 'Attributions',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'une attribution',
                'slug' => 'attributions_lots.view-details',
                'description' => 'Permet de consulter les détails complets d\'une attribution',
                'resource' => 'attributions_lots',
                'action' => 'view-details',
                'category' => 'Attributions',
                'module' => 'Attributions',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Attribuer un lot',
                'slug' => 'attributions_lots.assign',
                'description' => 'Permet d\'attribuer un lot à un prestataire',
                'resource' => 'attributions_lots',
                'action' => 'assign',
                'category' => 'Attributions',
                'module' => 'Attributions',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Réattribuer un lot',
                'slug' => 'attributions_lots.reassign',
                'description' => 'Permet de réattribuer un lot à un autre prestataire',
                'resource' => 'attributions_lots',
                'action' => 'reassign',
                'category' => 'Attributions',
                'module' => 'Attributions',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Retirer une attribution',
                'slug' => 'attributions_lots.withdraw',
                'description' => 'Permet de retirer une attribution de lot',
                'resource' => 'attributions_lots',
                'action' => 'withdraw',
                'category' => 'Attributions',
                'module' => 'Attributions',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Suspendre une attribution',
                'slug' => 'attributions_lots.suspend',
                'description' => 'Permet de suspendre temporairement une attribution',
                'resource' => 'attributions_lots',
                'action' => 'suspend',
                'category' => 'Attributions',
                'module' => 'Attributions',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Reprendre une attribution',
                'slug' => 'attributions_lots.resume',
                'description' => 'Permet de reprendre une attribution suspendue',
                'resource' => 'attributions_lots',
                'action' => 'resume',
                'category' => 'Attributions',
                'module' => 'Attributions',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir l\'historique des attributions',
                'slug' => 'attributions_lots.view-history',
                'description' => 'Permet de consulter l\'historique des attributions',
                'resource' => 'attributions_lots',
                'action' => 'view-history',
                'category' => 'Attributions',
                'module' => 'Attributions',
                'requires_confirmation' => false,
            ],

            // === ÉVALUATIONS DES ATTRIBUTIONS ===
            [
                'name' => 'Voir les évaluations',
                'slug' => 'evaluations_attributions.read',
                'description' => 'Permet de consulter les évaluations des prestataires',
                'resource' => 'evaluations_attributions',
                'action' => 'read',
                'category' => 'Attributions',
                'module' => 'Évaluations',
                'requires_confirmation' => false,
            ],

            [
                'name' => 'Voir les détails d\'une évaluation',
                'slug' => 'evaluations_attributions.view-details',
                'description' => 'Permet de consulter les détails d\'une évaluation',
                'resource' => 'evaluations_attributions',
                'action' => 'view-details',
                'category' => 'Attributions',
                'module' => 'Évaluations',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Évaluer un prestataire',
                'slug' => 'evaluations_attributions.evaluate',
                'description' => 'Permet d\'évaluer un prestataire sur un lot',
                'resource' => 'evaluations_attributions',
                'action' => 'evaluate',
                'category' => 'Attributions',
                'module' => 'Évaluations',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Valider une évaluation',
                'slug' => 'evaluations_attributions.validate',
                'description' => 'Permet de valider une évaluation',
                'resource' => 'evaluations_attributions',
                'action' => 'validate',
                'category' => 'Attributions',
                'module' => 'Évaluations',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Rejeter une évaluation',
                'slug' => 'evaluations_attributions.reject',
                'description' => 'Permet de rejeter une évaluation',
                'resource' => 'evaluations_attributions',
                'action' => 'reject',
                'category' => 'Attributions',
                'module' => 'Évaluations',
                'requires_confirmation' => true,
            ],

        ];
    }

    /**
     * Permissions de facturation et paiements.
     */
    private function getFacturationPaiementsPermissions(): array
    {
        return [
            // === FACTURES ===
            [
                'name' => 'Voir les factures',
                'slug' => 'factures.read',
                'description' => 'Permet de consulter la liste des factures',
                'resource' => 'factures',
                'action' => 'read',
                'category' => 'Facturation et Paiements',
                'module' => 'Factures',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'une facture',
                'slug' => 'factures.view-details',
                'description' => 'Permet de consulter les détails complets d\'une facture',
                'resource' => 'factures',
                'action' => 'view-details',
                'category' => 'Facturation et Paiements',
                'module' => 'Factures',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Créer une facture',
                'slug' => 'factures.create',
                'description' => 'Permet de créer une nouvelle facture',
                'resource' => 'factures',
                'action' => 'create',
                'category' => 'Facturation et Paiements',
                'module' => 'Factures',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier une facture',
                'slug' => 'factures.update',
                'description' => 'Permet de modifier une facture existante',
                'resource' => 'factures',
                'action' => 'update',
                'category' => 'Facturation et Paiements',
                'module' => 'Factures',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Valider une facture',
                'slug' => 'factures.validate',
                'description' => 'Permet de valider une facture pour paiement',
                'resource' => 'factures',
                'action' => 'validate',
                'category' => 'Facturation et Paiements',
                'module' => 'Factures',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Rejeter une facture',
                'slug' => 'factures.reject',
                'description' => 'Permet de rejeter une facture',
                'resource' => 'factures',
                'action' => 'reject',
                'category' => 'Facturation et Paiements',
                'module' => 'Factures',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Annuler une facture',
                'slug' => 'factures.cancel',
                'description' => 'Permet d\'annuler une facture',
                'resource' => 'factures',
                'action' => 'cancel',
                'category' => 'Facturation et Paiements',
                'module' => 'Factures',
                'requires_confirmation' => true,
            ],



            [
                'name' => 'Remettre en attente une facture',
                'slug' => 'factures.pending',
                'description' => 'Permet de remettre en attente une facture rejetée',
                'resource' => 'factures',
                'action' => 'pending',
                'category' => 'Facturation et Paiements',
                'module' => 'Factures',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Dupliquer une facture',
                'slug' => 'factures.duplicate',
                'description' => 'Permet de dupliquer une facture existante',
                'resource' => 'factures',
                'action' => 'duplicate',
                'category' => 'Facturation et Paiements',
                'module' => 'Factures',
                'requires_confirmation' => false,
            ],
            // NOTE : Si 'factures.delete' n'existe pas, ajouter aussi :
            [
                'name' => 'Supprimer une facture',
                'slug' => 'factures.delete',
                'description' => 'Permet de supprimer définitivement une facture',
                'resource' => 'factures',
                'action' => 'delete',
                'category' => 'Facturation et Paiements',
                'module' => 'Factures',
                'requires_confirmation' => true,
            ],





            // === PAIEMENTS ===
            [
                'name' => 'Voir les paiements',
                'slug' => 'paiements.read',
                'description' => 'Permet de consulter la liste des paiements',
                'resource' => 'paiements',
                'action' => 'read',
                'category' => 'Facturation et Paiements',
                'module' => 'Paiements',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Voir les détails d\'un paiement',
                'slug' => 'paiements.view-details',
                'description' => 'Permet de consulter les détails complets d\'un paiement',
                'resource' => 'paiements',
                'action' => 'view-details',
                'category' => 'Facturation et Paiements',
                'module' => 'Paiements',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Créer un paiement',
                'slug' => 'paiements.create',
                'description' => 'Permet d\'initier un nouveau paiement',
                'resource' => 'paiements',
                'action' => 'create',
                'category' => 'Facturation et Paiements',
                'module' => 'Paiements',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Modifier un paiement',
                'slug' => 'paiements.update',
                'description' => 'Permet de modifier les informations d\'un paiement',
                'resource' => 'paiements',
                'action' => 'update',
                'category' => 'Facturation et Paiements',
                'module' => 'Paiements',
                'requires_confirmation' => false,
            ],
            [
                'name' => 'Traiter un paiement',
                'slug' => 'paiements.process',
                'description' => 'Permet de traiter un paiement en attente',
                'resource' => 'paiements',
                'action' => 'process',
                'category' => 'Facturation et Paiements',
                'module' => 'Paiements',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Confirmer un paiement',
                'slug' => 'paiements.confirm',
                'description' => 'Permet de confirmer l\'exécution d\'un paiement',
                'resource' => 'paiements',
                'action' => 'confirm',
                'category' => 'Facturation et Paiements',
                'module' => 'Paiements',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Rejeter un paiement',
                'slug' => 'paiements.reject',
                'description' => 'Permet de rejeter un paiement',
                'resource' => 'paiements',
                'action' => 'reject',
                'category' => 'Facturation et Paiements',
                'module' => 'Paiements',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Voir l\'historique des paiements',
                'slug' => 'paiements.view-history',
                'description' => 'Permet de consulter l\'historique des paiements',
                'resource' => 'paiements',
                'action' => 'view-history',
                'category' => 'Facturation et Paiements',
                'module' => 'Paiements',
                'requires_confirmation' => false,
            ],


            [
                'name' => 'Valider un paiement',
                'slug' => 'paiements.validate',
                'description' => 'Permet de valider un paiement enregistré avant traitement',
                'resource' => 'paiements',
                'action' => 'validate',
                'category' => 'Facturation et Paiements',
                'module' => 'Paiements',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Remettre en attente un paiement',
                'slug' => 'paiements.pending',
                'description' => 'Permet de remettre en attente un paiement rejeté',
                'resource' => 'paiements',
                'action' => 'pending',
                'category' => 'Facturation et Paiements',
                'module' => 'Paiements',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Annuler un paiement',
                'slug' => 'paiements.cancel',
                'description' => 'Permet d\'annuler un paiement enregistré',
                'resource' => 'paiements',
                'action' => 'cancel',
                'category' => 'Facturation et Paiements',
                'module' => 'Paiements',
                'requires_confirmation' => true,
            ],
            [
                'name' => 'Supprimer un paiement',
                'slug' => 'paiements.delete',
                'description' => 'Permet de supprimer définitivement un paiement',
                'resource' => 'paiements',
                'action' => 'delete',
                'category' => 'Facturation et Paiements',
                'module' => 'Paiements',
                'requires_confirmation' => true,
            ],
        ];
    }
}
