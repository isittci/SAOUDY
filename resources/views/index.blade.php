@extends('layouts.main')
@section('title', 'Tableau de bord')
@section('breadcrumb', 'Tableau de bord')

@section('content')
<!-- Filters Bar modernisé -->
<div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
    <div class="px-3 sm:px-4 lg:px-6 py-4">
        <!-- Mobile: Toggle button -->
        <button onclick="toggleFilters()"
            class="md:hidden w-full flex items-center justify-between px-4 py-3 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:border-orange-300 transition-all duration-200 shadow-sm active:scale-98">
            <span class="text-sm font-semibold text-gray-700 flex items-center space-x-2">
                <i class="fas fa-sliders-h text-orange-500"></i>
                <span>Filtres de recherche</span>
            </span>
            <i id="filterToggleIcon" class="fas fa-chevron-down text-gray-500 text-xs transition-transform duration-300"></i>
        </button>

        <!-- Filters Container -->
        <div id="filtersContainer" class="hidden md:block mt-3 md:mt-0">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:flex xl:items-end gap-3 lg:gap-4">

                <!-- Période -->
                <div class="flex flex-col space-y-2">
                    <label class="text-xs lg:text-sm font-semibold text-gray-700 flex items-center space-x-1">
                        <i class="fas fa-calendar-alt text-orange-500 text-xs"></i>
                        <span>Période</span>
                    </label>
                    <div class="flex items-center space-x-2">
                        <input type="date"
                               value="2024-10-15"
                               class="flex-1 px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all" />
                        <span class="text-gray-400 font-medium">→</span>
                        <input type="date"
                               value="2025-11-16"
                               class="flex-1 px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all" />
                    </div>
                </div>

                <!-- Appel d'offre -->
                <div class="flex flex-col space-y-2">
                    <label class="text-xs lg:text-sm font-semibold text-gray-700 flex items-center space-x-1">
                        <i class="fas fa-bullhorn text-orange-500 text-xs"></i>
                        <span>Appel d'offre</span>
                    </label>
                    <select class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all cursor-pointer">
                        <option value="">Tous les appels</option>
                        <option>AOT-2025-001</option>
                        <option>AOS-2025-002</option>
                        <option>AOF-2025-003</option>
                    </select>
                </div>

                <!-- État appel d'offre -->
                <div class="flex flex-col space-y-2">
                    <label class="text-xs lg:text-sm font-semibold text-gray-700 flex items-center space-x-1">
                        <i class="fas fa-info-circle text-orange-500 text-xs"></i>
                        <span>État</span>
                    </label>
                    <select class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all cursor-pointer">
                        <option value="">Tous les états</option>
                        <option>En cours</option>
                        <option>Expiré</option>
                        <option>Publié</option>
                        <option>Brouillon</option>
                    </select>
                </div>

                <!-- Type d'appel d'offre -->
                <div class="flex flex-col space-y-2">
                    <label class="text-xs lg:text-sm font-semibold text-gray-700 flex items-center space-x-1">
                        <i class="fas fa-tags text-orange-500 text-xs"></i>
                        <span>Type</span>
                    </label>
                    <select class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all cursor-pointer">
                        <option value="">Tous les types</option>
                        <option>Appel d'offre Travaux</option>
                        <option>Appel d'offre Services</option>
                        <option>Appel d'offre Fournitures</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-center space-x-2 sm:col-span-2 lg:col-span-4 xl:col-span-1">
                    <button class="flex-1 xl:flex-none px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center justify-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                        <i class="fas fa-search text-sm"></i>
                        <span class="text-sm">Rechercher</span>
                    </button>
                    <button class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all duration-200 flex items-center justify-center shadow-sm active:scale-95">
                        <i class="fas fa-redo text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Area avec design moderne -->
<main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
        <!-- Card 1 -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-5 text-white transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <i class="fas fa-bullhorn text-2xl"></i>
                </div>
                <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded-lg">+12%</span>
            </div>
            <h3 class="text-3xl font-bold mb-1">24</h3>
            <p class="text-blue-100 text-sm font-medium">Appels d'offres actifs</p>
        </div>

        <!-- Card 2 -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-5 text-white transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-tie text-2xl"></i>
                </div>
                <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded-lg">+8%</span>
            </div>
            <h3 class="text-3xl font-bold mb-1">156</h3>
            <p class="text-green-100 text-sm font-medium">Prestataires inscrits</p>
        </div>

        <!-- Card 3 -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-5 text-white transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-signature text-2xl"></i>
                </div>
                <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded-lg">+5%</span>
            </div>
            <h3 class="text-3xl font-bold mb-1">18</h3>
            <p class="text-orange-100 text-sm font-medium">Attributions en cours</p>
        </div>

        <!-- Card 4 -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-5 text-white transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <i class="fas fa-coins text-2xl"></i>
                </div>
                <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded-lg">+15%</span>
            </div>
            <h3 class="text-3xl font-bold mb-1">2.4M</h3>
            <p class="text-purple-100 text-sm font-medium">Budget total (FCFA)</p>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-5 lg:p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-800 flex items-center space-x-2">
                    <i class="fas fa-history text-orange-500"></i>
                    <span>Activités récentes</span>
                </h2>
                <button class="text-sm text-orange-500 hover:text-orange-600 font-medium transition-colors">
                    Voir tout
                </button>
            </div>

            <div class="space-y-4">
                <!-- Activity Item -->
                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-200 cursor-pointer">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-plus text-blue-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 mb-1">Nouvel appel d'offres créé</p>
                        <p class="text-xs text-gray-600 mb-2">AOT-2025-045 - Construction d'un bâtiment administratif</p>
                        <span class="text-xs text-gray-500">Il y a 2 heures</span>
                    </div>
                </div>

                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-200 cursor-pointer">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 mb-1">Attribution validée</p>
                        <p class="text-xs text-gray-600 mb-2">AOS-2025-032 attribué à SARL TECH SOLUTIONS</p>
                        <span class="text-xs text-gray-500">Il y a 5 heures</span>
                    </div>
                </div>

                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-200 cursor-pointer">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user-plus text-orange-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 mb-1">Nouveau prestataire inscrit</p>
                        <p class="text-xs text-gray-600 mb-2">ENTREPRISE BATIMENT CI</p>
                        <span class="text-xs text-gray-500">Il y a 1 jour</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-lg p-5 lg:p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center space-x-2">
                <i class="fas fa-bolt text-orange-500"></i>
                <span>Actions rapides</span>
            </h2>

            <div class="space-y-3">
                <button class="w-full flex items-center space-x-3 p-4 bg-gradient-to-r from-orange-50 to-transparent hover:from-orange-100 border-l-4 border-orange-500 rounded-lg transition-all duration-200 group">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                        <i class="fas fa-plus text-orange-600"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Créer un appel d'offres</span>
                </button>

                <button class="w-full flex items-center space-x-3 p-4 bg-gradient-to-r from-blue-50 to-transparent hover:from-blue-100 border-l-4 border-blue-500 rounded-lg transition-all duration-200 group">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-user-plus text-blue-600"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Ajouter un prestataire</span>
                </button>

                <button class="w-full flex items-center space-x-3 p-4 bg-gradient-to-r from-green-50 to-transparent hover:from-green-100 border-l-4 border-green-500 rounded-lg transition-all duration-200 group">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                        <i class="fas fa-file-alt text-green-600"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Consulter les rapports</span>
                </button>

                <button class="w-full flex items-center space-x-3 p-4 bg-gradient-to-r from-purple-50 to-transparent hover:from-purple-100 border-l-4 border-purple-500 rounded-lg transition-all duration-200 group">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                        <i class="fas fa-chart-bar text-purple-600"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Voir les statistiques</span>
                </button>
            </div>
        </div>
    </div>
</main>
@endsection
