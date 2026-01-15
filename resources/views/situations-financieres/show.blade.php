@extends('layouts.main')

@section('title', 'Situation Financière ' . $situation->exercice_fiscal_situation_financiere . ' - ' . $prestataire->raison_sociale_prestataire)

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
    }
    .score-circle-large {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .score-excellente { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .score-bonne { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .score-moyenne { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }
    .score-fragile { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }
    .score-critique { background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .info-row:last-child {
        border-bottom: none;
    }

    .variation-positive {
        color: #10b981;
    }
    .variation-negative {
        color: #ef4444;
    }

    .indicator-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #e5e7eb;
    }
</style>
@endpush

@section('breadcrumb')
    <a href="{{ route('prestataires.index') }}" class="text-white/80 hover:text-white transition-colors">Prestataires</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('prestataires.show', $prestataire->id_prestataire) }}" class="text-white/80 hover:text-white transition-colors">{{ Str::limit($prestataire->raison_sociale_prestataire, 20) }}</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('prestataires.situations-financieres.index', $prestataire->id_prestataire) }}" class="text-white/80 hover:text-white transition-colors">Situations Financières</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">{{ $situation->exercice_fiscal_situation_financiere }}</span>
@endsection

@section('content')
    @php
        $niveau = $situation->getNiveau();
        $score = $situation->calculerScore();
        $scoreClass = match(true) {
            $score >= 80 => 'score-excellente',
            $score >= 60 => 'score-bonne',
            $score >= 40 => 'score-moyenne',
            $score >= 20 => 'score-fragile',
            default => 'score-critique',
        };
        $indicateurs = $situation->getIndicateursCles();
    @endphp

    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <a href="{{ route('prestataires.situations-financieres.index', $prestataire->id_prestataire) }}"
                       class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-chart-line text-orange-500 mr-2"></i>
                            Situation Financière {{ $situation->exercice_fiscal_situation_financiere }}
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">{{ $prestataire->raison_sociale_prestataire }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('prestataires.situations-financieres.edit', [$prestataire->id_prestataire, $situation->id_situation_financiere]) }}"
                       class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne gauche - Score et infos -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Score -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-{{ $niveau['classe'] }}-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-heartbeat text-{{ $niveau['classe'] }}-500 mr-2"></i>
                            Santé Financière
                        </h2>
                    </div>
                    <div class="p-6 text-center">
                        <div class="score-circle-large {{ $scoreClass }} mx-auto mb-4">
                            <span class="text-3xl font-bold">{{ $score }}</span>
                            <span class="text-sm opacity-80">/100</span>
                        </div>
                        <p class="text-lg font-semibold text-{{ $niveau['classe'] }}-600 mb-2">
                            <i class="fas fa-{{ $niveau['icon'] }} mr-1"></i>
                            {{ $niveau['niveau'] }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Exercice fiscal {{ $situation->exercice_fiscal_situation_financiere }}
                        </p>
                    </div>
                </div>

                <!-- Comparaison avec l'année précédente -->
                @if($comparaison && $situationPrecedente)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-exchange-alt text-purple-500 mr-2"></i>
                                Évolution vs {{ $situationPrecedente->exercice_fiscal_situation_financiere }}
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach($comparaison as $key => $data)
                                @php
                                    $label = match($key) {
                                        'chiffre_affaires' => 'Chiffre d\'affaires',
                                        'resultat_net' => 'Résultat net',
                                        'fonds_propres' => 'Fonds propres',
                                        default => $key,
                                    };
                                @endphp
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-600">{{ $label }}</span>
                                    @if($data['variation'] !== null)
                                        <span class="font-medium {{ $data['variation'] >= 0 ? 'variation-positive' : 'variation-negative' }}">
                                            <i class="fas fa-{{ $data['variation'] >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                                            {{ number_format(abs($data['variation']), 1) }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Informations -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-gray-500 mr-2"></i>
                            Informations
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="info-row">
                            <span class="text-gray-500 text-sm">Exercice</span>
                            <span class="font-bold text-gray-800">{{ $situation->exercice_fiscal_situation_financiere }}</span>
                        </div>
                        <div class="info-row">
                            <span class="text-gray-500 text-sm">Créé le</span>
                            <span class="font-medium text-gray-800">{{ $situation->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="text-gray-500 text-sm">Modifié le</span>
                            <span class="font-medium text-gray-800">{{ $situation->updated_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne droite - Données financières -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Indicateurs clés -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <!-- Chiffre d'affaires -->
                    <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-500 uppercase font-medium">Chiffre d'Affaires</span>
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-coins text-green-600 text-sm"></i>
                            </div>
                        </div>
                        <p class="text-lg font-bold text-gray-800">{{ number_format($situation->chiffre_affaire_situation_financiere ?? 0, 0, ',', ' ') }}</p>
                        <p class="text-xs text-gray-400">FCFA</p>
                    </div>

                    <!-- Résultat net -->
                    <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-500 uppercase font-medium">Résultat Net</span>
                            <div class="w-8 h-8 {{ ($situation->resultat_net_situation_financiere ?? 0) >= 0 ? 'bg-emerald-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                                <i class="fas {{ ($situation->resultat_net_situation_financiere ?? 0) >= 0 ? 'fa-arrow-up text-emerald-600' : 'fa-arrow-down text-red-600' }} text-sm"></i>
                            </div>
                        </div>
                        <p class="text-lg font-bold {{ ($situation->resultat_net_situation_financiere ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ number_format($situation->resultat_net_situation_financiere ?? 0, 0, ',', ' ') }}
                        </p>
                        <p class="text-xs text-gray-400">FCFA</p>
                    </div>

                    <!-- Fonds propres -->
                    <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-500 uppercase font-medium">Fonds Propres</span>
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-landmark text-purple-600 text-sm"></i>
                            </div>
                        </div>
                        <p class="text-lg font-bold text-gray-800">{{ number_format($situation->fonds_propres_situation_financiere ?? 0, 0, ',', ' ') }}</p>
                        <p class="text-xs text-gray-400">FCFA</p>
                    </div>

                    <!-- Marge nette -->
                    <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-500 uppercase font-medium">Marge Nette</span>
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-percentage text-blue-600 text-sm"></i>
                            </div>
                        </div>
                        <p class="text-lg font-bold text-gray-800">{{ $situation->marge_nette ?? 0 }}%</p>
                    </div>

                    <!-- Ratio solvabilité -->
                    <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-500 uppercase font-medium">Solvabilité</span>
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shield-alt text-orange-600 text-sm"></i>
                            </div>
                        </div>
                        <p class="text-lg font-bold text-gray-800">{{ $situation->ratio_solvabilite_situation_financiere ?? 0 }}%</p>
                    </div>

                    <!-- Ratio liquidité -->
                    <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-500 uppercase font-medium">Liquidité</span>
                            <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tint text-teal-600 text-sm"></i>
                            </div>
                        </div>
                        <p class="text-lg font-bold text-gray-800">{{ $situation->ratio_liquidite_situation_financiere ?? 0 }}</p>
                    </div>
                </div>

                <!-- Bilan -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-teal-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-balance-scale text-teal-500 mr-2"></i>
                            Bilan Comptable
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-8">
                            <!-- Actif -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase">
                                    <i class="fas fa-plus-circle text-green-500 mr-2"></i>Actif
                                </h3>
                                <div class="bg-green-50 rounded-xl p-4 text-center">
                                    <p class="text-2xl font-bold text-green-700">
                                        {{ number_format($situation->total_actif_situation_financiere ?? 0, 0, ',', ' ') }}
                                    </p>
                                    <p class="text-sm text-green-600">FCFA</p>
                                </div>
                            </div>

                            <!-- Passif -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase">
                                    <i class="fas fa-minus-circle text-red-500 mr-2"></i>Passif
                                </h3>
                                <div class="bg-red-50 rounded-xl p-4 text-center">
                                    <p class="text-2xl font-bold text-red-700">
                                        {{ number_format($situation->total_passif_situation_financiere ?? 0, 0, ',', ' ') }}
                                    </p>
                                    <p class="text-sm text-red-600">FCFA</p>
                                </div>
                            </div>
                        </div>

                        <!-- Capacité d'emprunt -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Capacité d'emprunt</p>
                                    <p class="text-xl font-bold text-gray-800">
                                        {{ number_format($situation->capacite_emprunt_situation_financiere ?? 0, 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-hand-holding-usd text-indigo-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ratios calculés -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-calculator text-indigo-500 mr-2"></i>
                            Ratios Calculés
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-gray-50 rounded-xl">
                                <p class="text-2xl font-bold text-gray-800">{{ $situation->roe ?? 0 }}%</p>
                                <p class="text-xs text-gray-500 mt-1">ROE</p>
                                <p class="text-[10px] text-gray-400">Rentabilité capitaux propres</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-xl">
                                <p class="text-2xl font-bold text-gray-800">{{ $situation->roa ?? 0 }}%</p>
                                <p class="text-xs text-gray-500 mt-1">ROA</p>
                                <p class="text-[10px] text-gray-400">Rentabilité des actifs</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-xl">
                                <p class="text-2xl font-bold text-gray-800">{{ $situation->marge_nette ?? 0 }}%</p>
                                <p class="text-xs text-gray-500 mt-1">Marge Nette</p>
                                <p class="text-[10px] text-gray-400">Résultat / CA</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-xl">
                                <p class="text-2xl font-bold text-gray-800">{{ $situation->ratio_endettement ?? 0 }}%</p>
                                <p class="text-xs text-gray-500 mt-1">Endettement</p>
                                <p class="text-[10px] text-gray-400">Dettes / Fonds propres</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observations -->
                @if($situation->observations_situation_financiere)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-comment-alt text-gray-500 mr-2"></i>
                                Observations
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="prose prose-sm max-w-none text-gray-700">
                                {!! nl2br(e($situation->observations_situation_financiere)) !!}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </main>
@endsection
