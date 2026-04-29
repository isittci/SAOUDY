@extends('layouts.main')

@section('title', 'Situations Financières - ' . $prestataire->raison_sociale_prestataire)

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
    }
    .situation-card {
        transition: all 0.3s ease;
    }
    .situation-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .score-circle {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: bold;
        color: white;
    }
    .score-excellente { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .score-bonne { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .score-moyenne { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }
    .score-fragile { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }
    .score-critique { background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); }

    .variation-positive {
        color: #10b981;
    }
    .variation-negative {
        color: #ef4444;
    }
</style>
@endpush

@section('breadcrumb')
    <a @can('prestataires.read') href="{{ route('prestataires.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Prestataires</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('prestataires.view-details') href="{{ route('prestataires.show', $prestataire->id_prestataire) }}" @endcan class="text-white/80 hover:text-white transition-colors">{{ Str::limit($prestataire->raison_sociale_prestataire, 30) }}</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Situations Financières</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    @can('prestataires.view-details')
                    <a href="{{ route('prestataires.show', $prestataire->id_prestataire) }}"
                       class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    @endcan
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-chart-line text-orange-500 mr-2"></i>
                            Situations Financières
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">{{ $prestataire->raison_sociale_prestataire }}</p>
                    </div>
                </div>
                @can('situations_financieres.manage')
                    <div class="flex items-center gap-2">
                        <a href="{{ route('prestataires.situations-financieres.create', $prestataire->id_prestataire) }}"
                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                            <i class="fas fa-plus mr-2"></i>Ajouter
                        </a>
                    </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages Flash -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Statistiques -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Exercices</span>
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-blue-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            </div>

            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">CA Total</span>
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-coins text-green-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-lg font-bold text-green-600">{{ number_format(floor($stats['ca_total'] ?? 0), 0, ',', ' ') }}</p>
                <p class="text-xs text-gray-400">FCFA</p>
            </div>

            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">CA Moyen</span>
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-bar text-purple-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-lg font-bold text-purple-600">{{ number_format(floor($stats['ca_moyen'] ?? 0), 0, ',', ' ') }}</p>
                <p class="text-xs text-gray-400">FCFA/an</p>
            </div>

            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Résultats +</span>
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-up text-emerald-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['resultat_positif'] }}</p>
            </div>

            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Dernier Exercice</span>
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-history text-orange-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-orange-600">{{ $stats['dernier_exercice'] ?? '-' }}</p>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                <form action="{{ route('prestataires.situations-financieres.index', $prestataire->id_prestataire) }}" method="GET" class="flex flex-wrap items-end gap-4">
                    <!-- Exercice -->
                    <div class="min-w-[150px]">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Exercice</label>
                        <select name="exercice" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                            <option value="">Tous</option>
                            @foreach($exercices as $exercice)
                                <option value="{{ $exercice }}" {{ request('exercice') == $exercice ? 'selected' : '' }}>{{ $exercice }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Résultat -->
                    <div class="min-w-[150px]">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Résultat</label>
                        <select name="resultat" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                            <option value="">Tous</option>
                            <option value="positif" {{ request('resultat') === 'positif' ? 'selected' : '' }}>Positif</option>
                            <option value="negatif" {{ request('resultat') === 'negatif' ? 'selected' : '' }}>Négatif</option>
                        </select>
                    </div>

                    <!-- Boutons -->
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors text-sm">
                            <i class="fas fa-filter mr-1"></i>Filtrer
                        </button>
                        <a href="{{ route('prestataires.situations-financieres.index', $prestataire->id_prestataire) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors text-sm">
                            <i class="fas fa-times mr-1"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des situations financières -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-list text-blue-500 mr-2"></i>
                    Historique des Situations Financières
                    <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">{{ $situations->total() }}</span>
                </h2>
            </div>

            @if($situations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Exercice</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Chiffre d'Affaires</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Résultat Net</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Fonds Propres</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Solvabilité</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Score</th>
                                @canany(['situations_financieres.manage', 'situations_financieres.read'])
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($situations as $situation)
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
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-lg font-bold text-gray-800">{{ $situation->exercice_fiscal_situation_financiere }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-sm font-medium text-gray-800">
                                            {{ number_format(floor($situation->chiffre_affaire_situation_financiere ?? 0), 0, ',', ' ') }}
                                        </span>
                                        <span class="text-xs text-gray-400 ml-1">FCFA</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-sm font-medium {{ ($situation->resultat_net_situation_financiere ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format(floor($situation->resultat_net_situation_financiere ?? 0), 0, ',', ' ') }}
                                        </span>
                                        <span class="text-xs text-gray-400 ml-1">FCFA</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-sm font-medium text-gray-800">
                                            {{ number_format(floor($situation->fonds_propres_situation_financiere ?? 0), 0, ',', ' ') }}
                                        </span>
                                        <span class="text-xs text-gray-400 ml-1">FCFA</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                            {{ $situation->ratio_solvabilite_situation_financiere ?? 0 }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="score-circle {{ $scoreClass }} w-12 h-12 text-sm">
                                                {{ $score }}
                                            </div>
                                            <span class="mt-1 text-xs text-{{ $niveau['classe'] }}-600">{{ $niveau['niveau'] }}</span>
                                        </div>
                                    </td>
                                    @canany(['situations_financieres.manage', 'situations_financieres.read'])
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            @can('situations_financieres.read')
                                            <a href="{{ route('prestataires.situations-financieres.show', [$prestataire->id_prestataire, $situation->id_situation_financiere]) }}"
                                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan

                                            @can('situations_financieres.manage')
                                            <a href="{{ route('prestataires.situations-financieres.edit', [$prestataire->id_prestataire, $situation->id_situation_financiere]) }}"
                                               class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('prestataires.situations-financieres.destroy', [$prestataire->id_prestataire, $situation->id_situation_financiere]) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette situation financière ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                    @endcanany
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($situations->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $situations->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg mb-2">Aucune situation financière enregistrée</p>
                    @can('situations_financieres.manage')
                    <p class="text-gray-400 text-sm mb-4">Commencez par ajouter les données financières de ce prestataire</p>
                    <a href="{{ route('prestataires.situations-financieres.create', $prestataire->id_prestataire) }}"
                       class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Ajouter une situation
                    </a>
                    @endcan
                </div>
            @endif
        </div>

    </main>
@endsection
