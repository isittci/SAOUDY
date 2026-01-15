<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Crée les utilisateurs par défaut du système.
     */
    public function run(): void
    {
        // Récupérer les rôles
        $superAdminRole = Role::where('slug', 'super-administrateur')->first();
        $adminRole = Role::where('slug', 'administrateur')->first();

        if (!$superAdminRole) {
            $this->command->error('❌ Le rôle Super Administrateur n\'existe pas. Exécutez d\'abord RolesSeeder.');
            return;
        }

        // Utiliser une transaction pour garantir la cohérence
        DB::beginTransaction();

        try {
            // =====================================================
            // SUPER ADMINISTRATEUR PAR DÉFAUT
            // =====================================================
            $superAdminEmail = env('SUPER_ADMIN_EMAIL');

            if ($superAdminEmail) {
                // Recherche directe avec DB::table pour éviter les scopes du modèle
                $superAdmin = DB::table('users')
                    ->whereRaw('LOWER(TRIM(email)) = LOWER(TRIM(?))', [trim($superAdminEmail)])
                    ->first();

                $userData = [
                    'nom_complet' => env('SUPER_ADMIN_NAME'),
                    'password' => Hash::make(env('SUPER_ADMIN_PASSWORD')),
                    'telephone_principal' => env('SUPER_ADMIN_PHONE'),
                    'role_id' => $superAdminRole->id,
                    'statut' => 1,
                    'email_verified_at' => now(),
                    'updated_at' => now(),
                ];

                if ($superAdmin) {
                    // Mise à jour si existe
                    DB::table('users')
                        ->where('id', $superAdmin->id)
                        ->update($userData);
                    $this->command->info('✅ Super Administrateur mis à jour:');
                } else {
                    // Création si n'existe pas
                    $userData['id'] = Str::uuid();
                    $userData['email'] = trim($superAdminEmail);
                    $userData['created_at'] = now();

                    DB::table('users')->insert($userData);
                    $this->command->info('✅ Super Administrateur créé:');
                }

                $this->command->line('   Email: ' . $superAdminEmail);
                $this->command->line('   Nom: ' . env('SUPER_ADMIN_NAME'));

                // Récupérer l'ID pour usage ultérieur
                $superAdminId = $superAdmin->id ?? $userData['id'];
            }

            // =====================================================
            // SUPER ADMINISTRATEUR 2
            // =====================================================
            $superAdmin2Email = env('SUPER_ADMIN2_EMAIL');

            if ($superAdmin2Email) {
                // Recherche directe avec DB::table
                $superAdmin2 = DB::table('users')
                    ->whereRaw('LOWER(TRIM(email)) = LOWER(TRIM(?))', [trim($superAdmin2Email)])
                    ->first();

                $userData2 = [
                    'nom_complet' => env('SUPER_ADMIN2_NAME'),
                    'password' => Hash::make(env('SUPER_ADMIN2_PASSWORD')),
                    'telephone_principal' => env('SUPER_ADMIN2_PHONE'),
                    'role_id' => $superAdminRole->id,
                    'statut' => 1,
                    'email_verified_at' => now(),
                    'updated_at' => now(),
                ];

                if ($superAdmin2) {
                    // Mise à jour si existe
                    DB::table('users')
                        ->where('id', $superAdmin2->id)
                        ->update($userData2);
                    $this->command->info('✅ Super Administrateur 2 mis à jour:');
                } else {
                    // Création si n'existe pas
                    $userData2['id'] = Str::uuid();
                    $userData2['email'] = trim($superAdmin2Email);
                    $userData2['created_at'] = now();

                    DB::table('users')->insert($userData2);
                    $this->command->info('✅ Super Administrateur 2 créé:');
                }

                $this->command->line('   Email: ' . $superAdmin2Email);
                $this->command->line('   Nom: ' . env('SUPER_ADMIN2_NAME'));
            }

            // =====================================================
            // ADMINISTRATEUR PAR DÉFAUT
            // =====================================================
            if ($adminRole) {
                $adminEmail = env('ADMIN_EMAIL');

                if ($adminEmail) {
                    // Recherche directe avec DB::table
                    $admin = DB::table('users')
                        ->whereRaw('LOWER(TRIM(email)) = LOWER(TRIM(?))', [trim($adminEmail)])
                        ->first();

                    $adminData = [
                        'nom_complet' => env('ADMIN_NAME'),
                        'password' => Hash::make(env('ADMIN_PASSWORD')),
                        'telephone_principal' => env('ADMIN_PHONE'),
                        'role_id' => $adminRole->id,
                        'statut' => 1,
                        'email_verified_at' => now(),
                        'updated_at' => now(),
                        'created_by' => $superAdminId ?? null,
                    ];

                    if ($admin) {
                        // Mise à jour si existe
                        DB::table('users')
                            ->where('id', $admin->id)
                            ->update($adminData);
                        $this->command->info('✅ Administrateur mis à jour:');
                    } else {
                        // Création si n'existe pas
                        $adminData['id'] = Str::uuid();
                        $adminData['email'] = trim($adminEmail);
                        $adminData['created_at'] = now();

                        DB::table('users')->insert($adminData);
                        $this->command->info('✅ Administrateur créé:');
                    }

                    $this->command->line('   Email: ' . $adminEmail);
                    $this->command->line('   Nom: ' . env('ADMIN_NAME'));
                }
            }

            DB::commit();

            $this->command->info('');
            $this->command->warn('⚠️  IMPORTANT: Changez ces mots de passe après la première connexion!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Erreur lors de la création des utilisateurs: ' . $e->getMessage());
            throw $e;
        }
    }
}
