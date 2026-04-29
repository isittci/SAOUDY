@extends('layouts.main')
@section('title', 'Proformas')
@section('breadcrumb', 'Proformas')

@section('content')
    <!-- Filters Bar -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et bouton créer -->
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-file-invoice-dollar text-orange-500"></i>
                        <span>Proformas</span>
                    </h1>
                </div>

                <!-- Filtres et actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Recherche -->
                    <div class="relative flex-1 sm:min-w-[350px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Rechercher par numéro..."
                            value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages de succès/erreur -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-orange-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Actives</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['actives'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Inactives</p>
                        <p class="text-2xl font-bold text-gray-600">{{ $stats['inactives'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-pause-circle text-gray-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Utilisées</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['utilisees'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-link text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Montant Total</p>
                        <p class="text-lg font-bold text-purple-600">
                            {{ number_format(floor($stats['montant_total'] ?? 0), 0, ',', ' ') }}</p>
                        <p class="text-xs text-gray-400">FCFA</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-coins text-purple-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau -->
        <!-- Tableau amélioré -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <!-- En-tête du tableau avec gradient orange -->
            <div class="px-6 py-4 border-b border-orange-200 bg-gradient-to-r from-orange-500 to-orange-600">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-list-alt mr-2"></i>
                        Liste des proformas (<span id="totalCount">{{ $proformas->total() }}</span>)
                    </h2>
                    <div class="flex items-center space-x-2">
                        <button onclick="refreshTable()"
                            class="px-3 py-2 text-white hover:bg-white/20 rounded-lg transition-all duration-200"
                            title="Actualiser">
                            <i class="fas fa-sync-alt text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table responsive -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead
                        class="bg-gradient-to-r from-orange-50 to-amber-50 border-b-2 border-orange-200 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-orange-800 uppercase tracking-wider">
                                <div
                                    class="flex items-center space-x-1 cursor-pointer hover:text-orange-600 transition-colors">
                                    <i class="fas fa-hashtag text-orange-500"></i>
                                    <span>Numéro / Version</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-orange-800 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-calendar text-orange-500"></i>
                                    <span>Date</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-orange-800 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-users text-orange-500"></i>
                                    <span>Prestataire / Lot</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-orange-800 uppercase tracking-wider">
                                <div class="flex items-center justify-end space-x-1">
                                    <i class="fas fa-money-bill text-orange-500"></i>
                                    <span>HT</span>
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-right text-xs font-bold text-orange-800 uppercase tracking-wider  lg:table-cell">
                                <div class="flex items-center justify-end space-x-1">
                                    <i class="fas fa-percent text-orange-500"></i>
                                    <span>Remise</span>
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-right text-xs font-bold text-orange-800 uppercase tracking-wider  lg:table-cell">
                                <div class="flex items-center justify-end space-x-1">
                                    <i class="fas fa-receipt text-orange-500"></i>
                                    <span>Taxe</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-orange-800 uppercase tracking-wider">
                                <div class="flex items-center justify-end space-x-1">
                                    <i class="fas fa-coins text-orange-500"></i>
                                    <span>TTC</span>
                                </div>
                            </th>
                            @canany(['proformas.view-details', 'proformas.update', 'proformas.create-version',
                                'proformas.update', 'proformas.view-history', 'proformas.delete'])
                                <th class="px-6 py-4 text-center text-xs font-bold text-orange-800 uppercase tracking-wider">
                                    <div class="flex items-center justify-center space-x-1">
                                        <i class="fas fa-cogs text-orange-500"></i>
                                        <span>Actions</span>
                                    </div>
                                </th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-gray-100 bg-white">
                        @forelse($proformas as $index => $proforma)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }} hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 transition-all duration-300 group cursor-pointer"
                                data-proforma-id="{{ $proforma->id_proforma }}">

                                {{-- Colonne Numéro/Version --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="w-11 h-11 flex-shrink-0 bg-gradient-to-br from-orange-400 via-orange-500 to-amber-500 rounded-xl flex items-center justify-center text-white font-bold shadow-lg shadow-orange-200/50 group-hover:shadow-orange-300 group-hover:scale-110 transition-all duration-300">
                                            <i class="fas fa-file-invoice text-sm"></i>
                                        </div>
                                        <div class="ml-4">
                                            <a @can('proformas.view-details') href="{{ route('proformas.show', $proforma->id_proforma) }}" @endcan
                                                class="text-sm font-bold text-gray-800 group-hover:text-orange-600 transition-colors hover:underline decoration-orange-300 decoration-2 underline-offset-2">
                                                {{ $proforma->numero_proforma }}
                                            </a>
                                            <div class="flex items-center flex-wrap gap-1.5 mt-1.5">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-gradient-to-r from-orange-100 to-amber-100 text-orange-700 border border-orange-200/50 shadow-sm">
                                                    <i class="fas fa-code-branch mr-1 text-orange-500 text-[10px]"></i>
                                                    v{{ $proforma->version_proforma }}
                                                </span>
                                                @if ($proforma->parent_id)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-indigo-100 text-indigo-700 border border-indigo-200/50">
                                                        <i class="fas fa-history mr-1 text-[10px]"></i> Révisée
                                                    </span>
                                                @endif
                                                @if ($proforma->estUtilisee())
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-700 border border-green-200/50">
                                                        <i class="fas fa-link mr-1 text-[10px]"></i> Utilisée
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Colonne Date --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-9 h-9 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                                            <i class="fas fa-calendar-day text-blue-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <span class="text-sm font-semibold text-gray-700 block">
                                                {{ $proforma->date_proforma ? $proforma->date_proforma->format('d/m/Y') : '-' }}
                                            </span>
                                            @if ($proforma->date_proforma)
                                                <span class="text-xs text-gray-400">
                                                    {{ $proforma->date_proforma->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Colonne Prestataire/Lot --}}
                                @php
                                    $attribution = $proforma->prestatairePrincipal ?? $proforma->prestataireRetire;
                                @endphp
                                <td class="px-6 py-4">
                                    <div class="space-y-2">
                                        {{-- Prestataire --}}
                                        <a @can('prestataires.view-details') href="{{ route('prestataires.show', $attribution->prestataire->id_prestataire) }}" @endcan
                                            class="group/link flex items-center p-1.5 -ml-1.5 rounded-lg hover:bg-orange-50/80 transition-all duration-200"
                                            title="{{ $attribution->prestataire->raison_sociale_prestataire }}">
                                            <div
                                                class="w-8 h-8 bg-gradient-to-br from-orange-400 to-amber-500 rounded-lg flex items-center justify-center mr-2 shadow-sm group-hover/link:shadow-md group-hover/link:scale-105 transition-all duration-200">
                                                <i class="fas fa-building text-white text-xs"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <span
                                                    class="text-sm font-semibold text-gray-800 group-hover/link:text-orange-600 transition-colors truncate block">
                                                    {{ Str::limit($attribution->prestataire->raison_sociale_prestataire, 22) }}
                                                </span>
                                            </div>
                                            <i
                                                class="fas fa-chevron-right text-orange-400 text-[10px] opacity-0 group-hover/link:opacity-100 transition-all ml-1"></i>
                                        </a>

                                        {{-- Lot --}}
                                        <a @can('lots.view-details') href="{{ route('lots-appels-offres.show', [$attribution->lot->appel_offre_id, $attribution->lot->id_lot]) }}" @endcan
                                            class="group/link flex items-center p-1.5 -ml-1.5 rounded-lg hover:bg-blue-50/80 transition-all duration-200"
                                            title="{{ $attribution->lot->libelle }}">
                                            <div
                                                class="w-7 h-7 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-md flex items-center justify-center mr-2 shadow-sm group-hover/link:shadow-md group-hover/link:scale-105 transition-all duration-200">
                                                <i class="fas fa-cube text-white text-[10px]"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <span
                                                    class="text-xs font-medium text-gray-600 group-hover/link:text-blue-600 transition-colors truncate block">
                                                    {{ Str::limit($attribution->lot->libelle, 28) }}
                                                </span>
                                            </div>
                                            <i
                                                class="fas fa-chevron-right text-blue-400 text-[10px] opacity-0 group-hover/link:opacity-100 transition-all ml-1"></i>
                                        </a>
                                    </div>
                                </td>

                                {{-- Colonne Montant HT --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex flex-col items-end">
                                        <span class="text-sm font-bold text-gray-800 tabular-nums">
                                            {{ number_format(floor($proforma->montant_retenu_proforma), 0, ',', ' ') }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-medium tracking-wide">FCFA</span>
                                    </div>
                                </td>

                                {{-- Colonne Remise (cachée sur mobile) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right  lg:table-cell">
                                    @if ($proforma->remise_montant_proforma > 0)
                                        <div
                                            class="inline-flex items-center space-x-2 bg-gradient-to-r from-red-50 to-rose-50 px-3 py-1.5 rounded-lg border border-red-100">
                                            <div class="flex flex-col items-end">
                                                <span class="text-sm font-bold text-red-600 tabular-nums">
                                                    -{{ number_format(floor($proforma->remise_montant_proforma), 0, ',', ' ') }}
                                                </span>
                                            </div>
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded bg-red-100 text-[10px] font-bold text-red-700">
                                                {{ $proforma->pourcentage_remise }}%
                                            </span>
                                        </div>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400">
                                            <i class="fas fa-minus text-[8px] mr-1"></i> —
                                        </span>
                                    @endif
                                </td>

                                {{-- Colonne Taxe (cachée sur mobile) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right  lg:table-cell">
                                    @if ($proforma->taxe_montant > 0)
                                        <div
                                            class="inline-flex items-center space-x-2 bg-gradient-to-r from-amber-50 to-yellow-50 px-3 py-1.5 rounded-lg border border-amber-100">
                                            <div class="flex flex-col items-end">
                                                <span class="text-sm font-bold text-amber-700 tabular-nums">
                                                    {{ number_format(floor($proforma->taxe_montant), 0, ',', ' ') }}
                                                </span>
                                            </div>
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded bg-amber-100 text-[10px] font-bold text-amber-700">
                                                {{ $proforma->taux_taxe }}%
                                            </span>
                                        </div>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">
                                            <i class="fas fa-check text-[8px] mr-1"></i> Exonéré
                                        </span>
                                    @endif
                                </td>

                                {{-- Colonne Montant TTC --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div
                                        class="inline-flex flex-col items-end bg-gradient-to-r from-green-50 to-emerald-50 px-3 py-2 rounded-xl border border-green-200 shadow-sm group-hover:shadow-md group-hover:border-green-300 transition-all duration-300">
                                        <span class="text-base font-bold text-green-700 tabular-nums">
                                            {{ number_format(floor($proforma->montant_ttc), 0, ',', ' ') }}
                                        </span>
                                        <span class="text-[10px] text-green-600 font-semibold tracking-wide">FCFA
                                            TTC</span>
                                    </div>
                                </td>

                                @canany(['proformas.view-details', 'proformas.update', 'proformas.create-version',
                                    'proformas.view-history', 'proformas.delete'])
                                    {{-- Colonne Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center justify-center space-x-1">
                                            @can('proformas.view-details')
                                                {{-- Voir --}}
                                                <button
                                                    onclick="window.location.href='{{ route('proformas.show', $proforma->id_proforma) }}'"
                                                    class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-500 hover:text-white rounded-lg transition-all duration-200 hover:shadow-lg hover:scale-110 active:scale-95"
                                                    title="Voir détails">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </button>
                                            @endcan

                                            @can('proformas.update')
                                                {{-- Modifier --}}
                                                <button
                                                    onclick="window.location.href='{{ route('proformas.edit', $proforma->id_proforma) }}'"
                                                    class="p-2 text-orange-600 bg-orange-50 hover:bg-orange-500 hover:text-white rounded-lg transition-all duration-200 hover:shadow-lg hover:scale-110 active:scale-95"
                                                    title="Modifier">
                                                    <i class="fas fa-pen text-sm"></i>
                                                </button>
                                            @endcan

                                            @canany(['proformas.create-version', 'proformas.view-history', 'proformas.delete'])
                                                {{-- Menu Plus --}}
                                                <div class="relative">
                                                    <button onclick="toggleMenu(event, '{{ $proforma->id_proforma }}')"
                                                        class="p-2 text-gray-500 bg-gray-50 hover:bg-gray-600 hover:text-white rounded-lg transition-all duration-200 hover:shadow-lg hover:scale-110 active:scale-95"
                                                        title="Plus d'actions">
                                                        <i class="fas fa-ellipsis-v text-sm"></i>
                                                    </button>
                                                    <div id="menu-{{ $proforma->id_proforma }}"
                                                        class="hidden fixed w-48 bg-white rounded-xl shadow-2xl border border-gray-100 z-[9999] overflow-hidden">
                                                        <div class="py-1">
                                                            @can('proformas.create-version')
                                                                <button onclick="creerVersion('{{ $proforma->id_proforma }}')"
                                                                    class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 flex items-center group/item transition-colors">
                                                                    <span
                                                                        class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3 group-hover/item:bg-indigo-500 transition-colors">
                                                                        <i
                                                                            class="fas fa-code-branch text-indigo-600 group-hover/item:text-white text-xs transition-colors"></i>
                                                                    </span>
                                                                    <span class="font-medium">Nouvelle version</span>
                                                                </button>
                                                            @endcan

                                                            @can('proformas.view-history')
                                                                <button onclick="voirHistorique('{{ $proforma->id_proforma }}')"
                                                                    class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 flex items-center group/item transition-colors">
                                                                    <span
                                                                        class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover/item:bg-blue-500 transition-colors">
                                                                        <i
                                                                            class="fas fa-history text-blue-600 group-hover/item:text-white text-xs transition-colors"></i>
                                                                    </span>
                                                                    <span class="font-medium">Historique</span>
                                                                </button>
                                                            @endcan

                                                            @can('proformas.delete')
                                                                <hr class="my-1 border-gray-100">
                                                                <button
                                                                    onclick="confirmDelete('{{ $proforma->id_proforma }}', '{{ $proforma->numero_proforma }}', {{ $proforma->estUtilisee() ? 'true' : 'false' }})"
                                                                    class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 flex items-center group/item transition-colors">
                                                                    <span
                                                                        class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3 group-hover/item:bg-red-500 transition-colors">
                                                                        <i
                                                                            class="fas fa-trash text-red-600 group-hover/item:text-white text-xs transition-colors"></i>
                                                                    </span>
                                                                    <span class="font-medium">Supprimer</span>
                                                                </button>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </div>
                                            @endcanany
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-20 h-20 bg-gradient-to-br from-orange-100 to-amber-100 rounded-2xl flex items-center justify-center mb-4 shadow-inner">
                                            <i class="fas fa-file-invoice-dollar text-orange-400 text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-700 mb-1">Aucune proforma trouvée</h3>

                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer avec pagination --}}
            @if ($proformas->hasPages())
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-slate-50 border-t border-gray-100">
                    {{ $proformas->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- Modal Confirmation Suppression -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Confirmer la suppression</h3>
                    <p id="deleteMessage" class="text-sm text-gray-600 mb-6"></p>

                    <div class="flex items-center justify-center space-x-3">
                        <button onclick="closeDeleteModal()"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                            Annuler
                        </button>
                        @can('prestataires.delete')
                            <button onclick="executeDelete()" id="deleteBtn"
                                class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium">
                                Supprimer
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nouvelle Version -->
    <div id="versionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-code-branch text-indigo-500 mr-2"></i>
                        Créer une nouvelle version
                    </h3>
                    <button onclick="closeVersionModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="versionForm" class="p-6">
                    <input type="hidden" id="version_proforma_id">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Motif de modification <span class="text-red-500">*</span>
                            </label>
                            <textarea id="motif_modification" rows="3" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent resize-none"
                                placeholder="Expliquez la raison de cette nouvelle version..."></textarea>
                        </div>

                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-yellow-500 mr-2 mt-0.5"></i>
                                <p class="text-sm text-yellow-700">
                                    La version actuelle sera désactivée et une nouvelle version sera créée avec les
                                    modifications.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeVersionModal()"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        @can('proformas.create')
                            <button type="submit"
                                class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                                Créer la version
                            </button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>

    @can('proformas.read')
        @push('scripts')
            <script>
                let deleteProformaId = null;

                // Toggle menu
                // public/js/dropdown-menu.js
                function toggleMenu(event, id) {
                    event.stopPropagation();

                    const button = event.currentTarget;
                    const menu = document.getElementById('menu-' + id);

                    document.querySelectorAll('[id^="menu-"]').forEach(m => {
                        if (m.id !== 'menu-' + id) m.classList.add('hidden');
                    });

                    menu.classList.toggle('hidden');

                    if (!menu.classList.contains('hidden')) {
                        const rect = button.getBoundingClientRect();
                        let top = rect.bottom + 8;
                        let left = rect.right - menu.offsetWidth;

                        if (top + menu.offsetHeight > window.innerHeight) {
                            top = rect.top - menu.offsetHeight - 8;
                        }
                        if (left < 0) left = rect.left;

                        menu.style.top = top + 'px';
                        menu.style.left = left + 'px';
                    }
                }

                document.addEventListener('click', function(event) {
                    if (!event.target.closest('[id^="menu-"]') && !event.target.closest('button[onclick*="toggleMenu"]')) {
                        document.querySelectorAll('[id^="menu-"]').forEach(menu => menu.classList.add('hidden'));
                    }
                });

                document.addEventListener('scroll', function() {
                    document.querySelectorAll('[id^="menu-"]').forEach(menu => menu.classList.add('hidden'));
                }, true);



                // Confirmer suppression
                window.confirmDelete = function(id, numero, estUtilisee) {
                    deleteProformaId = id;

                    if (estUtilisee) {
                        document.getElementById('deleteMessage').innerHTML =
                            `<strong class="text-red-600">Impossible de supprimer la proforma "${numero}" car elle est utilisée dans des attributions.</strong>`;
                        document.getElementById('deleteBtn').classList.add('hidden');
                    } else {
                        document.getElementById('deleteMessage').textContent =
                            `Êtes-vous sûr de vouloir supprimer la proforma "${numero}" ?`;
                        document.getElementById('deleteBtn').classList.remove('hidden');
                    }

                    document.getElementById('deleteModal').classList.remove('hidden');
                }

                // Exécuter suppression
                window.executeDelete = function() {
                    if (!deleteProformaId) return;

                    fetch("{{ route('proformas.destroy', ':deleteProformaId') }}".replace(':deleteProformaId',
                            deleteProformaId), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message || 'Une erreur est survenue');
                                closeDeleteModal();
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue');
                            closeDeleteModal();
                        });
                }

                // Fermer modal suppression
                window.closeDeleteModal = function() {
                    document.getElementById('deleteModal').classList.add('hidden');
                    deleteProformaId = null;
                }



                // Créer version
                window.creerVersion = function(id) {
                    document.getElementById('version_proforma_id').value = id;
                    document.getElementById('motif_modification').value = '';
                    document.getElementById('versionModal').classList.remove('hidden');
                }

                // Fermer modal version
                window.closeVersionModal = function() {
                    document.getElementById('versionModal').classList.add('hidden');
                }

                // Soumettre nouvelle version
                document.getElementById('versionForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const id = document.getElementById('version_proforma_id').value;
                    const motif = document.getElementById('motif_modification').value;

                    if (!motif.trim()) {
                        alert('Le motif de modification est obligatoire');
                        return;
                    }

                    fetch("{{ route('proformas.creer-version', ':id') }}".replace(':id', id), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                motif_modification_proforma: motif
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = `/proformas/${data.data.id_proforma}`;
                            } else {
                                alert(data.message || 'Une erreur est survenue');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue');
                        });
                });

                // Voir historique
                window.voirHistorique = function(id) {
                    window.location.href = "{{ route('proformas.historique', ':id') }}".replace(':id', id);
                }

                // Rafraîchir le tableau
                window.refreshTable = function() {
                    location.reload();
                }

                // Recherche en temps réel
                let searchTimeout;
                document.getElementById('searchInput').addEventListener('input', function(e) {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        applyFilters();
                    }, 500);
                });


                function applyFilters() {
                    const search = document.getElementById('searchInput').value;

                    const params = new URLSearchParams();
                    if (search) params.append('search', search);

                    window.location.href = `?${params.toString()}`;
                }

                // Fermer modals avec Escape
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeDeleteModal();
                        closeVersionModal();
                        document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
                    }
                });
            </script>

            <style>
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(-10px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .animate-fadeIn {
                    animation: fadeIn 0.3s ease-out;
                }
            </style>
        @endpush
    @endcan
@endsection
