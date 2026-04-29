@extends('layouts.main')

@section('title', 'Sauvegardes')

@section('breadcrumb')
    <span class="text-white font-medium">Sauvegardes</span>
@endsection

@section('content')
    <!-- Header simplifié (fixe) -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Sauvegardes</h1>
                    <p class="text-gray-600 mt-1 text-sm">Gestion des sauvegardes de la base de données</p>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Bouton toggle statistiques sur mobile -->
                    <button type="button" onclick="toggleStats()"
                        class="lg:hidden inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all duration-200">
                        <i class="fas fa-chart-bar mr-2"></i>
                        <span class="text-sm">Stats</span>
                        <i id="statsChevron" class="fas fa-chevron-down ml-2 text-xs transition-transform duration-200"></i>
                    </button>

                    <!-- Purger les sauvegardes expirées -->
                    <form action="{{ route('sauvegardes.purger') }}" method="POST" id="purgerForm">
                        @csrf
                        <button type="button" onclick="openPurgerModal()"
                            class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg transition-all duration-200 shadow-md">
                            <i class="fas fa-broom mr-2"></i>
                            <span class="hidden sm:inline">Purger les expirées</span>
                            <span class="sm:hidden">Purger</span>
                        </button>
                    </form>

                    <!-- Nouvelle sauvegarde manuelle -->
                    <form action="{{ route('sauvegardes.store') }}" method="POST" id="storeForm">
                        @csrf
                        <button type="button" onclick="openStoreModal()"
                            class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 shadow-md">
                            <i class="fas fa-plus mr-2"></i>
                            <span class="hidden sm:inline">Nouvelle Sauvegarde</span>
                            <span class="sm:hidden">Nouveau</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content (scrollable) -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @include('partials.alerts')

        <!-- Statistiques (dans le main, donc scrollable) -->
        <div id="statsSection" class="hidden lg:block mb-6">
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                <!-- Total -->
                <div class="bg-white rounded-xl p-3 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 uppercase">Total</span>
                        <i class="fas fa-database text-gray-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
                </div>
                <!-- Terminées -->
                <div class="bg-white rounded-xl p-3 border border-green-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-green-600 uppercase">Terminées</span>
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['terminées'] }}</p>
                </div>
                <!-- Échecs -->
                <div class="bg-white rounded-xl p-3 border border-red-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-red-600 uppercase">Échecs</span>
                        <i class="fas fa-times-circle text-red-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['echecs'] }}</p>
                </div>
                <!-- Dernière sauvegarde -->
                <div class="bg-white rounded-xl p-3 border border-blue-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-blue-600 uppercase">Dernière</span>
                        <i class="fas fa-history text-blue-400"></i>
                    </div>
                    <p class="text-sm font-bold text-blue-600 mt-1">
                        {{ $stats['derniere'] ? $stats['derniere']->created_at->format('d/m/Y H:i') : '—' }}
                    </p>
                </div>
            </div>

            <!-- Résumé stockage -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-orange-100 text-xs font-medium uppercase truncate">Espace total utilisé</p>
                            @php
                                $octets = $stats['taille_totale'] ?? 0;
                                if ($octets >= 1_073_741_824) {
                                    $tailleFormatee = round($octets / 1_073_741_824, 2) . ' Go';
                                } elseif ($octets >= 1_048_576) {
                                    $tailleFormatee = round($octets / 1_048_576, 2) . ' Mo';
                                } elseif ($octets >= 1_024) {
                                    $tailleFormatee = round($octets / 1_024, 2) . ' Ko';
                                } else {
                                    $tailleFormatee = $octets . ' o';
                                }
                            @endphp
                            <p class="text-lg sm:text-xl font-bold mt-1 truncate">{{ $tailleFormatee }}</p>
                        </div>
                        <i class="fas fa-hdd text-2xl sm:text-3xl text-orange-300 opacity-50 ml-2 flex-shrink-0"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-blue-100 text-xs font-medium uppercase truncate">Dernière sauvegarde</p>
                            <p class="text-lg sm:text-xl font-bold mt-1 truncate">
                                @if ($stats['derniere'])
                                    {{ $stats['derniere']->nom_fichier }}
                                @else
                                    Aucune sauvegarde terminée
                                @endif
                            </p>
                        </div>
                        <i class="fas fa-clock text-2xl sm:text-3xl text-blue-300 opacity-50 ml-2 flex-shrink-0"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div
                class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-base sm:text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-filter text-orange-500 mr-2"></i>
                    Filtres
                </h2>
                <!-- Bouton toggle filtres sur mobile -->
                <button type="button" onclick="toggleFilter()" class="sm:hidden p-2 text-gray-500 hover:text-gray-700">
                    <i id="filtersChevron" class="fas fa-chevron-down transition-transform duration-200"></i>
                </button>
            </div>
            <div id="filtersSection" class="p-4 sm:p-6 hidden sm:block">
                <form action="{{ route('sauvegardes.index') }}" method="GET"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400">
                            <option value="">Tous les types</option>
                            <option value="manuelle" {{ request('type') === 'manuelle' ? 'selected' : '' }}>Manuelle
                            </option>
                            <option value="automatique" {{ request('type') === 'automatique' ? 'selected' : '' }}>
                                Automatique</option>
                        </select>
                    </div>
                    <!-- Statut -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400">
                            <option value="">Tous les statuts</option>
                            <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours
                            </option>
                            <option value="terminee" {{ request('statut') === 'terminee' ? 'selected' : '' }}>Terminée
                            </option>
                            <option value="echec" {{ request('statut') === 'echec' ? 'selected' : '' }}>Échec</option>
                        </select>
                    </div>
                    <!-- Boutons -->
                    <div class="flex items-end space-x-2 sm:col-span-2 lg:col-span-2">
                        <button type="submit"
                            class="flex-1 sm:flex-none px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all">
                            <i class="fas fa-search mr-2"></i>Filtrer
                        </button>
                        <a href="{{ route('sauvegardes.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des sauvegardes -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                <h2 class="text-base sm:text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-database text-indigo-500 mr-2"></i>
                    Liste des sauvegardes
                    <span class="ml-2 px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-full">
                        {{ $sauvegardes->total() }}
                    </span>
                </h2>
            </div>

            @if ($sauvegardes->isEmpty())
                <div class="p-8 sm:p-12 text-center">
                    <i class="fas fa-database text-5xl sm:text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-600 mb-2">Aucune sauvegarde trouvée</h3>
                    <p class="text-gray-500 mb-4">Lancez une nouvelle sauvegarde manuelle pour démarrer.</p>
                    <form action="{{ route('sauvegardes.store') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700">
                            <i class="fas fa-plus mr-2"></i>Nouvelle Sauvegarde
                        </button>
                    </form>
                </div>
            @else
                <!-- ============================================================ -->
                <!-- Vue mobile : cartes avec détails dépliables                  -->
                <!-- ============================================================ -->
                <div class="block lg:hidden divide-y divide-gray-200">
                    @foreach ($sauvegardes as $sauvegarde)
                        <div class="p-4 hover:bg-gray-50">
                            <!-- En-tête de la carte (cliquable pour déplier) -->
                            <div class="flex items-start justify-between mb-3 cursor-pointer"
                                onclick="toggleMobileCard('mobile-{{ $sauvegarde->id }}')">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-database text-orange-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm truncate max-w-[180px]"
                                            title="{{ $sauvegarde->nom_fichier }}">
                                            {{ Str::limit($sauvegarde->nom_fichier, 28) }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $sauvegarde->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $sauvegarde->statut_badge_class }}">
                                        @if ($sauvegarde->statut === 'terminee')
                                            <i class="fas fa-check-circle mr-1"></i>
                                        @elseif($sauvegarde->statut === 'en_cours')
                                            <i class="fas fa-spinner fa-spin mr-1"></i>
                                        @else
                                            <i class="fas fa-times-circle mr-1"></i>
                                        @endif
                                        {{ $sauvegarde->statut_label }}
                                    </span>
                                    <div id="mobile-chevron-{{ $sauvegarde->id }}">
                                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Résumé visible -->
                            <div class="flex items-center space-x-3 mb-3">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium
                                    {{ $sauvegarde->type === 'manuelle' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    <i
                                        class="fas fa-{{ $sauvegarde->type === 'manuelle' ? 'hand-pointer' : 'robot' }} mr-1"></i>
                                    {{ ucfirst($sauvegarde->type) }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-weight-hanging mr-1"></i>
                                    {{ $sauvegarde->taille_formatee }}
                                </span>
                            </div>

                            <!-- Détails dépliables -->
                            <div id="mobile-{{ $sauvegarde->id }}" class="hidden mt-3 space-y-3">
                                <div class="grid grid-cols-2 gap-2">
                                    <!-- Créé par -->
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <i class="fas fa-user text-gray-500 text-sm"></i>
                                            <span class="text-xs text-gray-600 uppercase">Créé par</span>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $sauvegarde->creeePar?->nom_complet ?? 'Système' }}
                                        </p>
                                    </div>
                                    <!-- Expiration -->
                                    <div class="bg-yellow-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <i class="fas fa-calendar-times text-yellow-600 text-sm"></i>
                                            <span class="text-xs text-yellow-700 uppercase">Expire le</span>
                                        </div>
                                        <p class="text-sm font-semibold text-yellow-800">
                                            {{ $sauvegarde->expire_a ? $sauvegarde->expire_a->format('d/m/Y') : 'Permanent' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Message d'erreur si présent -->
                                @if ($sauvegarde->message_erreur)
                                    <div class="bg-red-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                                            <span class="text-xs text-red-700 uppercase">Erreur</span>
                                        </div>
                                        <p class="text-xs text-red-700 break-all">
                                            {{ Str::limit($sauvegarde->message_erreur, 100) }}</p>
                                    </div>
                                @endif

                                <!-- Checksum -->
                                @if ($sauvegarde->checksum_md5)
                                    <div class="bg-indigo-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <i class="fas fa-fingerprint text-indigo-600 text-sm"></i>
                                            <span class="text-xs text-indigo-700 uppercase">MD5</span>
                                        </div>
                                        <p class="text-xs text-indigo-700 font-mono break-all">
                                            {{ $sauvegarde->checksum_md5 }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Actions mobiles -->
                            <div class="flex items-center justify-end space-x-2 pt-2 border-t border-gray-100 mt-3">
                                @if ($sauvegarde->statut === 'terminee' && $sauvegarde->fichier_existe)
                                    <a href="{{ route('sauvegardes.download', $sauvegarde) }}"
                                        class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                        title="Télécharger">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    {{-- <a href="{{ route('sauvegardes.verifier', $sauvegarde) }}"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Vérifier l'intégrité">
                                        <i class="fas fa-shield-alt"></i>
                                    </a> --}}
                                    <button onclick="verifierIntegrite('{{ $sauvegarde->id }}')"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Vérifier l'intégrité">
                                        <i class="fas fa-shield-alt"></i>
                                    </button>
                                @endif
                                <button
                                    onclick="openDeleteModal('{{ $sauvegarde->id }}', '{{ addslashes($sauvegarde->nom_fichier) }}')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- ============================================================ -->
                <!-- Vue desktop : tableau avec lignes dépliables                 -->
                <!-- ============================================================ -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-10">
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Fichier</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Type</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Statut</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Taille</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Date</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Expire le</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Créé par</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($sauvegardes as $sauvegarde)
                                <!-- Ligne principale -->
                                <tr class="hover:bg-gray-50 transition-colors duration-150 cursor-pointer"
                                    onclick="toggleRow('{{ $sauvegarde->id }}')">
                                    <!-- Chevron -->
                                    <td class="px-4 py-3 text-center">
                                        <div id="chevron-{{ $sauvegarde->id }}"
                                            class="w-7 h-7 rounded-full bg-gray-100 hover:bg-orange-100 flex items-center justify-center transition-colors mx-auto">
                                            <i
                                                class="fas fa-chevron-right text-gray-500 text-xs transition-transform duration-200"></i>
                                        </div>
                                    </td>

                                    <!-- Nom fichier -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-9 h-9 bg-gradient-to-br from-orange-100 to-orange-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-database text-orange-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900 max-w-xs truncate"
                                                    title="{{ $sauvegarde->nom_fichier }}">
                                                    {{ $sauvegarde->nom_fichier }}
                                                </p>
                                                @if ($sauvegarde->checksum_md5)
                                                    <p class="text-xs text-gray-400 font-mono">
                                                        {{ Str::limit($sauvegarde->checksum_md5, 16) }}…</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Type -->
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                            {{ $sauvegarde->type === 'manuelle' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            <i
                                                class="fas fa-{{ $sauvegarde->type === 'manuelle' ? 'hand-pointer' : 'robot' }} mr-1"></i>
                                            {{ ucfirst($sauvegarde->type) }}
                                        </span>
                                    </td>

                                    <!-- Statut -->
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $sauvegarde->statut_badge_class }}">
                                            @if ($sauvegarde->statut === 'terminee')
                                                <i class="fas fa-check-circle mr-1"></i>
                                            @elseif($sauvegarde->statut === 'en_cours')
                                                <i class="fas fa-spinner fa-spin mr-1"></i>
                                            @else
                                                <i class="fas fa-times-circle mr-1"></i>
                                            @endif
                                            {{ $sauvegarde->statut_label }}
                                        </span>
                                    </td>

                                    <!-- Taille -->
                                    <td class="px-4 py-3 text-right">
                                        <span
                                            class="text-sm font-medium text-gray-700">{{ $sauvegarde->taille_formatee }}</span>
                                    </td>

                                    <!-- Date création -->
                                    <td class="px-4 py-3">
                                        <div>
                                            <p class="text-sm text-gray-800">
                                                {{ $sauvegarde->created_at->format('d/m/Y') }}</p>
                                            <p class="text-xs text-gray-500">
                                                {{ $sauvegarde->created_at->format('H:i:s') }}</p>
                                        </div>
                                    </td>

                                    <!-- Expiration -->
                                    <td class="px-4 py-3">
                                        @if ($sauvegarde->expire_a)
                                            <div>
                                                <p
                                                    class="text-sm {{ $sauvegarde->expire_a->isPast() ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                                    {{ $sauvegarde->expire_a->format('d/m/Y') }}
                                                </p>
                                                @if ($sauvegarde->expire_a->isPast())
                                                    <p class="text-xs text-red-500">Expirée</p>
                                                @else
                                                    <p class="text-xs text-gray-400">
                                                        {{ $sauvegarde->expire_a->diffForHumans() }}</p>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Permanent</span>
                                        @endif
                                    </td>

                                    <!-- Créé par -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-2">
                                            <div
                                                class="w-7 h-7 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-user text-gray-500 text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-800">
                                                    {{ $sauvegarde->creeePar?->nom_complet ?? 'Système' }}
                                                </p>
                                                @if ($sauvegarde->ip_declencheur)
                                                    <p class="text-xs text-gray-400">{{ $sauvegarde->ip_declencheur }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-3 text-center" onclick="event.stopPropagation()">
                                        <div class="flex items-center justify-center space-x-1">
                                            @if ($sauvegarde->statut === 'terminee' && $sauvegarde->fichier_existe)
                                                <a href="{{ route('sauvegardes.download', $sauvegarde) }}"
                                                    class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                                    title="Télécharger">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                {{-- <a href="{{ route('sauvegardes.verifier', $sauvegarde) }}"
                                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                    title="Vérifier l'intégrité">
                                                    <i class="fas fa-shield-alt"></i>
                                                </a> --}}
                                                <button onclick="verifierIntegrite('{{ $sauvegarde->id }}')"
                                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                    title="Vérifier l'intégrité">
                                                    <i class="fas fa-shield-alt"></i>
                                                </button>
                                            @endif
                                            <button
                                                onclick="openDeleteModal('{{ $sauvegarde->id }}', '{{ addslashes($sauvegarde->nom_fichier) }}')"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Ligne de détails dépliable -->
                                <tr id="details-{{ $sauvegarde->id }}" class="hidden bg-gray-50">
                                    <td colspan="9" class="px-6 py-4">
                                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                                            <!-- Informations générales -->
                                            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                                                <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
                                                    <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                                                    Informations générales
                                                </h4>
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">Nom fichier</span>
                                                        <span
                                                            class="font-medium text-gray-800 text-right max-w-[200px] truncate"
                                                            title="{{ $sauvegarde->nom_fichier }}">
                                                            {{ $sauvegarde->nom_fichier }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">Chemin</span>
                                                        <span
                                                            class="font-medium text-gray-800 text-right max-w-[200px] truncate font-mono text-xs"
                                                            title="{{ $sauvegarde->chemin_stockage }}">
                                                            {{ $sauvegarde->chemin_stockage }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">Taille</span>
                                                        <span
                                                            class="font-medium text-gray-800">{{ $sauvegarde->taille_formatee }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">Fichier présent</span>
                                                        <span
                                                            class="{{ $sauvegarde->fichier_existe ? 'text-green-600' : 'text-red-600' }} font-medium">
                                                            <i
                                                                class="fas fa-{{ $sauvegarde->fichier_existe ? 'check' : 'times' }} mr-1"></i>
                                                            {{ $sauvegarde->fichier_existe ? 'Oui' : 'Non' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Traçabilité -->
                                            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                                                <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
                                                    <i class="fas fa-history text-orange-500 mr-2"></i>
                                                    Traçabilité
                                                </h4>
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">Créé par</span>
                                                        <span class="font-medium text-gray-800">
                                                            {{ $sauvegarde->creeePar?->nom_complet ?? 'Système' }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">IP déclencheur</span>
                                                        <span class="font-mono text-xs text-gray-700">
                                                            {{ $sauvegarde->ip_declencheur ?? '—' }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">Créée le</span>
                                                        <span class="font-medium text-gray-800">
                                                            {{ $sauvegarde->created_at->format('d/m/Y à H:i:s') }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">Expire le</span>
                                                        <span
                                                            class="font-medium {{ $sauvegarde->expire_a?->isPast() ? 'text-red-600' : 'text-gray-800' }}">
                                                            {{ $sauvegarde->expire_a ? $sauvegarde->expire_a->format('d/m/Y') : 'Permanent' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Intégrité & tables -->
                                            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                                                <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
                                                    <i class="fas fa-fingerprint text-blue-500 mr-2"></i>
                                                    Intégrité & contenu
                                                </h4>
                                                <div class="space-y-2 text-sm">
                                                    <div>
                                                        <span class="text-gray-500 block mb-1">Checksum MD5</span>
                                                        @if ($sauvegarde->checksum_md5)
                                                            <span class="font-mono text-xs text-gray-700 break-all">
                                                                {{ $sauvegarde->checksum_md5 }}
                                                            </span>
                                                        @else
                                                            <span class="text-gray-400 italic">Non calculé</span>
                                                        @endif
                                                    </div>
                                                    @if ($sauvegarde->message_erreur)
                                                        <div class="mt-2 p-2 bg-red-50 rounded-lg border border-red-200">
                                                            <p class="text-xs text-red-700 break-all">
                                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                                {{ $sauvegarde->message_erreur }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                    @if ($sauvegarde->tables_incluses)
                                                        <div class="mt-2">
                                                            <span class="text-gray-500 block mb-1">Tables incluses</span>
                                                            <div class="flex flex-wrap gap-1">
                                                                @foreach ($sauvegarde->tables_incluses as $table)
                                                                    <span
                                                                        class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded text-xs">{{ $table }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                    {{ $sauvegardes->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- ============================================================ -->
    <!-- Modal de suppression                                          -->
    <!-- ============================================================ -->
    <div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeDeleteModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Confirmer la suppression
                    </h3>
                </div>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="p-6">
                        <p class="text-gray-600">
                            Êtes-vous sûr de vouloir supprimer la sauvegarde<br>
                            <strong id="deleteSauvegardeNom" class="break-all"></strong> ?
                        </p>
                        <p class="text-sm text-red-600 mt-2">
                            Cette action supprimera également le fichier physique. Elle est irréversible.
                        </p>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeDeleteModal()"
                            class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">
                            Supprimer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- ============================================================ -->
<!-- Modal Vérification Intégrité                                  -->
<!-- ============================================================ -->
<div id="verifierModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeVerifierModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full mx-4 overflow-hidden">

            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-shield-alt text-blue-500 mr-2"></i>Vérification d'intégrité
                </h3>
                <button onclick="closeVerifierModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Corps -->
            <div class="p-6">

                <!-- Chargement -->
                <div id="verifier-loading" class="flex flex-col items-center py-6">
                    <i class="fas fa-spinner fa-spin text-blue-500 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Vérification en cours…</p>
                </div>

                <!-- Résultat -->
                <div id="verifier-result" class="hidden space-y-4">

                    <!-- Badge statut -->
                    <div id="verifier-badge" class="flex items-center justify-center p-4 rounded-xl"></div>

                    <!-- Détails checksums -->
                    <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">Checksum stocké en base</p>
                            <p id="verifier-checksum-stocke"
                                class="font-mono text-xs text-gray-800 break-all bg-white p-2 rounded border border-gray-200">
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-1">Checksum actuel du fichier</p>
                            <p id="verifier-checksum-actuel"
                                class="font-mono text-xs text-gray-800 break-all bg-white p-2 rounded border border-gray-200">
                            </p>
                        </div>
                    </div>

                    <!-- Message -->
                    <div id="verifier-message" class="text-sm text-center font-medium"></div>
                </div>

                <!-- Erreur réseau -->
                <div id="verifier-error" class="hidden flex flex-col items-center py-6">
                    <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-3"></i>
                    <p id="verifier-error-message" class="text-red-600 text-sm text-center"></p>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end">
                <button onclick="closeVerifierModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- ============================================================ -->
    <!-- Modal confirmation nouvelle sauvegarde                       -->
    <!-- ============================================================ -->
    <div id="storeModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeStoreModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-database text-orange-500 mr-2"></i>Lancer une sauvegarde manuelle
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600">
                        Vous êtes sur le point de déclencher une sauvegarde manuelle de la base de données.
                    </p>
                    <p class="text-sm text-gray-500 mt-2">
                        Cette opération peut prendre quelques instants selon la taille de la base.
                    </p>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                    <button type="button" onclick="closeStoreModal()"
                        class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="button" onclick="document.getElementById('storeForm').submit()"
                        class="px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg">
                        <i class="fas fa-play mr-2"></i>Lancer la sauvegarde
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- Modal confirmation purge                                      -->
    <!-- ============================================================ -->
    <div id="purgerModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closePurgerModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-broom text-red-500 mr-2"></i>Purger les sauvegardes expirées
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600">
                        Toutes les sauvegardes dont la date d'expiration est dépassée seront <strong>définitivement
                            supprimées</strong>, y compris leurs fichiers physiques.
                    </p>
                    <p class="text-sm text-red-600 mt-2">Cette action est irréversible.</p>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                    <button type="button" onclick="closePurgerModal()"
                        class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="button" onclick="document.getElementById('purgerForm').submit()"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">
                        <i class="fas fa-broom mr-2"></i>Purger
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // -------------------------------------------------------
        // Toggle ligne dépliable (desktop)
        // -------------------------------------------------------
        function toggleRow(id) {
            const detailsRow = document.getElementById('details-' + id);
            const chevron = document.getElementById('chevron-' + id);
            const icon = chevron.querySelector('i');

            if (detailsRow.classList.contains('hidden')) {
                detailsRow.classList.remove('hidden');
                icon.classList.add('rotate-90');
            } else {
                detailsRow.classList.add('hidden');
                icon.classList.remove('rotate-90');
            }
        }

        // -------------------------------------------------------
        // Toggle carte dépliable (mobile)
        // -------------------------------------------------------
        function toggleMobileCard(id) {
            const detailsDiv = document.getElementById(id);
            const rawId = id.replace('mobile-', '');
            const chevron = document.getElementById('mobile-chevron-' + rawId);
            const icon = chevron ? chevron.querySelector('i') : null;

            if (detailsDiv.classList.contains('hidden')) {
                detailsDiv.classList.remove('hidden');
                if (icon) icon.classList.add('rotate-180');
            } else {
                detailsDiv.classList.add('hidden');
                if (icon) icon.classList.remove('rotate-180');
            }
        }

        // -------------------------------------------------------
        // Toggle statistiques (mobile)
        // -------------------------------------------------------
        function toggleStats() {
            const statsSection = document.getElementById('statsSection');
            const chevron = document.getElementById('statsChevron');
            statsSection.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        // -------------------------------------------------------
        // Toggle filtres (mobile)
        // -------------------------------------------------------
        function toggleFilter() {
            const filtersSection = document.getElementById('filtersSection');
            const chevron = document.getElementById('filtersChevron');
            filtersSection.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        // -------------------------------------------------------
        // Modal Suppression
        // -------------------------------------------------------
        function openDeleteModal(id, nom) {
            const route = "{{ route('sauvegardes.destroy', ':id') }}".replace(':id', id);
            document.getElementById('deleteForm').action = route;
            document.getElementById('deleteSauvegardeNom').textContent = nom;
            document.getElementById('deleteModal').classList.remove('hidden');
        }


        // -------------------------------------------------------
// Vérification intégrité via AJAX
// -------------------------------------------------------
function verifierIntegrite(id) {
    // Réinitialiser la modal
    document.getElementById('verifier-loading').classList.remove('hidden');
    document.getElementById('verifier-result').classList.add('hidden');
    document.getElementById('verifier-error').classList.add('hidden');
    document.getElementById('verifierModal').classList.remove('hidden');

    // Construire l'URL dynamiquement
    const url = "{{ route('sauvegardes.verifier', ':id') }}".replace(':id', id);

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Erreur serveur : ' + response.status);
        return response.json();
    })
    .then(data => {
        document.getElementById('verifier-loading').classList.add('hidden');
        document.getElementById('verifier-result').classList.remove('hidden');

        // Badge succès / échec
        const badge = document.getElementById('verifier-badge');
        if (data.valide) {
            badge.className = 'flex items-center justify-center p-4 rounded-xl bg-green-50 border border-green-200';
            badge.innerHTML = '<i class="fas fa-check-circle text-green-500 text-3xl mr-3"></i>'
                + '<span class="text-green-700 font-bold text-lg">Fichier intact</span>';
        } else {
            badge.className = 'flex items-center justify-center p-4 rounded-xl bg-red-50 border border-red-200';
            badge.innerHTML = '<i class="fas fa-times-circle text-red-500 text-3xl mr-3"></i>'
                + '<span class="text-red-700 font-bold text-lg">Fichier corrompu</span>';
        }

        // Checksums
        document.getElementById('verifier-checksum-stocke').textContent  = data.checksum_stocke ?? '—';
        document.getElementById('verifier-checksum-actuel').textContent  = data.checksum_actuel ?? '—';

        // Message
        const msg = document.getElementById('verifier-message');
        msg.textContent  = data.message;
        msg.className    = 'text-sm text-center font-medium ' + (data.valide ? 'text-green-600' : 'text-red-600');
    })
    .catch(err => {
        document.getElementById('verifier-loading').classList.add('hidden');
        document.getElementById('verifier-error').classList.remove('hidden');
        document.getElementById('verifier-error-message').textContent = err.message;
    });
}

function closeVerifierModal() {
    document.getElementById('verifierModal').classList.add('hidden');
}

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // -------------------------------------------------------
        // Modal Nouvelle sauvegarde
        // -------------------------------------------------------
        function openStoreModal() {
            document.getElementById('storeModal').classList.remove('hidden');
        }

        function closeStoreModal() {
            document.getElementById('storeModal').classList.add('hidden');
        }

        // -------------------------------------------------------
        // Modal Purge
        // -------------------------------------------------------
        function openPurgerModal() {
            document.getElementById('purgerModal').classList.remove('hidden');
        }

        function closePurgerModal() {
            document.getElementById('purgerModal').classList.add('hidden');
        }

        // -------------------------------------------------------
        // Touche Escape pour fermer les modals
        // -------------------------------------------------------
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
                closeStoreModal();
                closePurgerModal();

                closeVerifierModal();
            }
        });

        // -------------------------------------------------------
        // Sur desktop (lg+), toujours afficher les stats
        // -------------------------------------------------------
        function handleResize() {
            const statsSection = document.getElementById('statsSection');
            if (window.innerWidth >= 1024) {
                statsSection.classList.remove('hidden');
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize();
    </script>
@endpush
