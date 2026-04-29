@extends('layouts.main')

@section('title', 'Factures')

@section('breadcrumb')
    <span class="text-white font-medium">Factures</span>
@endsection

@section('content')
    <!-- Header simplifié (fixe) -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Factures</h1>
                    <p class="text-gray-600 mt-1 text-sm">Gestion des factures liées aux proformas</p>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Bouton toggle statistiques sur mobile -->
                    <button type="button" onclick="toggleStats()"
                        class="lg:hidden inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all duration-200">
                        <i class="fas fa-chart-bar mr-2"></i>
                        <span class="text-sm">Stats</span>
                        <i id="statsChevron" class="fas fa-chevron-down ml-2 text-xs transition-transform duration-200"></i>
                    </button>


                    @can('factures.create')
                        <a href="{{ route('factures.create') }}"
                            class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 shadow-md">
                            <i class="fas fa-plus mr-2"></i>
                            <span class="hidden sm:inline">Nouvelle Facture</span>
                            <span class="sm:hidden">Nouveau</span>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content (scrollable) -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @include('partials.alerts')

        <!-- Statistiques (dans le main, donc scrollable) -->
        <div id="statsSection" class="hidden lg:block mb-6">
            <!-- Statistiques par statut -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-4">
                <div class="bg-white rounded-xl p-3 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 uppercase">Total</span>
                        <i class="fas fa-file-invoice-dollar text-gray-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $statistiques['total'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-yellow-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-yellow-600 uppercase">En attente</span>
                        <i class="fas fa-clock text-yellow-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $statistiques['en_attente'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-blue-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-blue-600 uppercase">Validées</span>
                        <i class="fas fa-check-circle text-blue-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $statistiques['validees'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-green-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-green-600 uppercase">Payées</span>
                        <i class="fas fa-check-double text-green-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $statistiques['payees'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-orange-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-orange-600 uppercase">Partielles</span>
                        <i class="fas fa-adjust text-orange-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $statistiques['partiellement_payees'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-red-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-red-600 uppercase">Rejetées</span>
                        <i class="fas fa-times-circle text-red-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $statistiques['rejetees'] }}</p>
                </div>
            </div>

            <!-- Résumé Financier -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-orange-100 text-xs font-medium uppercase truncate">Montant Total Facturé</p>
                            <p class="text-lg sm:text-xl font-bold mt-1 truncate">
                                {{ number_format(floor($statistiques['montant_total']), 0, ',', ' ') }} <span
                                    class="text-sm">FCFA</span></p>
                        </div>
                        <i class="fas fa-coins text-2xl sm:text-3xl text-orange-300 opacity-50 ml-2 flex-shrink-0"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-green-100 text-xs font-medium uppercase truncate">Montant Payé</p>
                            <p class="text-lg sm:text-xl font-bold mt-1 truncate">
                                {{ number_format(floor($statistiques['montant_paye']), 0, ',', ' ') }} <span
                                    class="text-sm">FCFA</span></p>
                        </div>
                        <i
                            class="fas fa-hand-holding-usd text-2xl sm:text-3xl text-green-300 opacity-50 ml-2 flex-shrink-0"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-red-100 text-xs font-medium uppercase truncate">Reste à Payer</p>
                            <p class="text-lg sm:text-xl font-bold mt-1 truncate">
                                {{ number_format(floor($statistiques['montant_restant']), 0, ',', ' ') }} <span
                                    class="text-sm">FCFA</span></p>
                        </div>
                        <i
                            class="fas fa-exclamation-triangle text-2xl sm:text-3xl text-red-300 opacity-50 ml-2 flex-shrink-0"></i>
                    </div>
                    <div class="mt-2">
                        <div class="w-full bg-red-400 rounded-full h-1.5">
                            <div class="bg-white rounded-full h-1.5" style="width: {{ $statistiques['taux_paiement'] }}%">
                            </div>
                        </div>
                        <p class="text-xs text-red-100 mt-1">{{ $statistiques['taux_paiement'] }}% payé</p>
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
                <form action="{{ route('factures.index') }}" method="GET"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                            placeholder="N° facture, proforma...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400">
                            <option value="">Tous les statuts</option>
                            @foreach ($statuts as $key => $label)
                                <option value="{{ $key }}" {{ request('statut') === $key ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                        <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                        <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit"
                            class="flex-1 sm:flex-none px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all">
                            <i class="fas fa-search mr-2"></i>Filtrer
                        </button>
                        @can('factures.view-details')
                            <a href="{{ route('factures.index') }}"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                                <i class="fas fa-undo"></i>
                            </a>
                        @endcan
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des factures -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                <h2 class="text-base sm:text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-file-invoice text-indigo-500 mr-2"></i>
                    Liste des factures
                    <span class="ml-2 px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-full">
                        {{ $factures->total() }}
                    </span>
                </h2>
            </div>

            @if ($factures->isEmpty())
                <div class="p-8 sm:p-12 text-center">
                    <i class="fas fa-file-invoice text-5xl sm:text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-600 mb-2">Aucune facture trouvée</h3>
                    @can('factures.create')
                        <p class="text-gray-500 mb-4">Commencez par créer une nouvelle facture.</p>
                        <a href="{{ route('factures.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700">
                            <i class="fas fa-plus mr-2"></i>Nouvelle Facture
                        </a>
                    @endcan
                </div>
            @else
                <!-- Vue mobile: cartes avec détails dépliables -->
                <div class="block lg:hidden divide-y divide-gray-200">
                    @foreach ($factures as $facture)
                        @php
                            $statutClasses = [
                                'en_attente' => 'bg-yellow-100 text-yellow-800',
                                'validee' => 'bg-blue-100 text-blue-800',
                                'rejetee' => 'bg-red-100 text-red-800',
                                'payee' => 'bg-green-100 text-green-800',
                                'partiellement_payee' => 'bg-orange-100 text-orange-800',
                                'annulee' => 'bg-gray-100 text-gray-800',
                            ];
                            $statutIcons = [
                                'en_attente' => 'clock',
                                'validee' => 'check-circle',
                                'rejetee' => 'times-circle',
                                'payee' => 'check-double',
                                'partiellement_payee' => 'adjust',
                                'annulee' => 'ban',
                            ];
                            $pourcentage =
                                $facture->montant_facture > 0
                                    ? round(($facture->montant_paye / $facture->montant_facture) * 100)
                                    : 0;

                            // Récupérer l'attribution via la proforma
                            $attribution = $facture->proforma
                                ?->prestataireLotsAttributions()
                                ->where('is_active', true)
                                ->first();
                            $lot = $attribution?->lot;
                            $prestataire = $attribution?->prestataire;
                            $nbPaiements = $facture->paiements->count();
                        @endphp
                        <div class="p-4 hover:bg-gray-50">
                            <!-- En-tête de la carte (cliquable pour déplier) -->
                            <div class="flex items-start justify-between mb-3 cursor-pointer"
                                onclick="toggleMobileCard('mobile-{{ $facture->id_facture }}')">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-file-invoice text-orange-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $facture->numero_facture }}</p>
                                        <p class="text-xs text-gray-500">{{ $facture->date_facture->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $statutClasses[$facture->statut_facture] ?? 'bg-gray-100 text-gray-800' }}">
                                        <i
                                            class="fas fa-{{ $statutIcons[$facture->statut_facture] ?? 'question' }} mr-1"></i>
                                        {{ $facture->statut_libelle }}
                                    </span>
                                    <button type="button" class="p-1 text-gray-400"
                                        id="mobile-chevron-{{ $facture->id_facture }}">
                                        <i class="fas fa-chevron-down transform transition-transform duration-200"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-3 text-sm">
                                <div>
                                    <p class="text-gray-500">Proforma</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $facture->proforma->numero_proforma ?? 'N/A' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-500">Montant</p>
                                    <p class="font-bold text-gray-900">{{ $facture->montant_formate }}</p>
                                </div>
                            </div>

                            <!-- Barre de progression paiement -->
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Paiement</span>
                                    <span>{{ $pourcentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full transition-all duration-300"
                                        style="width: {{ $pourcentage }}%"></div>
                                </div>
                            </div>

                            <!-- Section dépliable sur mobile -->
                            <div id="mobile-{{ $facture->id_facture }}"
                                class="hidden mt-3 pt-3 border-t border-gray-200">
                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <!-- Paiements -->
                                    <div class="bg-emerald-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <i class="fas fa-money-check-alt text-emerald-600 text-sm"></i>
                                            <span class="text-xs text-emerald-700 uppercase">Total de Paiements
                                                effectués</span>
                                        </div>
                                        <p class="text-lg font-bold text-emerald-800">{{ $nbPaiements }}</p>
                                    </div>

                                    <!-- Lot -->
                                    <div class="bg-blue-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <i class="fas fa-box text-blue-600 text-sm"></i>
                                            <span class="text-xs text-blue-700 uppercase">Lot</span>
                                        </div>
                                        @if ($lot)
                                            <a href="{{ route('lots.show', $lot->id_lot) }}"
                                                class="text-sm font-bold text-blue-800 hover:underline">
                                                {{ $lot->numero }}
                                            </a>
                                        @else
                                            <p class="text-sm text-gray-400 italic">N/A</p>
                                        @endif
                                    </div>

                                    <!-- Prestataire -->
                                    <div class="bg-purple-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <i class="fas fa-building text-purple-600 text-sm"></i>
                                            <span class="text-xs text-purple-700 uppercase">Prestataire</span>
                                        </div>
                                        @if ($prestataire)
                                            <a href="{{ route('prestataires.show', $prestataire->id_prestataire) }}"
                                                class="text-sm font-bold text-purple-800 hover:underline truncate block"
                                                title="{{ $prestataire->raison_sociale_prestataire }}">
                                                {{ Str::limit($prestataire->raison_sociale_prestataire, 15) }}
                                            </a>
                                        @else
                                            <p class="text-sm text-gray-400 italic">N/A</p>
                                        @endif
                                    </div>

                                    <!-- Attribution -->
                                    <div class="bg-orange-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <i class="fas fa-file-signature text-orange-600 text-sm"></i>
                                            <span class="text-xs text-orange-700 uppercase">Attribution</span>
                                        </div>
                                        @if ($attribution)
                                            <a @can('attributions_lots.view-details') href="{{ route('attributions.show', $attribution->id_attribution) }}" @endcan
                                                class="text-sm font-bold text-orange-800 hover:underline">
                                                {{ $attribution->numero_attribution }}
                                            </a>
                                        @else
                                            <p class="text-sm text-gray-400 italic">N/A</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @canany(['factures.view-details', 'factures.update', 'factures.validate', 'factures.delete'])
                                <!-- Actions -->
                                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-gray-100">
                                    @can('factures.view-details')
                                        <a href="{{ route('factures.show', $facture->id_facture) }}"
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan

                                    @can('factures.update')
                                        @if ($facture->peutEtreModifiee())
                                            <a href="{{ route('factures.edit', $facture->id_facture) }}"
                                                class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    @endcan

                                    @can('factures.validate')
                                        @if ($facture->peutEtreValidee())
                                            <form action="{{ route('factures.valider', $facture->id_facture) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                    @can('factures.delete')
                                        <button
                                            onclick="openDeleteModal('{{ $facture->id_facture }}', '{{ $facture->numero_facture }}')"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            @endcanany
                        </div>
                    @endforeach
                </div>

                <!-- Vue desktop: tableau avec lignes dépliables -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-10">
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    N° Facture</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Proforma</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Montant</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Date</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Statut</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Paiement</th>
                                @canany(['factures.view-details', 'factures.update', 'factures.validate', 'paiements.create', 'factures.delete'])
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($factures as $facture)
                                @php
                                    $statutClasses = [
                                        'en_attente' => 'bg-yellow-100 text-yellow-800',
                                        'validee' => 'bg-blue-100 text-blue-800',
                                        'rejetee' => 'bg-red-100 text-red-800',
                                        'payee' => 'bg-green-100 text-green-800',
                                        'partiellement_payee' => 'bg-orange-100 text-orange-800',
                                        'annulee' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $statutIcons = [
                                        'en_attente' => 'clock',
                                        'validee' => 'check-circle',
                                        'rejetee' => 'times-circle',
                                        'payee' => 'check-double',
                                        'partiellement_payee' => 'adjust',
                                        'annulee' => 'ban',
                                    ];
                                    $pourcentage = $facture->montant_facture > 0 ? round(($facture->montant_paye / $facture->montant_facture) * 100) : 0;

                                    // Récupérer l'attribution via la proforma
                                    $attribution = $facture->proforma?->prestataireLotsAttributions()->where('is_active', true)->first();
                                    $lot = $attribution?->lot;
                                    $prestataire = $attribution?->prestataire;
                                    $nbPaiements = $facture->paiements->count();
                                @endphp

                                <!-- Ligne principale -->
                                <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                                    onclick="toggleRow('{{ $facture->id_facture }}')">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <button type="button"
                                            class="p-1 text-gray-400 hover:text-gray-600 transition-colors"
                                            id="chevron-{{ $facture->id_facture }}">
                                            <i
                                                class="fas fa-chevron-right transform transition-transform duration-200"></i>
                                        </button>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-file-invoice text-orange-600"></i>
                                            </div>
                                            <div>
                                                <span
                                                    class="font-semibold text-gray-900">{{ $facture->numero_facture }}</span>
                                                <p class="text-xs text-gray-500">
                                                    {{ $facture->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if ($facture->proforma)
                                            <span
                                                class="text-sm font-medium text-gray-900">{{ $facture->proforma->numero_proforma }}</span>
                                        @else
                                            <span class="text-sm text-gray-400 italic">Non définie</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right">
                                        <span
                                            class="text-sm font-bold text-gray-900">{{ $facture->montant_formate }}</span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $facture->date_facture->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">Reçue:
                                            {{ $facture->date_reception_facture->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statutClasses[$facture->statut_facture] ?? 'bg-gray-100 text-gray-800' }}">
                                            <i
                                                class="fas fa-{{ $statutIcons[$facture->statut_facture] ?? 'question' }} mr-1"></i>
                                            {{ $facture->statut_libelle }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mb-1">
                                                <div class="bg-green-500 h-2 rounded-full"
                                                    style="width: {{ $pourcentage }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-600">{{ $pourcentage }}%</span>
                                        </div>
                                    </td>
                                    @canany(['factures.view-details', 'factures.update', 'factures.validate', 'paiements.create', 'factures.delete'])
                                        <td class="px-4 py-4 whitespace-nowrap text-center" onclick="event.stopPropagation()">
                                            <div class="flex items-center justify-center space-x-1">
                                                @can('factures.view-details')
                                                <a href="{{ route('factures.show', $facture->id_facture) }}"
                                                    class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                                    title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan

                                                @can('factures.update')
                                                @if ($facture->peutEtreModifiee())
                                                    <a href="{{ route('factures.edit', $facture->id_facture) }}"
                                                        class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                                                        title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @endcan



                                                @can('factures.validate')
                                                @if ($facture->peutEtreValidee())
                                                    <form action="{{ route('factures.valider', $facture->id_facture) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                                            title="Valider">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @endcan

                                                @can('paiements.create')
                                                {{-- @if ($facture->peutRecevoirPaiement()) --}}
                                                @if($facture->peutRecevoirPaiement() && $attribution &&  (!$attribution->date_retrait || !$attribution->date_suspension) )
                                                    <a href="{{ route('paiements.create', $facture->id_facture) }}"
                                                        class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                                        title="Ajouter un paiement">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </a>
                                                @endif
                                                @endcan

                                                {{-- Fiche Excel --}}
                                                <a href="{{ route('exports.factures.fiche.excel', $facture->id_facture) }}"
                                                title="Télécharger la fiche Excel"
                                                class="p-2 text-emerald-600 hover:bg-emerald-50 rounded transition-colors">
                                                    <i class="fa fa-file-excel"></i>
                                                </a>

                                                {{-- Fiche PDF --}}
                                                <a href="{{ route('exports.factures.fiche.pdf', $facture->id_facture) }}"
                                                title="Télécharger la fiche PDF"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded transition-colors">
                                                    <i class="fa fa-file-pdf"></i>
                                                </a>

                                                @can('factures.delete')
                                                <button
                                                    onclick="openDeleteModal('{{ $facture->id_facture }}', '{{ $facture->numero_facture }}')"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>

                                <!-- Ligne dépliable (détails) -->
                                <tr id="details-{{ $facture->id_facture }}"
                                    class="hidden bg-gradient-to-r from-gray-50 to-indigo-50">
                                    <td colspan="8" class="px-4 py-0">
                                        <div class="py-4 pl-14 pr-4">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                <!-- Nombre de paiements -->
                                                <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
                                                    <div class="flex items-center space-x-3">
                                                        <div
                                                            class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-money-check-alt text-emerald-600"></i>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-500 uppercase tracking-wide">Total
                                                                de Paiements effectués</p>
                                                            <p class="text-lg font-bold text-gray-900">{{ $nbPaiements }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    @can('factures.view-details')
                                                    @if ($nbPaiements > 0)
                                                        <a href="{{ route('factures.show', $facture->id_facture) }}#paiements"
                                                            class="mt-2 inline-flex items-center text-xs text-emerald-600 hover:text-emerald-800 transition-colors">
                                                            <i class="fas fa-external-link-alt mr-1"></i> Voir les
                                                            paiements
                                                        </a>
                                                    @endif
                                                    @endcan
                                                </div>

                                                <!-- Lot -->
                                                <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
                                                    <div class="flex items-center space-x-3">
                                                        <div
                                                            class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-box text-blue-600"></i>
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-xs text-gray-500 uppercase tracking-wide">Lot
                                                            </p>
                                                            @if ($lot)
                                                                <p class="text-sm font-bold text-gray-900 truncate"
                                                                    title="{{ $lot->libelle }}">{{ $lot->numero }}</p>
                                                            @else
                                                                <p class="text-sm text-gray-400 italic">Non attribué</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @can('lots.view-details')
                                                        @if ($lot)
                                                            <a href="{{ route('lots.show', $lot->id_lot) }}"
                                                                class="mt-2 inline-flex items-center text-xs text-blue-600 hover:text-blue-800 transition-colors">
                                                                <i class="fas fa-external-link-alt mr-1"></i> Voir le lot
                                                            </a>
                                                        @endif
                                                    @endcan
                                                </div>

                                                <!-- Prestataire -->
                                                <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
                                                    <div class="flex items-center space-x-3">
                                                        <div
                                                            class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-building text-purple-600"></i>
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-xs text-gray-500 uppercase tracking-wide">
                                                                Prestataire</p>
                                                            @if ($prestataire)
                                                                <p class="text-sm font-bold text-gray-900 truncate"
                                                                    title="{{ $prestataire->raison_sociale_prestataire }}">
                                                                    {{ Str::limit($prestataire->raison_sociale_prestataire, 20) }}
                                                                </p>
                                                            @else
                                                                <p class="text-sm text-gray-400 italic">Non défini</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @can('prestataires.view-details')
                                                    @if ($prestataire)
                                                        <a href="{{ route('prestataires.show', $prestataire->id_prestataire) }}"
                                                            class="mt-2 inline-flex items-center text-xs text-purple-600 hover:text-purple-800 transition-colors">
                                                            <i class="fas fa-external-link-alt mr-1"></i> Voir le
                                                            prestataire
                                                        </a>
                                                    @endif
                                                    @endcan
                                                </div>

                                                <!-- Attribution -->
                                                <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm">
                                                    <div class="flex items-center space-x-3">
                                                        <div
                                                            class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-file-signature text-orange-600"></i>
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-xs text-gray-500 uppercase tracking-wide">
                                                                Attribution</p>
                                                            @if ($attribution)
                                                                <p class="text-sm font-bold text-gray-900 truncate">
                                                                    {{ $attribution->numero_attribution }}</p>
                                                            @else
                                                                <p class="text-sm text-gray-400 italic">Non définie</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @can('attributions_lots.view-details')
                                                    @if ($attribution)
                                                        <a href="{{ route('attributions.show', $attribution->id_attribution) }}"
                                                            class="mt-2 inline-flex items-center text-xs text-orange-600 hover:text-orange-800 transition-colors">
                                                            <i class="fas fa-external-link-alt mr-1"></i> Voir
                                                            l'attribution
                                                        </a>
                                                    @endif
                                                    @endcan
                                                </div>
                                            </div>

                                            <!-- Informations complémentaires si disponibles -->
                                            @if ($lot && $lot->libelle)
                                                <div class="mt-3 p-3 bg-white rounded-lg border border-gray-200">
                                                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Libellé
                                                        du lot</p>
                                                    <p class="text-sm text-gray-700">{{ $lot->libelle }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                    {{ $factures->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- Modal de suppression -->
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
                            Êtes-vous sûr de vouloir supprimer la facture <strong id="deleteFactureNumero"></strong> ?
                        </p>
                        <p class="text-sm text-red-600 mt-2">Cette action est irréversible.</p>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeDeleteModal()"
                            class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        @can('factures.delete')
                        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">
                            Supprimer
                        </button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@can('factures.read')
@push('scripts')
    <script>
        // Toggle ligne dépliable (desktop)
        function toggleRow(id) {
            const detailsRow = document.getElementById('details-' + id);
            const chevron = document.getElementById('chevron-' + id);
            const icon = chevron.querySelector('i');

            if (detailsRow.classList.contains('hidden')) {
                // Ouvrir
                detailsRow.classList.remove('hidden');
                icon.classList.add('rotate-90');
            } else {
                // Fermer
                detailsRow.classList.add('hidden');
                icon.classList.remove('rotate-90');
            }
        }

        // Toggle carte dépliable (mobile)
        function toggleMobileCard(id) {
            const detailsDiv = document.getElementById(id);
            const chevron = document.getElementById('mobile-chevron-' + id.replace('mobile-', ''));
            const icon = chevron ? chevron.querySelector('i') : null;

            if (detailsDiv.classList.contains('hidden')) {
                // Ouvrir
                detailsDiv.classList.remove('hidden');
                if (icon) icon.classList.add('rotate-180');
            } else {
                // Fermer
                detailsDiv.classList.add('hidden');
                if (icon) icon.classList.remove('rotate-180');
            }
        }

        // Fermer toutes les lignes dépliées
        function closeAllRows() {
            document.querySelectorAll('[id^="details-"]').forEach(row => {
                row.classList.add('hidden');
            });
            document.querySelectorAll('[id^="chevron-"] i').forEach(icon => {
                icon.classList.remove('rotate-90');
            });
        }

        // Toggle statistiques sur mobile
        function toggleStats() {
            const statsSection = document.getElementById('statsSection');
            const chevron = document.getElementById('statsChevron');

            statsSection.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        // Toggle filtres sur mobile
        function toggleFilters() {

            const filtersSection = document.getElementById('filtersSection');
            const chevron = document.getElementById('filtersChevron');

            filtersSection.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        // Modal de suppression
        function openDeleteModal(id, numero) {
            document.getElementById('deleteForm').action = `/factures/${id}`;
            document.getElementById('deleteFactureNumero').textContent = numero;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });

        // Sur desktop (lg+), toujours afficher les stats
        function handleResize() {
            const statsSection = document.getElementById('statsSection');
            if (window.innerWidth >= 1024) {
                statsSection.classList.remove('hidden');
            }
        }

        window.addEventListener('resize', handleResize);
        // Initialisation
        handleResize();
    </script>
@endpush
@endcan
