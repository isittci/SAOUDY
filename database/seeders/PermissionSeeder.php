<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les permissions de base
        $this->createPermissions();

        // Créer les rôles
        $this->createRoles();

        // Assigner les permissions aux rôles
        $this->assignPermissionsToRoles();
    }

    /**
     * Create base permissions.
     */
    private function createPermissions(): void
    {
        $permissions = [
            // Gestion des utilisateurs

            [
                'name'=> 'Gérer les utilisateurs',
                'slug'=> 'users-manage',
                'resource'=> 'users',
                'action'=> 'manage',
                'category'=> 'Gestion des utilisateurs',
                'description'=> 'Permet de gérer toutes les actions sur les utilisateurs',
                'priority' => 20,
                'is_system' => true,
            ],
            [
                'name' => 'Créer des utilisateurs',
                'slug' => 'users-create',
                'resource' => 'users',
                'action' => 'create',
                'category' => 'Gestion des utilisateurs',
                'description' => 'Permet de créer de nouveaux utilisateurs',
                'priority' => 10,
                'is_system' => true,
            ],
            [
                'name' => 'Voir les utilisateurs',
                'slug' => 'users-read',
                'resource' => 'users',
                'action' => 'read',
                'category' => 'Gestion des utilisateurs',
                'description' => 'Permet de consulter la liste des utilisateurs',
                'priority' => 5,
                'is_system' => true,
            ],
            [
                'name' => 'Modifier les utilisateurs',
                'slug' => 'users-update',
                'resource' => 'users',
                'action' => 'update',
                'category' => 'Gestion des utilisateurs',
                'description' => 'Permet de modifier les informations des utilisateurs',
                'priority' => 10,
                'is_system' => true,
            ],
            [
                'name' => 'Supprimer les utilisateurs',
                'slug' => 'users-delete',
                'resource' => 'users',
                'action' => 'delete',
                'category' => 'Gestion des utilisateurs',
                'description' => 'Permet de supprimer des utilisateurs',
                'priority' => 15,
                'is_system' => true,
            ],
            [
                'name' => 'Exporter les utilisateurs',
                'slug' => 'users-export',
                'resource' => 'users',
                'action' => 'export',
                'category' => 'Gestion des utilisateurs',
                'description' => 'Permet d\'exporter la liste des utilisateurs',
                'priority' => 5,
            ],
            [
                'name' => 'Importer des utilisateurs',
                'slug' => 'users-import',
                'resource' => 'users',
                'action' => 'import',
                'category' => 'Gestion des utilisateurs',
                'description' => 'Permet d\'importer des utilisateurs depuis un fichier',
                'priority' => 10,
            ],
            [
                'name'=> 'Valider les utilisateurs',
                'slug' => 'users-validate',
                'resource' => 'users',
                'action' => 'validate',
                'category' => 'Gestion des utilisateurs',
                'description' => 'Permet de valider les comptes utilisateurs',
                'priority' => 10,
            ],
            [
                'name'=> 'Rejecter les utilisateurs',
                'slug'=> 'users-reject',
                'resource'=> 'users',
                'action'=> 'reject',
                'category'=> 'Gestion des utilisateurs',
                'description'=> 'Permet de rejetter les comptes utilisateurs',
                'priority' => 10,
            ],
            [
                'name'=> 'Restaurer les utilisateurs',
                'slug'=> 'users-restore',
                'resource'=> 'users',
                'action'=> 'restore',
                'category'=> 'Gestion des utilisateurs',
                'description'=> 'Permet de restaurer les comptes utilisateurs supprimés',
                'priority' => 15,
            ],
            [
                'name'=> 'Dupliquer les utilisateurs',
                'slug'=> 'users-duplicate',
                'resource'=> 'users',
                'action'=> 'duplicate',
                'category'=> 'Gestion des utilisateurs',
                'description'=> 'Permet de dupliquer les comptes utilisateurs',
                'priority' => 10,
            ],
            [
                'name'=> 'Télécharger les utilisateurs',
                'slug'=> 'users-download',
                'resource'=> 'users',
                'action'=> 'download',
                'category'=> 'Gestion des utilisateurs',
                'description'=> 'Permet de télécharger les informations des utilisateurs',
                'priority' => 5,
            ],








            // Gestion des rôles
            [
                'name' => 'Créer des rôles',
                'slug' => 'roles-create',
                'resource' => 'roles',
                'action' => 'create',
                'category' => 'Gestion des rôles',
                'description' => 'Permet de créer de nouveaux rôles',
                'priority' => 10,
                'is_system' => true,
            ],
            [
                'name' => 'Voir les rôles',
                'slug' => 'roles-read',
                'resource' => 'roles',
                'action' => 'read',
                'category' => 'Gestion des rôles',
                'description' => 'Permet de consulter la liste des rôles',
                'priority' => 5,
                'is_system' => true,
            ],
            [
                'name' => 'Modifier les rôles',
                'slug' => 'roles-update',
                'resource' => 'roles',
                'action' => 'update',
                'category' => 'Gestion des rôles',
                'description' => 'Permet de modifier les rôles',
                'priority' => 10,
                'is_system' => true,
            ],
            [
                'name' => 'Supprimer les rôles',
                'slug' => 'roles-delete',
                'resource' => 'roles',
                'action' => 'delete',
                'category' => 'Gestion des rôles',
                'description' => 'Permet de supprimer des rôles',
                'priority' => 15,
                'is_system' => true,
            ],
            [
                'name' => 'Assigner des rôles',
                'slug' => 'roles-assign',
                'resource' => 'roles',
                'action' => 'update',
                'category' => 'Gestion des rôles',
                'description' => 'Permet d\'assigner des rôles aux utilisateurs',
                'priority' => 15,
                'is_system' => true,
            ],
            [
                'name' => 'Exporter les rôles',
                'slug' => 'roles-export',
                'resource' => 'roles',
                'action' => 'export',
                'category' => 'Gestion des rôles',
                'description' => 'Permet d\'exporter la liste des rôles',
                'priority' => 5,
                'is_system' => true,
            ],
            [
                'name' => 'Importer des rôles',
                'slug' => 'roles-import',
                'resource' => 'roles',
                'action' => 'import',
                'category' => 'Gestion des rôles',
                'description' => 'Permet d\'importer des rôles depuis un fichier',
                'priority' => 10,
                'is_system' => true,
            ],
            [
                'name'=> 'Dupliquer les rôles',
                'slug'=> 'roles-duplicate',
                'resource'=> 'roles',
                'action'=> 'duplicate',
                'category'=> 'Gestion des rôles',
                'description'=> 'Permet de dupliquer les rôles',
                'priority' => 10,
                'is_system' => true,
            ],
            [
                'name'=> 'Télécharger les rôles',
                'slug'=> 'roles-download',
                'resource'=> 'roles',
                'action'=> 'download',
                'category'=> 'Gestion des rôles',
                'description'=> 'Permet de télécharger les informations des rôles',
                'priority' => 5,
                'is_system' => true,
            ],
            // Duplication des rôles
            [
                'name'=> 'Dupliquer les rôles',
                'slug'=> 'roles-duplicate',
                'resource'=> 'roles',
                'action'=> 'duplicate',
                'category'=> 'Gestion des rôles',
                'description'=> 'Permet de dupliquer les rôles',
                'priority' => 10,
                'is_system' => true,
            ],
            //Restoration des rôles
            [
                'name'=> 'Restaurer les rôles',
                'slug'=> 'roles-restore',
                'resource'=> 'roles',
                'action'=> 'restore',
                'category'=> 'Gestion des rôles',
                'description'=> 'Permet de restaurer les rôles supprimés',
                'priority' => 15,
                'is_system' => true,
            ],
            // Mangement des roles
            [
                'name'=> 'Gérer les rôles',
                'slug'=> 'roles-manage',
                'resource'=> 'roles',
                'action'=> 'manage',
                'category'=> 'Gestion des rôles',
                'description'=> 'Permet de gérer toutes les actions sur les rôles',
                'priority' => 20,
                'is_system' => true,
            ],



            // Gestion des permissions
            [
                'name' => 'Gérer les permissions',
                'slug' => 'permissions-manage',
                'resource' => 'permissions',
                'action' => 'manage',
                'category' => 'Gestion des permissions',
                'description' => 'Permet de gérer toutes les permissions',
                'priority' => 20,
                'is_system' => true,
            ],
            [
                'name' => 'Voir les permissions',
                'slug' => 'permissions-read',
                'resource' => 'permissions',
                'action' => 'read',
                'category' => 'Gestion des permissions',
                'description' => 'Permet de consulter les permissions',
                'priority' => 5,
                'is_system' => true,
            ],
            [
                'name' => 'Assigner des permissions',
                'slug' => 'permissions-assign',
                'resource' => 'permissions',
                'action' => 'update',
                'category' => 'Gestion des permissions',
                'description' => 'Permet d\'assigner des permissions aux rôles',
                'priority' => 15,
                'is_system' => true,
            ],

            // Dashboard et rapports
            [
                'name' => 'Voir le dashboard',
                'slug' => 'dashboard-read',
                'resource' => 'dashboard',
                'action' => 'read',
                'category' => 'Dashboard',
                'description' => 'Permet d\'accéder au tableau de bord',
                'priority' => 1,
            ],
            [
                'name' => 'Voir les rapports',
                'slug' => 'reports-read',
                'resource' => 'reports',
                'action' => 'read',
                'category' => 'Rapports',
                'description' => 'Permet de consulter les rapports',
                'priority' => 5,
            ],
            [
                'name' => 'Exporter les rapports',
                'slug' => 'reports-export',
                'resource' => 'reports',
                'action' => 'export',
                'category' => 'Rapports',
                'description' => 'Permet d\'exporter les rapports',
                'priority' => 5,
            ],
        ];

        foreach ($permissions as $permission) {
            // Vérifier si la permission existe déjà
            $existingPermission = Permission::where('slug', $permission['slug'])->first();
            if ($existingPermission) {
                continue; // Passer à l'itération suivante si la permission existe
            }
            Permission::create($permission);
        }

        $this->command->info('Permissions créées avec succès!');
    }

    /**
     * Create base roles.
     */
    private function createRoles(): void
    {
        $roles = [
            [
                'name' => 'Super Administrateur',
                'slug' => 'super-admin',
                'description' => 'Accès complet à toutes les fonctionnalités du système',
                'level' => 100,
                'is_system_role' => true,
            ],
            [
                'name' => 'Administrateur',
                'slug' => 'admin',
                'description' => 'Gestion complète du système avec quelques restrictions',
                'level' => 80,
                'is_system_role' => true,
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Gestion des utilisateurs et des contenus',
                'level' => 60,
                'is_system_role' => false,
            ],
            [
                'name' => 'Éditeur',
                'slug' => 'editor',
                'description' => 'Modification et gestion des contenus',
                'level' => 40,
                'is_system_role' => false,
            ],
            [
                'name' => 'Utilisateur',
                'slug' => 'user',
                'description' => 'Accès de base au système',
                'level' => 20,
                'is_system_role' => false,
            ],
        ];

        foreach ($roles as $role) {
            // Vérifier si le rôle existe déjà
            $existingRole = Role::where('slug', $role['slug'])->first();
            if ($existingRole) {
                continue; // Passer à l'itération suivante si le rôle existe
            }
            Role::create($role);
        }

        $this->command->info('Rôles créés avec succès!');
    }

    /**
     * Assign permissions to roles.
     */
    private function assignPermissionsToRoles(): void
    {
        // Super Admin - Toutes les permissions
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $allPermissions = Permission::all()->pluck('id')->toArray();
        $superAdmin->syncPermissions($allPermissions);

        // Admin - Presque toutes les permissions sauf gestion des permissions
        $admin = Role::where('slug', 'admin')->first();
        $adminPermissions = Permission::where('resource', '!=', 'permissions')
            ->pluck('id')
            ->toArray();
        $admin->syncPermissions($adminPermissions);

        // Manager - Gestion des utilisateurs et lecture
        $manager = Role::where('slug', 'manager')->first();
        $managerPermissions = Permission::whereIn('slug', [
            'users-read',
            'users-create',
            'users-update',
            'roles-read',
            'dashboard-read',
            'reports-read',
        ])->pluck('id')->toArray();
        $manager->syncPermissions($managerPermissions);

        // Editor - Lecture et modification limitée
        $editor = Role::where('slug', 'editor')->first();
        $editorPermissions = Permission::whereIn('slug', [
            'users-read',
            'dashboard-read',
            'reports-read',
        ])->pluck('id')->toArray();
        $editor->syncPermissions($editorPermissions);

        // User - Lecture seulement
        $user = Role::where('slug', 'user')->first();
        $userPermissions = Permission::whereIn('slug', [
            'dashboard-read',
        ])->pluck('id')->toArray();
        $user->syncPermissions($userPermissions);

        $this->command->info('Permissions assignées aux rôles avec succès!');
    }
}

// ============================================================================
// EXEMPLES D'UTILISATION
// ============================================================================

/*

// Dans un contrôleur
class UserController extends Controller
{
    public function index()
    {
        // Vérifier la permission
        if (!auth()->user()->hasPermission('users-read')) {
            abort(403, 'Non autorisé');
        }

        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        // Utiliser le helper
        if (!user_can('users-create')) {
            abort(403);
        }

        // Créer l'utilisateur...
    }
}

// Dans les routes (web.php)
Route::middleware(['auth', 'permission:users-read'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});

Route::middleware(['auth', 'permission:users-create'])->group(function () {
    Route::post('/users', [UserController::class, 'store']);
});

// Vérifier plusieurs permissions
Route::middleware(['auth', 'any-permission:users-read,users-update'])->group(function () {
    Route::get('/users/{user}/edit', [UserController::class, 'edit']);
});

// Vérifier niveau de rôle
Route::middleware(['auth', 'role-level:60'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});

// Dans les vues Blade
@can('users-create')
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        Créer un utilisateur
    </a>
@endcan

@if(user_can('users-delete'))
    <button type="submit" class="btn btn-danger">Supprimer</button>
@endif

// Utilisation programmatique
$user = User::find(1);

// Vérifier une permission
if ($user->hasPermission('users-update')) {
    // Faire quelque chose
}

// Vérifier plusieurs permissions
if ($user->hasAllPermissions(['users-read', 'users-update'])) {
    // L'utilisateur a toutes ces permissions
}

if ($user->hasAnyPermission(['users-create', 'users-delete'])) {
    // L'utilisateur a au moins une de ces permissions
}

// Obtenir toutes les permissions
$permissions = $user->getAllPermissions();

// Gestion des permissions de rôle
$role = Role::find(1);

// Assigner une permission
$permission = Permission::where('slug', 'users-create')->first();
$role->givePermissionTo($permission);

// Avec données pivot
$role->givePermissionTo($permission, [
    'expire_le' => now()->addDays(30),
    'notes' => 'Permission temporaire',
]);

// Révoquer une permission
$role->revokePermissionTo($permission);

// Synchroniser les permissions
$permissionIds = [1, 2, 3, 4];
$role->syncPermissions($permissionIds);

// Vérifier si un rôle a une permission
if ($role->hasPermission('users-read')) {
    // Le rôle a cette permission
}

// Utilisation des helpers
create_crud_permissions('posts', 'Gestion des articles');

assign_permission_to_role('posts-create', 'editor');

$grouped = get_permissions_by_category();

cleanup_expired_permissions();

// Commande artisan pour nettoyer les permissions expirées
php artisan schedule:run
// Ajouter dans app/Console/Kernel.php :
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        cleanup_expired_permissions();
    })->daily();
}

*/
