@extends('layouts.main')

@section('title', 'Tableau de bord')

@push('styles')
    <style>
        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .chart-container-sm {
            position: relative;
            height: 250px;
        }

        .alert-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .progress-animated {
            animation: progressAnimation 1.5s ease-in-out;
        }

        @keyframes progressAnimation {
            from {
                width: 0%;
            }
        }

        .scroll-container {
            max-height: 400px;
            overflow-y: auto;
        }

        .gradient-blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-green {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .gradient-orange {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .gradient-purple {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .gradient-red {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
        }

        .gradient-teal {
            background: linear-gradient(135deg, #0093E9 0%, #80D0C7 100%);
        }

        .periode-btn {
            transition: all 0.2s ease;
        }

        .periode-btn:hover {
            transform: scale(1.02);
        }

        .periode-btn.active {
            ring: 2px;
            ring-offset: 2px;
        }

        .variation-positive {
            color: #10b981;
        }

        .variation-negative {
            color: #ef4444;
        }
    </style>
@endpush

@section('breadcrumb')
    <span class="text-white font-medium">Tableau de bord</span>
@endsection

@section('content')
    <!-- Header avec Sélecteur de Période -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Tableau de bord</h1>
                    <p class="text-gray-600 mt-1 text-sm flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>
                        <span class="font-medium text-orange-600">{{ $periode['label'] }}</span>
                    </p>
                </div>

                <!-- Sélecteur de Période -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <!-- Boutons de période rapide -->
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('dashboard', ['periode' => 'mois_courant']) }}"
                            class="periode-btn px-3 py-1.5 text-xs font-medium rounded-lg transition-all
                                    {{ $periode['type'] === 'mois_courant' ? 'bg-orange-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            <i class="fas fa-calendar-day mr-1"></i>Mois
                        </a>
                        <a href="{{ route('dashboard', ['periode' => 'trimestre_courant']) }}"
                            class="periode-btn px-3 py-1.5 text-xs font-medium rounded-lg transition-all
                                    {{ $periode['type'] === 'trimestre_courant' ? 'bg-orange-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            <i class="fas fa-calendar-week mr-1"></i>Trimestre
                        </a>
                        <a href="{{ route('dashboard', ['periode' => 'semestre_courant']) }}"
                            class="periode-btn px-3 py-1.5 text-xs font-medium rounded-lg transition-all
                                    {{ $periode['type'] === 'semestre_courant' ? 'bg-orange-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            <i class="fas fa-calendar mr-1"></i>Semestre
                        </a>
                        <a href="{{ route('dashboard', ['periode' => 'annee_courante']) }}"
                            class="periode-btn px-3 py-1.5 text-xs font-medium rounded-lg transition-all
                                    {{ $periode['type'] === 'annee_courante' ? 'bg-orange-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            <i class="fas fa-calendar-alt mr-1"></i>Année
                        </a>
                        <a href="{{ route('dashboard', ['periode' => 'annee_precedente']) }}"
                            class="periode-btn px-3 py-1.5 text-xs font-medium rounded-lg transition-all
                                    {{ $periode['type'] === 'annee_precedente' ? 'bg-orange-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            <i class="fas fa-history mr-1"></i>{{ now()->subYear()->year }}
                        </a>
                        <a href="{{ route('dashboard', ['periode' => 'tout']) }}"
                            class="periode-btn px-3 py-1.5 text-xs font-medium rounded-lg transition-all
                                    {{ $periode['type'] === 'tout' ? 'bg-orange-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            <i class="fas fa-infinity mr-1"></i>Tout
                        </a>
                    </div>

                    <!-- Séparateur -->
                    <div class="hidden sm:block w-px h-8 bg-gray-300"></div>

                    <!-- Période personnalisée -->
                    <button type="button" onclick="togglePeriodePersonnalisee()"
                        class="periode-btn px-3 py-1.5 text-xs font-medium rounded-lg transition-all
                                    {{ $periode['type'] === 'personnalise' ? 'bg-orange-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        <i class="fas fa-sliders-h mr-1"></i>Personnalisé
                    </button>

                    <!-- Bouton rafraîchir -->
                    <button onclick="location.reload()"
                        class="p-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors" title="Rafraîchir">
                        <i class="fas fa-sync-alt text-gray-600"></i>
                    </button>
                </div>
            </div>

            <!-- Formulaire Période Personnalisée -->
            <div id="periodePersonnalisee" class="mt-4 {{ $periode['type'] === 'personnalise' ? '' : 'hidden' }}">
                <form action="{{ route('dashboard') }}" method="GET"
                    class="flex flex-wrap items-end gap-3 p-4 bg-orange-50 rounded-xl border border-orange-200">
                    <input type="hidden" name="periode" value="personnalise">

                    <div class="flex-1 min-w-[150px]">
                        <label for="date_debut" class="block text-xs font-medium text-gray-700 mb-1">Date de début</label>
                        <input type="date" id="date_debut" name="date_debut"
                            value="{{ $periode['date_debut_input'] ?? now()->startOfYear()->format('Y-m-d') }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <div class="flex-1 min-w-[150px]">
                        <label for="date_fin" class="block text-xs font-medium text-gray-700 mb-1">Date de fin</label>
                        <input type="date" id="date_fin" name="date_fin"
                            value="{{ $periode['date_fin_input'] ?? now()->endOfYear()->format('Y-m-d') }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <button type="submit"
                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-filter mr-1"></i>Appliquer
                    </button>

                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-times mr-1"></i>Réinitialiser
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Alertes -->
        @if (array_sum($alertes) > 0)
            <div class="mb-6 bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4 shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-bell text-amber-500 text-xl mr-3 mt-0.5 alert-badge"></i>
                    <div class="flex-1">
                        <h3 class="text-amber-800 font-semibold mb-2">Notifications (données actuelles)</h3>
                        <div class="flex flex-wrap gap-3 text-sm">
                            @if ($alertes['factures_en_attente'] > 0)
                                <a @can('factures.read') href="{{ route('factures.index', ['statut' => 'en_attente']) }}" @endcan
                                    class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full hover:bg-yellow-200 transition-colors">
                                    <i class="fas fa-file-invoice mr-1"></i>
                                    {{ $alertes['factures_en_attente'] }} facture(s) en attente
                                </a>
                            @endif
                            @if ($alertes['paiements_a_valider'] > 0)
                                <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                                    <i class="fas fa-money-check mr-1"></i>
                                    {{ $alertes['paiements_a_valider'] }} paiement(s) à valider
                                </span>
                            @endif
                            
                            @if ($alertes['lots_non_attribues'] > 0)
                                <a @can('lots.read') href="{{ route('lots.index', ['attribution' => '0']) }}" @endcan
                                    class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-800 rounded-full hover:bg-orange-200 transition-colors">
                                    <i class="fas fa-box-open mr-1"></i>
                                    {{ $alertes['lots_non_attribues'] }} lot(s) non attribué(s)
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Statistiques Globales - Cartes -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <!-- Appels d'Offres -->
            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Appels d'Offres</span>
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bullhorn text-purple-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $statsGlobales['appels_offres']['total'] }}</p>
                <div class="flex items-center justify-between mt-2 text-xs">
                    {{-- <span class="text-green-600">
                            <i class="fas fa-clock mr-1"></i>{{ $statsGlobales['appels_offres']['en_cours'] }} en cours
                        </span> --}}
                    @if ($comparaison ?? false)
                        <span
                            class="{{ $comparaison['appels_offres']['variation'] >= 0 ? 'variation-positive' : 'variation-negative' }}">
                            <i
                                class="fas fa-{{ $comparaison['appels_offres']['variation'] >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                            {{ abs($comparaison['appels_offres']['variation']) }}%
                        </span>
                    @endif
                </div>
            </div>

            <!-- Lots -->
            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Lots</span>
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cubes text-blue-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $statsGlobales['lots']['total'] }}</p>
                <div class="flex items-center mt-2 text-xs">
                    <span class="text-green-600">
                        <i class="fas fa-check mr-1"></i>{{ $statsGlobales['lots']['attribues'] }} attribués
                    </span>
                </div>
            </div>

            <!-- Prestataires -->
            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Prestataires</span>
                    <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-teal-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $statsGlobales['prestataires']['total'] }}</p>
                <div class="flex items-center mt-2 text-xs">
                    <span class="text-green-600">
                        <i class="fas fa-user-check mr-1"></i>{{ $statsGlobales['prestataires']['actifs'] }} actifs
                    </span>
                </div>
            </div>

            <!-- Factures -->
            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Factures</span>
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-orange-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $statsGlobales['factures']['total'] }}</p>
                <div class="flex items-center justify-between mt-2 text-xs">
                    <span class="text-yellow-600">
                        <i class="fas fa-hourglass-half mr-1"></i>{{ $statsGlobales['factures']['en_attente'] }} en
                        attente
                    </span>
                    @if ($comparaison ?? false)
                        <span
                            class="{{ $comparaison['factures']['variation'] >= 0 ? 'variation-positive' : 'variation-negative' }}">
                            <i
                                class="fas fa-{{ $comparaison['factures']['variation'] >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                            {{ abs($comparaison['factures']['variation']) }}%
                        </span>
                    @endif
                </div>
            </div>

            <!-- Paiements -->
            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Paiements</span>
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-green-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $statsGlobales['paiements']['total'] }}</p>
                <div class="flex items-center justify-between mt-2 text-xs">
                    <span class="text-green-600">
                        <i class="fas fa-check-double mr-1"></i>{{ $statsGlobales['paiements']['payes'] }} effectués
                    </span>
                    @if ($comparaison ?? false)
                        <span
                            class="{{ $comparaison['paiements']['variation'] >= 0 ? 'variation-positive' : 'variation-negative' }}">
                            <i
                                class="fas fa-{{ $comparaison['paiements']['variation'] >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                            {{ abs($comparaison['paiements']['variation']) }}%
                        </span>
                    @endif
                </div>
            </div>

            <!-- Taux de paiement -->
            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Taux Paiement</span>
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-emerald-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-emerald-600">{{ $statsGlobales['factures']['taux_paiement'] }}%</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-emerald-500 h-2 rounded-full progress-animated"
                        style="width: {{ $statsGlobales['factures']['taux_paiement'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Résumé Financier -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="gradient-blue rounded-xl p-5 text-white shadow-lg stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-xs font-medium uppercase">Montant Total AO</p>
                        <p class="text-2xl font-bold mt-1">
                            {{ number_format($statsGlobales['appels_offres']['montant_total'], 0, ',', ' ') }}</p>
                        <p class="text-sm text-white/70">FCFA</p>
                    </div>
                    <i class="fas fa-chart-line text-4xl text-white/30"></i>
                </div>
            </div>

            <div class="gradient-orange rounded-xl p-5 text-white shadow-lg stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-xs font-medium uppercase">Montant Facturé</p>
                        <p class="text-2xl font-bold mt-1">
                            {{ number_format($statsGlobales['factures']['montant_total'], 0, ',', ' ') }}</p>
                        <p class="text-sm text-white/70">FCFA</p>
                    </div>
                    <i class="fas fa-file-invoice text-4xl text-white/30"></i>
                </div>
            </div>

            <div class="gradient-green rounded-xl p-5 text-white shadow-lg stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-xs font-medium uppercase">Montant Payé</p>
                        <p class="text-2xl font-bold mt-1">
                            {{ number_format($statsGlobales['factures']['montant_paye'], 0, ',', ' ') }}</p>
                        <p class="text-sm text-white/70">FCFA</p>
                    </div>
                    <i class="fas fa-hand-holding-usd text-4xl text-white/30"></i>
                </div>
            </div>

            <div class="gradient-red rounded-xl p-5 text-white shadow-lg stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-xs font-medium uppercase">Reste à Payer</p>
                        <p class="text-2xl font-bold mt-1">
                            {{ number_format($statsGlobales['factures']['montant_total'] - $statsGlobales['factures']['montant_paye'], 0, ',', ' ') }}
                        </p>
                        <p class="text-sm text-white/70">FCFA</p>
                    </div>
                    <i class="fas fa-exclamation-triangle text-4xl text-white/30"></i>
                </div>
            </div>
        </div>

        <!-- Graphiques Principaux -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Camembert - Appels d'offres par type -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-pie text-purple-500 mr-2"></i>
                        Appels d'Offres par Type
                    </h2>
                </div>
                <div class="p-6">
                    @if ($appelsParType->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="chart-container">
                                <canvas id="chartAppelsParTypeNombre"></canvas>
                            </div>
                            <div class="chart-container">
                                <canvas id="chartAppelsParTypeMontant"></canvas>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-center space-x-4 text-sm text-gray-500">
                            <span><i class="fas fa-circle text-purple-500 mr-1"></i> Nombre</span>
                            <span><i class="fas fa-circle text-indigo-500 mr-1"></i> Montant</span>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                            <i class="fas fa-chart-pie text-5xl mb-3"></i>
                            <p>Aucune donnée pour cette période</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Histogramme - Lots par prestataire -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                        Lots par Prestataire (Top 10)
                    </h2>
                </div>
                <div class="p-6">
                    @if ($lotsParPrestataire->count() > 0)
                        <div class="chart-container">
                            <canvas id="chartLotsParPrestataire"></canvas>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                            <i class="fas fa-chart-bar text-5xl mb-3"></i>
                            <p>Aucune donnée pour cette période</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Graphiques Secondaires -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Évolution des Appels d'Offres -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-line text-indigo-500 mr-2"></i>
                        Évolution des Appels d'Offres
                    </h2>
                </div>
                <div class="p-6">
                    @if ($evolutionAppelsOffres->count() > 0)
                        <div class="chart-container">
                            <canvas id="chartEvolutionAO"></canvas>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                            <i class="fas fa-chart-line text-5xl mb-3"></i>
                            <p>Aucune donnée pour cette période</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Taux d'Attribution des Lots -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-teal-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-tasks text-teal-500 mr-2"></i>
                        Taux d'Attribution
                    </h2>
                </div>
                <div class="p-6">
                    @if ($tauxAttributionLots['total'] > 0)
                        <div class="chart-container-sm">
                            <canvas id="chartTauxAttribution"></canvas>
                        </div>
                        <div class="text-center mt-4">
                            <p class="text-3xl font-bold text-teal-600">{{ $tauxAttributionLots['pourcentage'] }}%</p>
                            <p class="text-sm text-gray-500">des lots attribués</p>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                            <i class="fas fa-tasks text-5xl mb-3"></i>
                            <p>Aucune donnée pour cette période</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Factures et Paiements par Statut -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Factures par Statut -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-file-invoice text-orange-500 mr-2"></i>
                        Factures par Statut
                    </h2>
                </div>
                <div class="p-6">
                    @if ($facturesParStatut->count() > 0)
                        <div class="chart-container-sm">
                            <canvas id="chartFacturesStatut"></canvas>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                            <i class="fas fa-file-invoice text-5xl mb-3"></i>
                            <p>Aucune donnée pour cette période</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Paiements par Statut -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-money-check-alt text-green-500 mr-2"></i>
                        Paiements par Statut
                    </h2>
                </div>
                <div class="p-6">
                    @if ($paiementsParStatut->count() > 0)
                        <div class="chart-container-sm">
                            <canvas id="chartPaiementsStatut"></canvas>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                            <i class="fas fa-money-check-alt text-5xl mb-3"></i>
                            <p>Aucune donnée pour cette période</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Listes et Tableaux -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Top Prestataires -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-trophy text-amber-500 mr-2"></i>
                        Top 5 Prestataires
                    </h2>
                </div>
                <div class="p-4 scroll-container">
                    @forelse($topPrestataires as $index => $prestataire)
                        <div
                            class="flex items-center p-3 {{ $index > 0 ? 'border-t border-gray-100' : '' }} hover:bg-gray-50 rounded-lg transition-colors">
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center mr-3
                                    @if ($index === 0) bg-yellow-100 text-yellow-600
                                    @elseif($index === 1) bg-gray-200 text-gray-600
                                    @elseif($index === 2) bg-orange-100 text-orange-600
                                    @else bg-gray-100 text-gray-500 @endif font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 truncate">
                                    {{ $prestataire->raison_sociale_prestataire }}</p>
                                <p class="text-xs text-gray-500">{{ $prestataire->nombre_attributions }} attribution(s)
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-800 text-sm">
                                    {{ number_format($prestataire->montant_total, 0, ',', ' ') }}</p>
                                <p class="text-xs text-gray-500">FCFA</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">Aucun prestataire pour cette période</p>
                    @endforelse
                </div>
            </div>

            <!-- Dernières Factures -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div
                    class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-file-invoice-dollar text-orange-500 mr-2"></i>
                        Dernières Factures
                    </h2>
                    @can('factures.read')
                        <a href="{{ route('factures.index') }}" class="text-sm text-orange-600 hover:text-orange-800">
                            Voir tout <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @endcan
                </div>
                <div class="p-4 scroll-container">
                    @forelse($dernieresFactures as $facture)
                        @php
                            $statutClasses = [
                                'en_attente' => 'bg-yellow-100 text-yellow-800',
                                'validee' => 'bg-blue-100 text-blue-800',
                                'payee' => 'bg-green-100 text-green-800',
                                'partiellement_payee' => 'bg-orange-100 text-orange-800',
                                'rejetee' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <a @can('factures.view-details') href="{{ route('factures.show', $facture->id_facture) }}" @endcan
                            class="block p-3 hover:bg-gray-50 rounded-lg transition-colors {{ !$loop->first ? 'border-t border-gray-100' : '' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-medium text-gray-800">{{ $facture->numero_facture }}</span>
                                <span
                                    class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statutClasses[$facture->statut_facture] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $facture->statut_libelle ?? $facture->statut_facture }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">{{ $facture->created_at->format('d/m/Y') }}</span>
                                <span
                                    class="font-semibold text-gray-700">{{ number_format($facture->montant_facture, 0, ',', ' ') }}
                                    FCFA</span>
                            </div>
                        </a>
                    @empty
                        <p class="text-center text-gray-500 py-4">Aucune facture pour cette période</p>
                    @endforelse
                </div>
            </div>

            <!-- Derniers Paiements -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div
                    class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                        Derniers Paiements
                    </h2>
                </div>
                <div class="p-4 scroll-container">
                    @forelse($derniersPaiements as $paiement)
                        @php
                            $statutPaiementClasses = [
                                0 => 'bg-yellow-100 text-yellow-800',
                                1 => 'bg-blue-100 text-blue-800',
                                2 => 'bg-indigo-100 text-indigo-800',
                                3 => 'bg-green-100 text-green-800',
                                4 => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <div
                            class="p-3 {{ !$loop->first ? 'border-t border-gray-100' : '' }} hover:bg-gray-50 rounded-lg transition-colors">
                            <div class="flex items-center justify-between mb-1">
                                <span
                                    class="font-medium text-gray-800">{{ $paiement->reference_paiement ?? 'N/A' }}</span>
                                <span
                                    class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statutPaiementClasses[$paiement->statut_paiement] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $paiement->statut_libelle ?? 'Inconnu' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">{{ $paiement->banque?->nom_banque ?? 'N/A' }}</span>
                                <span
                                    class="font-semibold text-green-600">{{ number_format($paiement->montant_net_paye_paiement, 0, ',', ' ') }}
                                    FCFA</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">Aucun paiement pour cette période</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Appels d'Offres Récents -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div
                class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-bullhorn text-purple-500 mr-2"></i>
                    Appels d'Offres Récents
                </h2>
                @can('appels_offres.read')
                    <a href="{{ route('appels-offres.index') }}" class="text-sm text-purple-600 hover:text-purple-800">
                        Voir tout <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                @endcan
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Numéro</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Objet</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Montant</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Lots</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($appelsOffresRecents as $ao)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a @can('appels_offres.view-details') href="{{ route('appels-offres.show', $ao->id_appel_offre) }}" @endcan
                                        class="font-medium text-purple-600 hover:text-purple-800">
                                        {{ $ao->numero_appel_offre }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">
                                        {{ $ao->typeAppelOffre?->code_type_appel_offre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-800 truncate max-w-xs"
                                        title="{{ $ao->objet_critere_appel_offre }}">
                                        {{ \Str::limit($ao->objet_critere_appel_offre, 50) }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span
                                        class="font-semibold text-gray-800">{{ number_format($ao->montant_global_appel_offre, 0, ',', ' ') }}</span>
                                    <span class="text-xs text-gray-500">FCFA</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                        {{ $ao->lots->count() }} lot(s)
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2 flex-wrap">
                                        @if ($ao->statut_evaluation_critere_appel_offre)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i> Actif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                <i class="fas fa-times-circle mr-1"></i> Inactif
                                            </span>
                                        @endif

                                        @if ($ao->isEtatEnAttente())
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                En attente
                                            </span>
                                        @elseif($ao->isEtatEnCours())
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                En cours
                                            </span>
                                        @elseif($ao->isEtatTermine() && $ao->peutEtreCloture())
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                Terminé
                                            </span>
                                        @elseif($ao->isEtatCloture())
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                Cloturé
                                            </span>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    Aucun appel d'offres pour cette période
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Liste des Lots en Cours (Non Terminés et Non Attribués) -->
        @if ($lotsEnCours->count() > 0)
            <div class="mt-6 bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-tasks text-blue-500 mr-2"></i>
                        Lots en Cours
                        <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                            {{ $lotsEnCours->count() }}
                        </span>
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Lots non attribués et lots attribués non terminés</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-hashtag mr-1"></i>N° Lot
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    N° Attribution
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prestataire
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-coins mr-1"></i>Montant Lot
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-check-circle mr-1"></i>Déjà Payé
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-exclamation-circle mr-1"></i>Reste à Payer
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-clock mr-1"></i>Délai Restant
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-chart-line mr-1"></i>Avancement
                                </th>
                                @can('lots.view-details')
                                    <th scope="col"
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-cog mr-1"></i>Actions
                                    </th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($lotsEnCours as $lot)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <!-- N° Lot -->
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-cube text-blue-600 text-sm"></i>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $lot['numero_lot'] }}
                                                </div>
                                                @if ($lot['libelle_lot'])
                                                    <div class="text-xs text-gray-500">
                                                        {{ Str::limit($lot['libelle_lot'], 30) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Statut -->
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if ($lot['est_attribue'])
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>Attribué
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                <i class="fas fa-hourglass-half mr-1"></i>Non attribué
                                            </span>
                                        @endif
                                    </td>

                                    <!-- N° Attribution -->
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $lot['numero_attribution'] }}
                                        </div>
                                    </td>

                                    <!-- Prestataire -->
                                    <td class="px-4 py-3">
                                        @if ($lot['est_attribue'])
                                            <div class="text-sm">
                                                <div class="font-medium text-gray-900">
                                                    {{ Str::limit($lot['raison_sociale_prestataire'], 25) }}</div>
                                                <div class="text-xs text-gray-500">{{ $lot['numero_prestataire'] }}</div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 italic">-</span>
                                        @endif
                                    </td>

                                    @php
                                            $facture = $lot['attribution']->proforma->facture;
                                            $montant_net_paye_paiement = $facture
                                                ? $facture->paiementsValides->sum('montant_net_paye_paiement')
                                                : 0;
                                            $montant_reste_paiement = $facture ? $facture->montant_facture - $montant_net_paye_paiement : 0;
                                        @endphp

                                    <!-- Montant Lot -->
                                    <td class="px-4 py-3 whitespace-nowrap text-right">
                                        @if ($lot['est_attribue'])
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ number_format($facture->montant_facture, 0, ',', ' ') }}
                                            </div>
                                            <div class="text-xs text-gray-500">FCFA</div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>

                                    {{-- COMMENTAIRES --}}
                                    <!-- Déjà Payé -->
                                    <td class="px-4 py-3 whitespace-nowrap text-right">

                                        @if ($lot['est_attribue'])
                                            <div class="text-sm font-medium text-green-600">
                                                {{ number_format($montant_net_paye_paiement, 0, ',', ' ') }}
                                            </div>
                                            <div class="text-xs text-gray-500">FCFA</div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>

                                    <!-- Reste à Payer -->
                                    <td class="px-4 py-3 whitespace-nowrap text-right">
                                        @if ($lot['est_attribue'])
                                            <div
                                                class="text-sm font-medium {{ $lot['reste_a_payer'] > 0 ? 'text-orange-600' : 'text-gray-400' }}">
                                                {{ number_format($montant_reste_paiement, 0, ',', ' ') }}
                                            </div>
                                            <div class="text-xs text-gray-500">FCFA</div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>

                                    <!-- Délai Restant -->
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        @if ($lot['est_attribue'] && $lot['delai_restant'])
                                            @php
                                                $dateEffective = $lot['date_effective_fin'];
                                                $delaiJours = $lot['delai_jours'];
                                                $estTermine = $dateEffective !== null;
                                            @endphp

                                            @if ($estTermine)
                                                {{-- Travaux terminés --}}
                                                @if ($delaiJours > 0)
                                                    {{-- Terminé en retard --}}
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $lot['delai_restant'] }}
                                                    </span>
                                                @else
                                                    {{-- Terminé dans les délais ou en avance --}}
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>{{ $lot['delai_restant'] }}
                                                    </span>
                                                @endif
                                            @else
                                                {{-- Travaux en cours --}}
                                                @if ($delaiJours < 0)
                                                    {{-- En retard --}}
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $lot['delai_restant'] }}
                                                    </span>
                                                @elseif ($delaiJours >= 0 && $delaiJours <= 7)
                                                    {{-- Urgent (7 jours ou moins) --}}
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <i class="fas fa-clock mr-1"></i>{{ $lot['delai_restant'] }}
                                                    </span>
                                                @else
                                                    {{-- Dans les délais (plus de 7 jours) --}}
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <i class="fas fa-calendar-check mr-1"></i>{{ $lot['delai_restant'] }}
                                                    </span>
                                                @endif
                                            @endif
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>

                                    <!-- Avancement -->
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if ($lot['est_attribue'])
                                            <div class="flex items-center justify-center">
                                                <div class="w-full max-w-[100px]">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <span
                                                            class="text-xs font-medium text-gray-700">{{ number_format($lot['avancement'], 1) }}%</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                                        <div class="h-2 rounded-full transition-all progress-animated
                                                        @if ($lot['avancement'] >= 75) bg-green-500
                                                        @elseif($lot['avancement'] >= 50) bg-blue-500
                                                        @elseif($lot['avancement'] >= 25) bg-yellow-500
                                                        @else bg-orange-500 @endif"
                                                            style="width: {{ $lot['avancement'] }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>

                                    @can('lots.view-details')
                                        <!-- Actions -->
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <a href="{{ route('lots-appels-offres.show', [$lot['appel_offre_id'], $lot['id_lot']]) }}"
                                                class="inline-flex items-center px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-colors shadow-sm">
                                                <i class="fas fa-eye mr-1"></i>
                                                Voir détails
                                            </a>
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Footer avec légende -->
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-600">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-orange-100 border border-orange-300 rounded-full mr-2"></span>
                            <span>Non attribué</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-100 border border-green-300 rounded-full mr-2"></span>
                            <span>Attribué</span>
                        </div>
                        <div class="flex items-center ml-auto">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            <span>Limité aux 10 premiers lots</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Toggle période personnalisée
        function togglePeriodePersonnalisee() {
            const panel = document.getElementById('periodePersonnalisee');
            panel.classList.toggle('hidden');
        }

        // Couleurs personnalisées
        const colors = {
            purple: ['#8b5cf6', '#a78bfa', '#c4b5fd', '#ddd6fe', '#ede9fe'],
            blue: ['#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe', '#dbeafe'],
            green: ['#10b981', '#34d399', '#6ee7b7', '#a7f3d0', '#d1fae5'],
            orange: ['#f97316', '#fb923c', '#fdba74', '#fed7aa', '#ffedd5'],
            red: ['#ef4444', '#f87171', '#fca5a5', '#fecaca', '#fee2e2'],
            teal: ['#14b8a6', '#2dd4bf', '#5eead4', '#99f6e4', '#ccfbf1'],
            indigo: ['#6366f1', '#818cf8', '#a5b4fc', '#c7d2fe', '#e0e7ff'],
        };

        // Données PHP vers JS
        const appelsParType = @json($appelsParType);
        const lotsParPrestataire = @json($lotsParPrestataire);
        const evolutionAO = @json($evolutionAppelsOffres);
        const facturesParStatut = @json($facturesParStatut);
        const paiementsParStatut = @json($paiementsParStatut);
        const tauxAttribution = @json($tauxAttributionLots);

        // ================================================================
        // GRAPHIQUE CAMEMBERT - Appels d'offres par type (Nombre)
        // ================================================================
        if (appelsParType.length > 0) {
            new Chart(document.getElementById('chartAppelsParTypeNombre'), {
                type: 'doughnut',
                data: {
                    labels: appelsParType.map(item => item.label),
                    datasets: [{
                        data: appelsParType.map(item => item.nombre),
                        backgroundColor: colors.purple,
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        },
                        title: {
                            display: true,
                            text: 'Par Nombre',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                }
            });

            // GRAPHIQUE CAMEMBERT - Appels d'offres par type (Montant)
            new Chart(document.getElementById('chartAppelsParTypeMontant'), {
                type: 'doughnut',
                data: {
                    labels: appelsParType.map(item => item.label),
                    datasets: [{
                        data: appelsParType.map(item => item.montant),
                        backgroundColor: colors.indigo,
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        },
                        title: {
                            display: true,
                            text: 'Par Montant (FCFA)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + new Intl.NumberFormat('fr-FR').format(context
                                        .raw) + ' FCFA';
                                }
                            }
                        }
                    }
                }
            });
        }

        // ================================================================
        // GRAPHIQUE HISTOGRAMME - Lots par prestataire
        // ================================================================
        if (lotsParPrestataire.length > 0) {
            new Chart(document.getElementById('chartLotsParPrestataire'), {
                type: 'bar',
                data: {
                    labels: lotsParPrestataire.map(item => item.label),
                    datasets: [{
                            label: 'Nombre de lots',
                            data: lotsParPrestataire.map(item => item.nombre_lots),
                            backgroundColor: colors.blue[0],
                            borderRadius: 6,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Montant (FCFA)',
                            data: lotsParPrestataire.map(item => item.montant),
                            backgroundColor: colors.green[0],
                            borderRadius: 6,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    return lotsParPrestataire[index].label_complet;
                                },
                                label: function(context) {
                                    if (context.datasetIndex === 1) {
                                        return context.dataset.label + ': ' + new Intl.NumberFormat('fr-FR')
                                            .format(context.raw) + ' FCFA';
                                    }
                                    return context.dataset.label + ': ' + context.raw;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Nombre de lots'
                            },
                            beginAtZero: true,
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Montant (FCFA)'
                            },
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            },
                        }
                    }
                }
            });
        }

        // ================================================================
        // GRAPHIQUE LIGNE - Évolution des AO
        // ================================================================
        if (evolutionAO.length > 0) {
            const moisLabels = evolutionAO.map(item => {
                const [annee, mois] = item.mois.split('-');
                const date = new Date(annee, mois - 1);
                return date.toLocaleDateString('fr-FR', {
                    month: 'short',
                    year: '2-digit'
                });
            });

            new Chart(document.getElementById('chartEvolutionAO'), {
                type: 'line',
                data: {
                    labels: moisLabels,
                    datasets: [{
                            label: 'Nombre d\'AO',
                            data: evolutionAO.map(item => item.nombre),
                            borderColor: colors.purple[0],
                            backgroundColor: colors.purple[0] + '20',
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Montant (FCFA)',
                            data: evolutionAO.map(item => item.montant),
                            borderColor: colors.green[0],
                            backgroundColor: colors.green[0] + '20',
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.datasetIndex === 1) {
                                        return context.dataset.label + ': ' + new Intl.NumberFormat('fr-FR')
                                            .format(context.raw) + ' FCFA';
                                    }
                                    return context.dataset.label + ': ' + context.raw;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Nombre'
                            },
                            beginAtZero: true,
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Montant (FCFA)'
                            },
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            },
                        }
                    }
                }
            });
        }

        // ================================================================
        // GRAPHIQUE DOUGHNUT - Taux d'attribution
        // ================================================================
        if (tauxAttribution.total > 0) {
            new Chart(document.getElementById('chartTauxAttribution'), {
                type: 'doughnut',
                data: {
                    labels: ['Attribués', 'Non attribués'],
                    datasets: [{
                        data: [tauxAttribution.attribues, tauxAttribution.non_attribues],
                        backgroundColor: [colors.teal[0], colors.red[2]],
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        }
                    }
                }
            });
        }

        // ================================================================
        // GRAPHIQUE BARRES - Factures par statut
        // ================================================================
        const statutsFacture = {
            'en_attente': {
                label: 'En attente',
                color: colors.orange[1]
            },
            'validee': {
                label: 'Validée',
                color: colors.blue[1]
            },
            'payee': {
                label: 'Payée',
                color: colors.green[1]
            },
            'partiellement_payee': {
                label: 'Partielle',
                color: colors.orange[0]
            },
            'rejetee': {
                label: 'Rejetée',
                color: colors.red[1]
            },
            'annulee': {
                label: 'Annulée',
                color: colors.red[2]
            },
        };

        if (Object.keys(facturesParStatut).length > 0) {
            const facturesLabels = Object.keys(facturesParStatut).map(key => statutsFacture[key]?.label || key);
            const facturesData = Object.values(facturesParStatut).map(item => item.nombre);
            const facturesColors = Object.keys(facturesParStatut).map(key => statutsFacture[key]?.color || '#ccc');

            new Chart(document.getElementById('chartFacturesStatut'), {
                type: 'bar',
                data: {
                    labels: facturesLabels,
                    datasets: [{
                        label: 'Nombre de factures',
                        data: facturesData,
                        backgroundColor: facturesColors,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // ================================================================
        // GRAPHIQUE DOUGHNUT - Paiements par statut
        // ================================================================
        const statutsPaiement = {
            0: {
                label: 'En attente',
                color: colors.orange[1]
            },
            1: {
                label: 'Validé',
                color: colors.blue[1]
            },
            2: {
                label: 'En traitement',
                color: colors.indigo[1]
            },
            3: {
                label: 'Payé',
                color: colors.green[1]
            },
            4: {
                label: 'Rejeté',
                color: colors.red[1]
            },
        };

        if (paiementsParStatut.length > 0) {
            new Chart(document.getElementById('chartPaiementsStatut'), {
                type: 'doughnut',
                data: {
                    labels: paiementsParStatut.map(item => item.label),
                    datasets: [{
                        data: paiementsParStatut.map(item => item.nombre),
                        backgroundColor: paiementsParStatut.map(item => statutsPaiement[item.statut]
                            ?.color || '#ccc'),
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const item = paiementsParStatut[context.dataIndex];
                                    return item.label + ': ' + item.nombre + ' (' + new Intl.NumberFormat(
                                        'fr-FR').format(item.montant) + ' FCFA)';
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
