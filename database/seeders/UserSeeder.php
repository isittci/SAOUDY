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
        // Récupérer les rôles
        $superAdminRole = Role::where('slug', 'super-admin')->first();

        $superAdmin = User::where('email', env('SUPER_ADMIN_EMAIL', 'superadmin@yopmail.com'))->first();
        if($superAdmin) return;


        // Créer le Super Administrateur
        $superAdmin = User::create([
            'id' => Str::uuid()->toString(),
            'nom_complet' => env('SUPER_ADMIN_NAME', 'Suer Admin'),
            'email' => env('SUPER_ADMIN_EMAIL', 'superadmin@yopmail.com'),
            'telephone_principal' => env('SUPER_ADMIN_PHONE', '+2250000000000'),
            'telepone_secondaire' => env('SUPER_ADMIN_PHONE2', '+2250000000001'),
            'role_id' => $superAdminRole->id,
            'password' => Hash::make(env('SUPER_ADMIN_PASSWORD')),
            'email_verified_at' => now(),
            'statut' => true,
        ]);
        $this->command->info('✓ Super Administrateur créé: '.env('SUPER_ADMIN_EMAIL', 'superadmin@yopmail.com'));
    }
}
