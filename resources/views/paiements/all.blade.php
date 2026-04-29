@extends('layouts.main')

@section('title', 'Tous les Paiements')

@section('breadcrumb')
    <span class="text-white font-medium">Paiements</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-money-check-alt text-green-500 mr-2"></i>Tous les Paiements
                    </h1>
                    <p class="text-gray-600 mt-1 text-sm">Vue globale de tous les paiements enregistrés</p>
                </div>
                @can('paiements.read')
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="toggleStats()"
                        class="lg:hidden inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all duration-200">
                        <i class="fas fa-chart-bar mr-2"></i>
                        <span class="text-sm">Stats</span>
                        <i id="statsChevron" class="fas fa-chevron-down ml-2 text-xs transition-transform duration-200"></i>
                    </button>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @include('partials.alerts')

        <!-- Statistiques -->
        <div id="statsSection" class="hidden lg:block mb-6">
            <!-- Statistiques par statut -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3 mb-4">
                <div class="bg-white rounded-xl p-3 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 uppercase">Total</span>
                        <i class="fas fa-list text-gray-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-yellow-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-yellow-600 uppercase">En attente</span>
                        <i class="fas fa-clock text-yellow-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['en_attente'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-blue-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-blue-600 uppercase">Validés</span>
                        <i class="fas fa-check-circle text-blue-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['payes'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-indigo-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-indigo-600 uppercase">Traitement</span>
                        <i class="fas fa-spinner text-indigo-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['payes'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-green-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-green-600 uppercase">Payés</span>
                        <i class="fas fa-check-double text-green-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['payes'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-red-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-red-600 uppercase">Rejetés</span>
                        <i class="fas fa-times-circle text-red-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['rejetes'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-gray-300 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 uppercase">Annulés</span>
                        <i class="fas fa-ban text-gray-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-500 mt-1">{{ $stats['annules'] }}</p>
                </div>
            </div>

            <!-- Résumé Financier -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-orange-100 text-xs font-medium uppercase truncate">Montant Total</p>
                            <p class="text-lg sm:text-xl font-bold mt-1 truncate">{{ number_format(floor($stats['montant_total']), 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
                        </div>
                        <i class="fas fa-coins text-2xl sm:text-3xl text-orange-300 opacity-50 ml-2 flex-shrink-0"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-green-100 text-xs font-medium uppercase truncate">Montant Payé</p>
                            <p class="text-lg sm:text-xl font-bold mt-1 truncate">{{ number_format(floor($stats['montant_paye']), 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
                        </div>
                        <i class="fas fa-hand-holding-usd text-2xl sm:text-3xl text-green-300 opacity-50 ml-2 flex-shrink-0"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-blue-100 text-xs font-medium uppercase truncate">En cours de traitement</p>
                            <p class="text-lg sm:text-xl font-bold mt-1 truncate">{{ number_format(floor($stats['montant_en_attente']), 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
                        </div>
                        <i class="fas fa-hourglass-half text-2xl sm:text-3xl text-blue-300 opacity-50 ml-2 flex-shrink-0"></i>
                    </div>
                    @if($stats['montant_total'] > 0)
                    <div class="mt-2">
                        <div class="w-full bg-blue-400 rounded-full h-1.5">
                            <div class="bg-white rounded-full h-1.5" style="width: {{ round(($stats['montant_paye'] / $stats['montant_total']) * 100, 1) }}%"></div>
                        </div>
                        <p class="text-xs text-blue-100 mt-1">{{ round(($stats['montant_paye'] / $stats['montant_total']) * 100, 1) }}% payé</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-base sm:text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-filter text-green-500 mr-2"></i>
                    Filtres
                </h2>
                <button type="button" onclick="toggleFilters()" class="sm:hidden p-2 text-gray-500 hover:text-gray-700">
                    <i id="filtersChevron" class="fas fa-chevron-down transition-transform duration-200"></i>
                </button>
            </div>
            <div id="filtersSection" class="p-4 sm:p-6 hidden sm:block">
                <form action="{{ route('paiements.all') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Recherche -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-transparent"
                                placeholder="N° facture, proforma, prestataire, banque...">
                        </div>

                        <!-- Statut -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select name="statut" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
                                <option value="">Tous les statuts</option>
                                @foreach($statuts as $key => $label)
                                    <option value="{{ $key }}" {{ request('statut') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Banque -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Banque</label>
                            <select name="banque_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
                                <option value="">Toutes les banques</option>
                                @foreach($banques as $banque)
                                    <option value="{{ $banque->id_banque }}" {{ request('banque_id') == $banque->id_banque ? 'selected' : '' }}>
                                        {{ $banque->nom_banque }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Montant min -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Montant min (FCFA)</label>
                            <input type="number" name="montant_min" value="{{ request('montant_min') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400"
                                placeholder="0">
                        </div>

                        <!-- Montant max -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Montant max (FCFA)</label>
                            <input type="number" name="montant_max" value="{{ request('montant_max') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400"
                                placeholder="10000000">
                        </div>

                        <!-- Date début -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date création (début)</label>
                            <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
                        </div>

                        <!-- Date fin -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date création (fin)</label>
                            <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="flex flex-col sm:flex-row gap-2 sm:justify-end pt-2">
                        @can('paiements.read')
                        <a href="{{ route('paiements.all') }}"
                            class="w-full sm:w-auto px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-center transition-colors">
                            <i class="fas fa-times mr-2"></i>Réinitialiser
                        </a>
                        @endcan
                        <button type="submit"
                            class="w-full sm:w-auto px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-search mr-2"></i>Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des paiements -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <h2 class="text-base sm:text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-list text-green-500 mr-2"></i>
                        Liste des paiements
                        <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-600 rounded-full text-xs font-medium">
                            {{ $paiements->total() }}
                        </span>
                    </h2>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <span>Afficher</span>
                        <select onchange="window.location.href='{{ route('paiements.all') }}?per_page=' + this.value + '&{{ http_build_query(request()->except('per_page')) }}'"
                            class="px-2 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span>par page</span>
                    </div>
                </div>
            </div>

            @if($paiements->isEmpty())
                <div class="p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-inbox text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800 mb-1">Aucun paiement trouvé</h3>
                    <p class="text-gray-500">Aucun paiement ne correspond à vos critères de recherche.</p>
                </div>
            @else
                <!-- Vue Desktop -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>

                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Référence</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Facture</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Prestataire</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Banque</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Montant</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($paiements as $paiement)
                                @php
                                    $facture = $paiement->facture;
                                    $proforma = $facture?->proforma;
                                    $prestataire = $proforma->getPrestataire() ?? $proforma->getPrestataireRetire();
                                    $banque = $paiement->banque;
                                    $statutCouleur = $paiement->statut_couleur;
                                    $reference_paiement = $paiement->reference_paiement;
                                @endphp

                                <tr class="hover:bg-gray-50 transition-colors">
                                    <!-- Référence -->
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $reference_paiement }}</div>
                                    </td>
                                    <!-- Facture -->
                                    <td class="px-4 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-file-invoice text-green-600"></i>
                                            </div>
                                            <div>
                                                <a @can('factures.view-details') href="{{ route('factures.show', $facture->id_facture) }}" @endcan class="font-medium text-gray-900 hover:text-green-600">
                                                    {{ $facture->numero_facture }}
                                                </a>
                                                @if($proforma)
                                                    <p class="text-xs text-gray-500">{{ $proforma->numero_proforma }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Prestataire -->
                                    <td class="px-4 py-4">
                                        @if($prestataire)
                                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($prestataire->raison_sociale_prestataire, 30) }}</div>
                                            @if($prestataire->ville_prestataire)
                                                <div class="text-xs text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i>{{ $prestataire->ville_prestataire }}</div>
                                            @endif
                                        @else
                                            <span class="text-gray-400 italic">N/A</span>
                                        @endif
                                    </td>

                                    <!-- Banque -->
                                    <td class="px-4 py-4">
                                        @if($banque)
                                            <div class="text-sm font-medium text-gray-900">{{ $banque->nom_banque }}</div>
                                            <div class="text-xs text-gray-500">{{ $banque->numero_compte_banque }}</div>
                                        @else
                                            <span class="text-gray-400 italic">N/A</span>
                                        @endif
                                    </td>

                                    <!-- Montant -->
                                    <td class="px-4 py-4 text-right">
                                        <span class="font-bold text-gray-900">{{ $paiement->montant_formate }}</span>
                                    </td>

                                    <!-- Statut -->
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                            @if($statutCouleur === 'yellow') bg-yellow-100 text-yellow-700
                                            @elseif($statutCouleur === 'blue') bg-blue-100 text-blue-700
                                            @elseif($statutCouleur === 'indigo') bg-indigo-100 text-indigo-700
                                            @elseif($statutCouleur === 'green') bg-green-100 text-green-700
                                            @elseif($statutCouleur === 'red') bg-red-100 text-red-700
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            <i class="fas {{ $paiement->statut_icone }} mr-1"></i>
                                            {{ $paiement->statut_libelle }}
                                        </span>
                                    </td>

                                    <!-- Date -->
                                    <td class="px-4 py-4 text-center">
                                        <div class="text-sm text-gray-900">{{ $paiement->date_effectif_paiement?->format('d/m/Y') ?? 'Non définie' }}</div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            @can('paiements.view-details')
                                            <a href="{{ route('paiements.show', ['factureId' => $facture->id_facture, 'paiement' => $paiement->id_paiement]) }}"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan

                                            @can('paiements.update')
                                                @if($paiement->peutEtreModifie())
                                                    <a href="{{ route('paiements.edit', ['factureId' => $facture->id_facture, 'paiement' => $paiement->id_paiement]) }}"
                                                        class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Vue Mobile -->
                <div class="lg:hidden divide-y divide-gray-200">
                    @foreach($paiements as $paiement)
                        @php
                            $facture = $paiement->facture;
                            $proforma = $facture?->proforma;
                            $prestataire = $proforma?->getPrestataire();
                            $banque = $paiement->banque;
                            $statutCouleur = $paiement->statut_couleur;
                        @endphp
                        <div class="p-4">
                            <!-- En-tête carte -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-file-invoice text-green-600"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('paiements.show', ['factureId' => $facture->id_facture, 'paiement' => $paiement->id_paiement]) }}"
                                            class="font-medium text-gray-900 hover:text-green-600">
                                            {{ $facture->numero_facture }}
                                        </a>
                                        <p class="text-xs text-gray-500">{{ $paiement->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($statutCouleur === 'yellow') bg-yellow-100 text-yellow-700
                                    @elseif($statutCouleur === 'blue') bg-blue-100 text-blue-700
                                    @elseif($statutCouleur === 'indigo') bg-indigo-100 text-indigo-700
                                    @elseif($statutCouleur === 'green') bg-green-100 text-green-700
                                    @elseif($statutCouleur === 'red') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    <i class="fas {{ $paiement->statut_icone }} mr-1"></i>
                                    {{ $paiement->statut_libelle }}
                                </span>
                            </div>

                            <!-- Détails -->
                            <div class="space-y-2 text-sm">
                                @if($prestataire)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Prestataire</span>
                                        <span class="font-medium text-gray-900">{{ Str::limit($prestataire->raison_sociale_prestataire, 25) }}</span>
                                    </div>
                                @endif
                                @if($banque)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Banque</span>
                                        <span class="font-medium text-gray-900">{{ $banque->nom_banque }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Montant</span>
                                    <span class="font-bold text-green-600">{{ $paiement->montant_formate }}</span>
                                </div>
                            </div>

                            <!-- Actions mobile -->
                            <div class="mt-3 pt-3 border-t border-gray-100 flex justify-end space-x-2">
                                <a href="{{ route('paiements.show', ['factureId' => $facture->id_facture, 'paiement' => $paiement->id_paiement]) }}"
                                    class="px-3 py-1.5 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-eye mr-1"></i>Voir
                                </a>
                                @if($paiement->peutEtreModifie())
                                    <a href="{{ route('paiements.edit', ['factureId' => $facture->id_facture, 'paiement' => $paiement->id_paiement]) }}"
                                        class="px-3 py-1.5 text-orange-600 bg-orange-50 hover:bg-orange-100 rounded-lg text-sm transition-colors">
                                        <i class="fas fa-edit mr-1"></i>Modifier
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                    {{ $paiements->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </main>
@endsection

@push('scripts')
<script>
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

    // Sur desktop (lg+), toujours afficher les stats
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
