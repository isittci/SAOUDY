<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer le rôle Super Admin
        $superAdminRole = Role::where('slug', 'super-admin')->first();

        if (!$superAdminRole) {
            $this->command->error('✗ Rôle super-admin introuvable. Exécutez d\'abord RoleSeeder.');
            return;
        }

        // Liste des super admins à créer
        $superAdmins = [
            [
                'nom_complet' => env('SUPER_ADMIN_NAME', 'Super Admin'),
                'email' => env('SUPER_ADMIN_EMAIL', 'superadmin@yopmail.com'),
                'telephone_principal' => env('SUPER_ADMIN_PHONE', '+2250000000000'),
                'telephone_secondaire' => env('SUPER_ADMIN_PHONE2', '+2250000000001'),
                'password' => env('SUPER_ADMIN_PASSWORD', 'Admin@2025!!'),
            ],
            [
                'nom_complet' => env('SUPER_ADMIN_NAME2', 'Allangba Koné'),
                'email' => env('SUPER_ADMIN_EMAIL2', 'direction@isittci.com'),
                'telephone_principal' => env('SUPER_ADMIN_PHONE3', '+2250000000010'),
                'telephone_secondaire' => env('SUPER_ADMIN_PHONE4', '+2250000000011'),
                'password' => env('SUPER_ADMIN_PASSWORD2', 'Admin@2026!!'),
            ],
        ];

        foreach ($superAdmins as $adminData) {
            // Vérifier si l'utilisateur existe déjà
            $existingUser = User::where('email', $adminData['email'])->first();

            if ($existingUser) {
                $this->command->warn('⚠ Utilisateur déjà existant: ' . $adminData['email']);
                continue;
            }

            // Créer l'utilisateur
            User::create([
                'id' => Str::uuid()->toString(),
                'nom_complet' => $adminData['nom_complet'],
                'email' => $adminData['email'],
                'telephone_principal' => $adminData['telephone_principal'],
                'telephone_secondaire' => $adminData['telephone_secondaire'],
                'role_id' => $superAdminRole->id,
                'password' => Hash::make($adminData['password']),
                'email_verified_at' => now(),
                'statut' => true,
            ]);

            $this->command->info('✓ Super Administrateur créé: ' . $adminData['email']);
        }
    }
}
