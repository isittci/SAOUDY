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
                    <a href="{{ route('factures.create') }}"
                        class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 shadow-md">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="hidden sm:inline">Nouvelle Facture</span>
                        <span class="sm:hidden">Nouveau</span>
                    </a>
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
                            <p class="text-lg sm:text-xl font-bold mt-1 truncate">{{ number_format($statistiques['montant_total'], 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
                        </div>
                        <i class="fas fa-coins text-2xl sm:text-3xl text-orange-300 opacity-50 ml-2 flex-shrink-0"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-green-100 text-xs font-medium uppercase truncate">Montant Payé</p>
                            <p class="text-lg sm:text-xl font-bold mt-1 truncate">{{ number_format($statistiques['montant_paye'], 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
                        </div>
                        <i class="fas fa-hand-holding-usd text-2xl sm:text-3xl text-green-300 opacity-50 ml-2 flex-shrink-0"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-red-100 text-xs font-medium uppercase truncate">Reste à Payer</p>
                            <p class="text-lg sm:text-xl font-bold mt-1 truncate">{{ number_format($statistiques['montant_restant'], 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
                        </div>
                        <i class="fas fa-exclamation-triangle text-2xl sm:text-3xl text-red-300 opacity-50 ml-2 flex-shrink-0"></i>
                    </div>
                    <div class="mt-2">
                        <div class="w-full bg-red-400 rounded-full h-1.5">
                            <div class="bg-white rounded-full h-1.5" style="width: {{ $statistiques['taux_paiement'] }}%"></div>
                        </div>
                        <p class="text-xs text-red-100 mt-1">{{ $statistiques['taux_paiement'] }}% payé</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-base sm:text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-filter text-orange-500 mr-2"></i>
                    Filtres
                </h2>
                <!-- Bouton toggle filtres sur mobile -->
                <button type="button" onclick="toggleFilters()" class="sm:hidden p-2 text-gray-500 hover:text-gray-700">
                    <i id="filtersChevron" class="fas fa-chevron-down transition-transform duration-200"></i>
                </button>
            </div>
            <div id="filtersSection" class="p-4 sm:p-6 hidden sm:block">
                <form action="{{ route('factures.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                            placeholder="N° facture, proforma...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400">
                            <option value="">Tous les statuts</option>
                            @foreach($statuts as $key => $label)
                                <option value="{{ $key }}" {{ request('statut') === $key ? 'selected' : '' }}>{{ $label }}</option>
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
                        <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all">
                            <i class="fas fa-search mr-2"></i>Filtrer
                        </button>
                        <a href="{{ route('factures.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                            <i class="fas fa-undo"></i>
                        </a>
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

            @if($factures->isEmpty())
                <div class="p-8 sm:p-12 text-center">
                    <i class="fas fa-file-invoice text-5xl sm:text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-600 mb-2">Aucune facture trouvée</h3>
                    <p class="text-gray-500 mb-4">Commencez par créer une nouvelle facture.</p>
                    <a href="{{ route('factures.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700">
                        <i class="fas fa-plus mr-2"></i>Nouvelle Facture
                    </a>
                </div>
            @else
                <!-- Vue mobile: cartes -->
                <div class="block lg:hidden divide-y divide-gray-200">
                    @foreach($factures as $facture)
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
                            $pourcentage = $facture->montant_facture > 0
                                ? round(($facture->montant_paye / $facture->montant_facture) * 100)
                                : 0;
                        @endphp
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-file-invoice text-orange-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $facture->numero_facture }}</p>
                                        <p class="text-xs text-gray-500">{{ $facture->date_facture->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $statutClasses[$facture->statut_facture] ?? 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas fa-{{ $statutIcons[$facture->statut_facture] ?? 'question' }} mr-1"></i>
                                    {{ $facture->statut_libelle }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-3 text-sm">
                                <div>
                                    <p class="text-gray-500">Proforma</p>
                                    <p class="font-medium text-gray-900">{{ $facture->proforma->numero_proforma ?? 'N/A' }}</p>
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
                                    <div class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: {{ $pourcentage }}%"></div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end space-x-2 pt-2 border-t border-gray-100">
                                <a href="{{ route('factures.show', $facture->id_facture) }}"
                                    class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($facture->peutEtreModifiee())
                                    <a href="{{ route('factures.edit', $facture->id_facture) }}"
                                        class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                @if($facture->peutEtreValidee())
                                    <form action="{{ route('factures.valider', $facture->id_facture) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <button onclick="openDeleteModal('{{ $facture->id_facture }}', '{{ $facture->numero_facture }}')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Vue desktop: tableau -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">N° Facture</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Proforma</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Paiement</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($factures as $facture)
                            {{-- {{ dd($facture->proforma->prestataireLotsAttributions) }}
                            {{ dd($facture->proforma->prestataireLotsAttributions[0]->prestataire) }}
                            {{ dd($facture->proforma->prestataireLotsAttributions[0]->lot) }} --}}
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
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-file-invoice text-orange-600"></i>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-900">{{ $facture->numero_facture }}</span>
                                                <p class="text-xs text-gray-500">{{ $facture->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($facture->proforma)
                                            <span class="text-sm font-medium text-gray-900">{{ $facture->proforma->numero_proforma }}</span>
                                        @else
                                            <span class="text-sm text-gray-400 italic">Non définie</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-sm font-bold text-gray-900">{{ $facture->montant_formate }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $facture->date_facture->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">Reçue: {{ $facture->date_reception_facture->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statutClasses[$facture->statut_facture] ?? 'bg-gray-100 text-gray-800' }}">
                                            <i class="fas fa-{{ $statutIcons[$facture->statut_facture] ?? 'question' }} mr-1"></i>
                                            {{ $facture->statut_libelle }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $pourcentage = $facture->montant_facture > 0
                                                ? round(($facture->montant_paye / $facture->montant_facture) * 100)
                                                : 0;
                                        @endphp
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mb-1">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $pourcentage }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-600">{{ $pourcentage }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-1">
                                            <a href="{{ route('factures.show', $facture->id_facture) }}"
                                                class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                                title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($facture->peutEtreModifiee())
                                                <a href="{{ route('factures.edit', $facture->id_facture) }}"
                                                    class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                                                    title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            @if($facture->peutEtreValidee())
                                                <form action="{{ route('factures.valider', $facture->id_facture) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                                        title="Valider">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($facture->peutRecevoirPaiement())
                                                <a href="#"
                                                    class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                                    title="Ajouter un paiement">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </a>
                                            @endif
                                            <button onclick="openDeleteModal('{{ $facture->id_facture }}', '{{ $facture->numero_facture }}')"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
                        <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">
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
