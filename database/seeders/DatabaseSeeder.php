<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->command->info('');
        $this->command->info('🚀 Démarrage du seeding de la base de données...');
        $this->command->info('');

        // 1. Créer les permissions
        $this->command->info('📋 Création des permissions...');
        $this->call(PermissionsSeeder::class);
        $this->command->info('');

        // 2. Créer les rôles avec leurs permissions
        $this->command->info('👥 Création des rôles...');
        $this->call(RolesSeeder::class);
        $this->command->info('');

        // 3. Créer les utilisateurs par défaut
        $this->command->info('👤 Création des utilisateurs...');
        $this->call(UsersSeeder::class);
        $this->command->info('');

        $this->command->info('✅ Seeding terminé avec succès!');
        $this->command->info('');
    }
}
