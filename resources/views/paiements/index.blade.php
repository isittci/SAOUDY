@extends('layouts.main')
@section('title', 'Paiements - ' . $facture->numero_facture)
@section('breadcrumb')
    <a @can('factures.read') href="{{ route('factures.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Factures</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('factures.view-details') href="{{ route('factures.show', $facture->id_facture) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">{{ $facture->numero_facture }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Paiements</span>
@endsection

@section('content')
    <!-- Filters Bar -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <!-- Info Facture -->
            <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex items-center">
                    <i class="fas fa-file-invoice text-blue-500 text-2xl mr-3"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-blue-800">
                            Paiements de la facture :
                            <a @can('factures.view-details') href="{{ route('factures.show', $facture->id_facture) }}" @endcan class="font-bold hover:underline">
                                {{ $facture->numero_facture }}
                            </a>
                        </p>
                        <p class="text-xs text-blue-600 mt-1">
                            Prestataire : {{ $facture->proforma->getPrestataire()->raison_sociale_prestataire }}
                            | Montant facture : {{ number_format($facture->montant_ht_facture ?? 0, 0, ',', ' ') }} FCFA
                            @if (method_exists($facture, 'getResteAPayer'))
                                | Reste à payer : {{ number_format($facture->getResteAPayer(), 0, ',', ' ') }} FCFA
                            @endif
                        </p>
                    </div>
                    @can('factures.view-details')
                    <a href="{{ route('factures.show', $facture->id_facture) }}"
                        class="ml-4 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-all text-sm font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour à la facture
                    </a>
                    @endcan
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et bouton créer -->
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-money-bill-wave text-green-500"></i>
                        <span>Paiements</span>
                    </h1>
                    @can('paiements.create')
                    <a href="{{ route('paiements.create', ['factureId' => $factureId]) }}"
                        class="md:hidden px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Nouveau</span>
                    </a>
                    @endcan
                </div>

                <!-- Filtres et actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Recherche -->
                    <div class="relative flex-1 sm:min-w-[250px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Rechercher..." value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent hover:border-green-300 transition-all" />
                    </div>

                    <!-- Filtre statut -->
                    <select id="statutFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent hover:border-green-300 transition-all cursor-pointer">
                        <option value="">Tous les statuts</option>
                        <option value="0" {{ request('statut') == '0' ? 'selected' : '' }}>En attente</option>
                        <option value="1" {{ request('statut') == '1' ? 'selected' : '' }}>Validé</option>
                        <option value="2" {{ request('statut') == '2' ? 'selected' : '' }}>En traitement</option>
                        <option value="3" {{ request('statut') == '3' ? 'selected' : '' }}>Payé</option>
                        <option value="4" {{ request('statut') == '4' ? 'selected' : '' }}>Rejeté</option>
                        <option value="5" {{ request('statut') == '5' ? 'selected' : '' }}>Annulé</option>
                    </select>

                    <!-- Boutons actions -->
                    {{-- <button onclick="showStatistiques()"
                        class="px-4 py-2.5 bg-white border border-blue-300 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                        <i class="fas fa-chart-bar text-sm"></i>
                        <span class="text-sm font-medium">Stats</span>
                    </button> --}}
                    @can('paiements.create')
                    <a href="{{ route('paiements.create', ['factureId' => $factureId]) }}"
                        class="hidden md:flex px-6 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all duration-200 items-center space-x-2 shadow-md">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm font-medium">Faire un paiement</span>
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Filtres avancés -->
            <div id="advancedFilters" class="hidden mt-4 pt-4 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date début</label>
                        <input type="date" id="dateDebut" value="{{ request('date_debut') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date fin</label>
                        <input type="date" id="dateFin" value="{{ request('date_fin') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Montant min</label>
                        <input type="number" id="montantMin" value="{{ request('montant_min') }}" placeholder="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Montant max</label>
                        <input type="number" id="montantMax" value="{{ request('montant_max') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400">
                    </div>
                </div>
                <div class="flex justify-end mt-3 space-x-2">
                    <button onclick="resetFilters()"
                        class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-all text-sm">
                        Réinitialiser
                    </button>
                    <button onclick="applyFilters()"
                        class="px-4 py-2 bg-green-500 text-white hover:bg-green-600 rounded-lg transition-all text-sm">
                        Appliquer
                    </button>
                </div>
            </div>

            <button onclick="toggleAdvancedFilters()"
                class="mt-3 text-sm text-green-600 hover:text-green-700 flex items-center">
                <i class="fas fa-filter mr-1"></i>
                <span id="filterToggleText">Afficher les filtres avancés</span>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages -->
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

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Total Paiements</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-file-invoice-dollar text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">En attente</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['en_attente'] ?? 0 }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Payés</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['payes'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-check-double text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Montant Payé</p>
                        <p class="text-xl font-bold text-gray-800 mt-1">
                            {{ number_format($stats['montant_paye'] ?? 0, 0, ',', ' ') }} F</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-coins text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- En-tête -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Liste des paiements (<span id="totalCount">{{ $paiements->total() }}</span>)
                    </h2>
                    <div class="flex items-center space-x-2">
                        @can('paiements.view-history')
                        <a href="{{ route('paiements.trashed', ['factureId' => $factureId]) }}"
                            class="px-3 py-2 text-gray-600 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-200"
                            title="Corbeille">
                            <i class="fas fa-trash text-sm"></i>
                        </a>
                        @endcan
                        <button onclick="refreshTable()"
                            class="px-3 py-2 text-gray-600 hover:text-green-500 hover:bg-green-50 rounded-lg transition-all duration-200">
                            <i class="fas fa-sync-alt text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Actions en masse -->
            <div id="bulkActions" class="hidden px-6 py-3 bg-blue-50 border-b border-blue-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">
                        <span id="selectedCount">0</span> paiement(s) sélectionné(s)
                    </span>
                    <div class="flex items-center space-x-2">
                        @can('paiements.validate')
                        <button onclick="validerMasse()"
                            class="px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm">
                            <i class="fas fa-check mr-1"></i> Valider
                        </button>
                        @endcan

                        @can('paiements.confirm')
                        <button onclick="confirmerMasse()"
                            class="px-3 py-1.5 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm">
                            <i class="fas fa-check-double mr-1"></i> Confirmer
                        </button>
                        @endcan

                        <button onclick="deselectAll()"
                            class="px-3 py-1.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-4 text-center">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()"
                                    class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Référence</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Montant</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Banque</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Statut</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Date</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($paiements as $paiement)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox"
                                        class="payment-checkbox w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                        value="{{ $paiement->id_paiement }}" onchange="updateBulkActions()">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a @can('paiements.view-details') href="{{ route('paiements.show', ['factureId' => $factureId, 'paiement' => $paiement->id_paiement]) }}" @endcan
                                        class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $paiement->reference_paiement }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ number_format($paiement->montant_net_paye_paiement, 0, ',', ' ') }}
                                    </div>
                                    <div class="text-xs text-gray-500">FCFA</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $paiement->banque->nom_banque ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $paiement->banque->numero_compte_masque ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $couleurs = [
                                            0 => 'yellow',
                                            1 => 'blue',
                                            2 => 'indigo',
                                            3 => 'green',
                                            4 => 'red',
                                            5 => 'gray',
                                        ];
                                        $couleur = $couleurs[$paiement->statut_paiement] ?? 'gray';
                                    @endphp
                                    
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-{{ $couleur }}-100 text-{{ $couleur }}-800">
                                        <i class="fas {{ $paiement->statut_icone }} mr-1"></i>
                                        {{ $paiement->statut_libelle }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    {{ $paiement->created_at->format('d/m/Y') }}
                                </td>
                                @canany(['paiements.view-details', 'paiements.validate', 'paiements.process', 'paiements.confirm', 'paiements.reject', 'paiements.pending', 'paiements.delete'])
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            @can('paiements.view-details')
                                                <a href="{{ route('paiements.show', ['factureId' => $factureId, 'paiement' => $paiement->id_paiement]) }}"
                                                    class="text-blue-600 hover:text-blue-800 transition-colors" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan

                                            @canany(['paiements.validate', 'paiements.process', 'paiements.confirm', 'paiements.reject', 'paiements.pending', 'paiements.delete'])
                                                <!-- Menu dropdown -->
                                                @if ($paiement->peutEtreValide() || in_array($paiement->statut_paiement, [1, 2]) || $paiement->peutEtreRejete() || $paiement->statut_paiement != 3)
                                                    <div class="relative">
                                                        <button onclick="toggleMenu('{{ $paiement->id_paiement }}')"
                                                            class="text-gray-600 hover:text-gray-800 transition-colors">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <div id="menu-{{ $paiement->id_paiement }}"
                                                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-20">
                                                            <div class="py-1">
                                                                @can('paiements.validate')
                                                                @if ($paiement->peutEtreValide())
                                                                    <button onclick="valider('{{ $paiement->id_paiement }}')"
                                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-check text-blue-500 mr-2"></i> Valider
                                                                    </button>
                                                                @endif
                                                                @endcan

                                                                @can('paiements.process')
                                                                @if ($paiement->statut_paiement == 1)
                                                                    <button
                                                                        onclick="mettreEnTraitement('{{ $paiement->id_paiement }}')"
                                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-spinner text-indigo-500 mr-2"></i> Traitement
                                                                    </button>
                                                                @endif
                                                                @endcan

                                                                @can('paiements.confirm')
                                                                @if (in_array($paiement->statut_paiement, [1, 2]))
                                                                    <button onclick="confirmer('{{ $paiement->id_paiement }}')"
                                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-check-double text-green-500 mr-2"></i>
                                                                        Confirmer
                                                                    </button>
                                                                @endif
                                                                @endcan

                                                                @can('paiements.reject')
                                                                @if ($paiement->peutEtreRejete())
                                                                    <button onclick="showRejectModal('{{ $paiement->id_paiement }}')"
                                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-times-circle text-red-500 mr-2"></i> Rejeter
                                                                    </button>
                                                                @endif
                                                                @endcan

                                                                @can('paiements.pending')
                                                                @if($paiement->statut_paiement == 4)
                                                                    <button onclick="remettreEnAttente('{{$paiement->id_paiement}}')"
                                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-undo mr-2 text-yellow-500"></i>
                                                                        Remettre en attente
                                                                    </button>
                                                                @endif
                                                                @endcan

                                                                @can('paiements.delete')
                                                                @if ($paiement->statut_paiement != 3)
                                                                    <button onclick="confirmDelete('{{ $paiement->id_paiement }}')"
                                                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                                                        <i class="fas fa-trash mr-2"></i> Supprimer
                                                                    </button>
                                                                @endif
                                                                @endcan
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endcanany
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <i class="fas fa-inbox text-5xl mb-4 text-gray-300"></i>
                                        <p class="text-lg font-medium">Aucun paiement trouvé</p>
                                        @can('paiements.create')
                                        <a href="{{ route('paiements.create', ['factureId' => $factureId]) }}"
                                            class="mt-4 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all">
                                            <i class="fas fa-plus mr-2"></i>Créer le premier paiement
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($paiements->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $paiements->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- Modal Rejet -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Rejeter le paiement</h3>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Motif du rejet *</label>
                <textarea id="rejectMotif" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400 focus:border-transparent"
                    placeholder="Expliquez pourquoi ce paiement est rejeté (minimum 10 caractères)"></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button onclick="closeRejectModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                    Annuler
                </button>
                @can('paiements.reject')
                <button onclick="executeReject()"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all">
                    Confirmer le rejet
                </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- Modal Suppression -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Confirmer la suppression</h3>
            </div>
            <div class="p-6">
                <p id="deleteMessage" class="text-gray-700"></p>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                    Annuler
                </button>
                @can('paiements.delete')
                <button onclick="executeDelete()"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all">
                    Supprimer
                </button>
                @endcan
            </div>
        </div>
    </div>
    @can('paiements.read')
    @push('scripts')
        <script>
            const factureId = '{{ $factureId }}';
            let currentPaiementId = null;
            let selectedPayments = new Set();

            function toggleAdvancedFilters() {
                const filters = document.getElementById('advancedFilters');
                const text = document.getElementById('filterToggleText');
                filters.classList.toggle('hidden');
                text.textContent = filters.classList.contains('hidden') ?
                    'Afficher les filtres avancés' : 'Masquer les filtres avancés';
            }

            function applyFilters() {
                const search = document.getElementById('searchInput').value;
                const statut = document.getElementById('statutFilter').value;
                const dateDebut = document.getElementById('dateDebut')?.value;
                const dateFin = document.getElementById('dateFin')?.value;
                const montantMin = document.getElementById('montantMin')?.value;
                const montantMax = document.getElementById('montantMax')?.value;

                const params = new URLSearchParams();
                if (search) params.append('search', search);
                if (statut) params.append('statut', statut);
                if (dateDebut) params.append('date_debut', dateDebut);
                if (dateFin) params.append('date_fin', dateFin);
                if (montantMin) params.append('montant_min', montantMin);
                if (montantMax) params.append('montant_max', montantMax);

                window.location.href = "{{ route('paiements.index', ':factureId') }}".replace(':factureId', factureId)+ `?${params.toString()}`;
            }

            function resetFilters() {
                window.location.href = "{{ route('paiements.index', ':factureId') }}".replace(':factureId', factureId);
            }

            function toggleMenu(id) {
                document.querySelectorAll('[id^="menu-"]').forEach(m => {
                    if (m.id !== `menu-${id}`) m.classList.add('hidden');
                });
                document.getElementById(`menu-${id}`).classList.toggle('hidden');
            }

            function toggleSelectAll() {
                const checkboxes = document.querySelectorAll('.payment-checkbox');
                const selectAll = document.getElementById('selectAll');
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                    if (selectAll.checked) {
                        selectedPayments.add(cb.value);
                    } else {
                        selectedPayments.delete(cb.value);
                    }
                });
                updateBulkActions();
            }

            function updateBulkActions() {
                const checkboxes = document.querySelectorAll('.payment-checkbox:checked');
                selectedPayments.clear();
                checkboxes.forEach(cb => selectedPayments.add(cb.value));

                const bulkActions = document.getElementById('bulkActions');
                const selectedCount = document.getElementById('selectedCount');

                if (selectedPayments.size > 0) {
                    bulkActions.classList.remove('hidden');
                    selectedCount.textContent = selectedPayments.size;
                } else {
                    bulkActions.classList.add('hidden');
                }
            }

            function deselectAll() {
                document.querySelectorAll('.payment-checkbox').forEach(cb => cb.checked = false);
                document.getElementById('selectAll').checked = false;
                selectedPayments.clear();
                updateBulkActions();
            }

            function valider(id) {
                if (confirm('Voulez-vous valider ce paiement ?')) {
                    fetch("{{ route('paiements.valider', [':factureId', ':paiement']) }}".replace(':factureId', factureId).replace(':paiement', id), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        });
                }
            }

            function mettreEnTraitement(id) {
                if (confirm('Mettre ce paiement en traitement bancaire ?')) {
                    fetch("{{ route('paiements.traitement', [':factureId', ':paiement']) }}".replace(':factureId', factureId).replace(':paiement', id), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        });
                }
            }

            function confirmer(id) {
                if (confirm('Confirmer que ce paiement a été effectué ?')) {
                    fetch("{{ route('paiements.confirmer', [':factureId', ':paiement']) }}".replace(':factureId', factureId).replace(':paiement', id), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        });
                }
            }

            function showRejectModal(id) {
                currentPaiementId = id;
                document.getElementById('rejectMotif').value = '';
                document.getElementById('rejectModal').classList.remove('hidden');
            }

            function closeRejectModal() {
                document.getElementById('rejectModal').classList.add('hidden');
                currentPaiementId = null;
            }

            function executeReject() {
                const motif = document.getElementById('rejectMotif').value.trim();
                if (motif.length < 10) {
                    alert('Le motif doit contenir au moins 10 caractères');
                    return;
                }

                fetch("{{ route('paiements.rejeter', [':factureId', ':currentPaiementId']) }}".replace(':factureId', factureId).replace(':currentPaiementId', currentPaiementId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            motif_rejet: motif
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    });
            }

            function confirmDelete(id) {
                currentPaiementId = id;
                document.getElementById('deleteMessage').textContent =
                    'Êtes-vous sûr de vouloir supprimer ce paiement ?';
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
                currentPaiementId = null;
            }

            function executeDelete() {
                fetch("{{ route('paiements.destroy', [':factureId', ':currentPaiementId']) }}".replace(':factureId', factureId).replace(':currentPaiementId', currentPaiementId), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message);
                            closeDeleteModal();
                        }
                    });
            }

            function validerMasse() {
                if (selectedPayments.size === 0) return;
                if (confirm(`Valider ${selectedPayments.size} paiement(s) ?`)) {
                    fetch("{{ route('paiements.valider-masse', ':factureId') }}".replace(':factureId', factureId), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                paiement_ids: Array.from(selectedPayments)
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        });
                }
            }

            function confirmerMasse() {
                if (selectedPayments.size === 0) return;
                if (confirm(`Confirmer ${selectedPayments.size} paiement(s) ?`)) {
                    fetch("{{ route('paiements.confirmer-masse', ':factureId') }}".replace(':factureId', factureId), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                paiement_ids: Array.from(selectedPayments)
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        });
                }
            }

            function remettreEnAttente(id) {
                if (confirm('Remettre ce paiement en attente ?')) {
                    fetch("{{ route('paiements.remettre-attente', [':factureId', ':id']) }}".replace(':factureId', factureId).replace(':id', id), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    });
                }
            }

            function showStatistiques() {
                fetch("{{ route('paiements.statistiques', ':factureId') }}".replace(':factureId', factureId), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const stats = data.data;
                            alert(`Statistiques des paiements:
                            Total: ${stats.total}
                            En attente: ${stats.en_attente}
                            Validés: ${stats.valides}
                            Payés: ${stats.payes}
                            Rejetés: ${stats.rejetes}

                            Montant total: ${stats.montant_total.toLocaleString('fr-FR')} FCFA
                            Montant payé: ${stats.montant_paye.toLocaleString('fr-FR')} FCFA`);
                        }
                    });
            }

            function refreshTable() {
                location.reload();
            }

            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applyFilters();
                }, 500);
            });

            document.getElementById('statutFilter').addEventListener('change', applyFilters);

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeRejectModal();
                    closeDeleteModal();
                    document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
                }
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('[id^="menu-"]') && !e.target.closest('button[onclick^="toggleMenu"]')) {
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
