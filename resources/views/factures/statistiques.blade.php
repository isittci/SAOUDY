@extends('layouts.main')

@section('title', 'Statistiques des Factures')

@section('breadcrumb')
    <a @can('factures.read') href="{{ route('factures.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Factures</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Statistiques</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    @can('factures.read')
                    <a href="{{ route('factures.index') }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    @endcan
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Statistiques des Factures</h1>
                        <p class="text-gray-600 mt-1">Tableau de bord et analyses des factures</p>
                    </div>
                </div>

                <!-- Sélecteur d'année -->
                <form action="{{ route('factures.statistiques') }}" method="GET" class="flex items-center space-x-3">
                    <label for="annee" class="text-sm font-medium text-gray-700">Année :</label>
                    <select name="annee" id="annee" onchange="this.form.submit()"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                            <option value="{{ $i }}" {{ $annee == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @include('partials.alerts')

        <!-- Statistiques globales -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Total</span>
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-invoice text-gray-500"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($statistiques['total']) }}</p>
                <p class="text-xs text-gray-500 mt-1">factures</p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-yellow-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-yellow-600 uppercase font-medium">En attente</span>
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-500"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-yellow-600">{{ number_format($statistiques['en_attente']) }}</p>
                <p class="text-xs text-yellow-500 mt-1">
                    {{ $statistiques['total'] > 0 ? round(($statistiques['en_attente'] / $statistiques['total']) * 100, 1) : 0 }}%
                </p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-blue-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-blue-600 uppercase font-medium">Validées</span>
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-blue-500"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($statistiques['validees']) }}</p>
                <p class="text-xs text-blue-500 mt-1">
                    {{ $statistiques['total'] > 0 ? round(($statistiques['validees'] / $statistiques['total']) * 100, 1) : 0 }}%
                </p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-green-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-green-600 uppercase font-medium">Payées</span>
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-double text-green-500"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-green-600">{{ number_format($statistiques['payees']) }}</p>
                <p class="text-xs text-green-500 mt-1">
                    {{ $statistiques['total'] > 0 ? round(($statistiques['payees'] / $statistiques['total']) * 100, 1) : 0 }}%
                </p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-orange-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-orange-600 uppercase font-medium">Partielles</span>
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-adjust text-orange-500"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-orange-600">{{ number_format($statistiques['partiellement_payees']) }}</p>
                <p class="text-xs text-orange-500 mt-1">
                    {{ $statistiques['total'] > 0 ? round(($statistiques['partiellement_payees'] / $statistiques['total']) * 100, 1) : 0 }}%
                </p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-red-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-red-600 uppercase font-medium">Rejetées</span>
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-500"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-red-600">{{ number_format($statistiques['rejetees']) }}</p>
                <p class="text-xs text-red-500 mt-1">
                    {{ $statistiques['total'] > 0 ? round(($statistiques['rejetees'] / $statistiques['total']) * 100, 1) : 0 }}%
                </p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-300 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-600 uppercase font-medium">Annulées</span>
                    <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-ban text-gray-500"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-600">{{ number_format($statistiques['annulees']) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $statistiques['total'] > 0 ? round(($statistiques['annulees'] / $statistiques['total']) * 100, 1) : 0 }}%
                </p>
            </div>
        </div>

        <!-- Résumé financier -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium uppercase tracking-wide">Montant Total Facturé</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($statistiques['montant_total'], 0, ',', ' ') }}</p>
                        <p class="text-orange-200 text-sm mt-1">FCFA</p>
                    </div>
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Montant Payé</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($statistiques['montant_paye'], 0, ',', ' ') }}</p>
                        <p class="text-green-200 text-sm mt-1">FCFA</p>
                    </div>
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-hand-holding-usd text-3xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-green-100 mb-1">
                        <span>Progression</span>
                        <span>{{ $statistiques['taux_paiement'] }}%</span>
                    </div>
                    <div class="w-full bg-green-400/50 rounded-full h-2">
                        <div class="bg-white rounded-full h-2 transition-all duration-500" style="width: {{ $statistiques['taux_paiement'] }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium uppercase tracking-wide">Reste à Payer</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($statistiques['montant_restant'], 0, ',', ' ') }}</p>
                        <p class="text-red-200 text-sm mt-1">FCFA</p>
                    </div>
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-3xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-red-100 mb-1">
                        <span>Impayé</span>
                        <span>{{ 100 - $statistiques['taux_paiement'] }}%</span>
                    </div>
                    <div class="w-full bg-red-400/50 rounded-full h-2">
                        <div class="bg-white rounded-full h-2 transition-all duration-500" style="width: {{ 100 - $statistiques['taux_paiement'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Graphique évolution mensuelle -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>
                        Évolution Mensuelle {{ $annee }}
                    </h2>
                </div>
                <div class="p-6">
                    @php
                        $moisNoms = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
                        $dataParMois = [];
                        foreach($moisNoms as $index => $nom) {
                            $dataParMois[$index + 1] = ['nom' => $nom, 'nombre' => 0, 'montant' => 0];
                        }
                        foreach($parMois as $item) {
                            $dataParMois[$item->mois]['nombre'] = $item->nombre;
                            $dataParMois[$item->mois]['montant'] = $item->montant_total;
                        }
                        $maxNombre = max(array_column($dataParMois, 'nombre')) ?: 1;
                    @endphp

                    <!-- Graphique en barres simple -->
                    <div class="space-y-3">
                        @foreach($dataParMois as $mois => $data)
                            <div class="flex items-center space-x-3">
                                <span class="w-10 text-xs font-medium text-gray-500">{{ $data['nom'] }}</span>
                                <div class="flex-1 bg-gray-100 rounded-full h-6 relative overflow-hidden">
                                    <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-indigo-500 to-indigo-400 rounded-full transition-all duration-500 flex items-center justify-end pr-2"
                                        style="width: {{ $maxNombre > 0 ? ($data['nombre'] / $maxNombre) * 100 : 0 }}%">
                                        @if($data['nombre'] > 0)
                                            <span class="text-xs font-semibold text-white">{{ $data['nombre'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="w-28 text-xs text-gray-600 text-right">
                                    {{ number_format($data['montant'], 0, ',', ' ') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Légende -->
                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-indigo-500 rounded mr-2"></div>
                            <span>Nombre de factures</span>
                        </div>
                        <span>Montant en FCFA</span>
                    </div>
                </div>
            </div>

            <!-- Répartition par statut (Donut) -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-pie text-purple-500 mr-2"></i>
                        Répartition par Statut
                    </h2>
                </div>
                <div class="p-6">
                    @php
                        $statutData = [
                            ['label' => 'En attente', 'value' => $statistiques['en_attente'], 'color' => '#EAB308', 'bg' => 'bg-yellow-500'],
                            ['label' => 'Validées', 'value' => $statistiques['validees'], 'color' => '#3B82F6', 'bg' => 'bg-blue-500'],
                            ['label' => 'Payées', 'value' => $statistiques['payees'], 'color' => '#22C55E', 'bg' => 'bg-green-500'],
                            ['label' => 'Partielles', 'value' => $statistiques['partiellement_payees'], 'color' => '#F97316', 'bg' => 'bg-orange-500'],
                            ['label' => 'Rejetées', 'value' => $statistiques['rejetees'], 'color' => '#EF4444', 'bg' => 'bg-red-500'],
                            ['label' => 'Annulées', 'value' => $statistiques['annulees'], 'color' => '#6B7280', 'bg' => 'bg-gray-500'],
                        ];
                        $total = array_sum(array_column($statutData, 'value')) ?: 1;
                    @endphp

                    <!-- Représentation visuelle circulaire simplifiée -->
                    <div class="flex items-center justify-center mb-6">
                        <div class="relative w-48 h-48">
                            <!-- Cercle de fond -->
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                @php
                                    $offset = 0;
                                @endphp
                                @foreach($statutData as $item)
                                    @if($item['value'] > 0)
                                        @php
                                            $percentage = ($item['value'] / $total) * 100;
                                            $dashArray = $percentage * 2.51327; // circumference = 2 * PI * 40
                                            $dashOffset = $offset * 2.51327;
                                        @endphp
                                        <circle
                                            cx="50" cy="50" r="40"
                                            fill="none"
                                            stroke="{{ $item['color'] }}"
                                            stroke-width="20"
                                            stroke-dasharray="{{ $dashArray }} 251.327"
                                            stroke-dashoffset="-{{ $dashOffset }}"
                                            class="transition-all duration-500"
                                        />
                                        @php
                                            $offset += $percentage;
                                        @endphp
                                    @endif
                                @endforeach
                            </svg>
                            <!-- Centre -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-gray-800">{{ $statistiques['total'] }}</p>
                                    <p class="text-xs text-gray-500">Total</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Légende -->
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($statutData as $item)
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full {{ $item['bg'] }} mr-2"></div>
                                    <span class="text-sm text-gray-700">{{ $item['label'] }}</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ $item['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Top Proformas -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden lg:col-span-2">
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-trophy text-emerald-500 mr-2"></i>
                        Top 10 Proformas par Montant Facturé
                    </h2>
                </div>

                @if($topProformas->isEmpty())
                    <div class="p-8 text-center">
                        <i class="fas fa-chart-line text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Aucune donnée disponible</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Rang</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Proforma</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Montant Facturé</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Part</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($topProformas as $index => $item)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($index === 0)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-800 font-bold">
                                                    <i class="fas fa-trophy"></i>
                                                </span>
                                            @elseif($index === 1)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-700 font-bold">
                                                    2
                                                </span>
                                            @elseif($index === 2)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-800 font-bold">
                                                    3
                                                </span>
                                            @else
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-600 font-medium">
                                                    {{ $index + 1 }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-file-alt text-indigo-600"></i>
                                                </div>
                                                <span class="font-medium text-gray-900">
                                                    {{ $item->proforma->numero_proforma ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-lg font-bold text-gray-900">
                                                {{ number_format($item->total_facture, 0, ',', ' ') }}
                                            </span>
                                            <span class="text-sm text-gray-500 ml-1">FCFA</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @php
                                                $pourcentage = $statistiques['montant_total'] > 0
                                                    ? round(($item->total_facture / $statistiques['montant_total']) * 100, 1)
                                                    : 0;
                                            @endphp
                                            <div class="flex items-center justify-center">
                                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min($pourcentage * 2, 100) }}%"></div>
                                                </div>
                                                <span class="text-sm font-medium text-gray-600">{{ $pourcentage }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Indicateurs de performance -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm text-gray-500">Taux de validation</span>
                    <i class="fas fa-percentage text-blue-400"></i>
                </div>
                @php
                    $tauxValidation = $statistiques['total'] > 0
                        ? round((($statistiques['validees'] + $statistiques['payees'] + $statistiques['partiellement_payees']) / $statistiques['total']) * 100, 1)
                        : 0;
                @endphp
                <p class="text-3xl font-bold text-blue-600">{{ $tauxValidation }}%</p>
                <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $tauxValidation }}%"></div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm text-gray-500">Taux de paiement complet</span>
                    <i class="fas fa-check-double text-green-400"></i>
                </div>
                @php
                    $tauxPaiementComplet = $statistiques['total'] > 0
                        ? round(($statistiques['payees'] / $statistiques['total']) * 100, 1)
                        : 0;
                @endphp
                <p class="text-3xl font-bold text-green-600">{{ $tauxPaiementComplet }}%</p>
                <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $tauxPaiementComplet }}%"></div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm text-gray-500">Taux de rejet</span>
                    <i class="fas fa-times-circle text-red-400"></i>
                </div>
                @php
                    $tauxRejet = $statistiques['total'] > 0
                        ? round(($statistiques['rejetees'] / $statistiques['total']) * 100, 1)
                        : 0;
                @endphp
                <p class="text-3xl font-bold text-red-600">{{ $tauxRejet }}%</p>
                <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-red-500 h-2 rounded-full" style="width: {{ $tauxRejet }}%"></div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm text-gray-500">Montant moyen</span>
                    <i class="fas fa-calculator text-orange-400"></i>
                </div>
                @php
                    $montantMoyen = $statistiques['total'] > 0
                        ? $statistiques['montant_total'] / $statistiques['total']
                        : 0;
                @endphp
                <p class="text-2xl font-bold text-orange-600">{{ number_format($montantMoyen, 0, ',', ' ') }}</p>
                <p class="text-xs text-gray-500 mt-1">FCFA / facture</p>
            </div>
        </div>
    </main>
@endsection


@push('scripts')
<script>
    // Animation des barres de progression au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const progressBars = document.querySelectorAll('[style*="width"]');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        });
    });
</script>
@endpush
