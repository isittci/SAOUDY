<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerGates();
    }

    /**
     * Enregistre les gates pour toutes les permissions.
     */
    protected function registerGates(): void
    {
        try {
            // Charger toutes les permissions actives
            $permissions = Permission::where('is_active', true)->get();

            foreach ($permissions as $permission) {
                Gate::define($permission->slug, function (User $user) use ($permission) {
                    return $user->hasPermission($permission->slug);
                });
            }

            // Gate pour vérifier si un utilisateur peut gérer un autre utilisateur
            Gate::define('manage-user', function (User $user, User $targetUser) {
                return $user->canManageUser($targetUser);
            });

            // Gate pour vérifier si un utilisateur peut voir un autre utilisateur
            Gate::define('view-user', function (User $user, User $targetUser) {
                return $user->canViewUser($targetUser);
            });

        } catch (\Exception $e) {
            // En cas d'erreur (ex: migration non exécutée), on continue silencieusement
            report($e);
        }
    }
}
