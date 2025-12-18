@extends('layouts.main')

@section('title', 'Évaluations')

@section('breadcrumb')
    <span class="text-white font-medium">Évaluations</span>
@endsection

@section('content')
    <!-- Header avec statistiques -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Évaluations</h1>
                    <p class="text-gray-600 mt-1">Gestion des évaluations des prestataires</p>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mt-4">
                <div class="bg-white rounded-xl p-3 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 uppercase">Total</span>
                        <i class="fas fa-clipboard-list text-gray-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 uppercase">En attente</span>
                        <i class="fas fa-clock text-gray-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-600 mt-1">{{ $stats['en_attente'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-blue-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-blue-600 uppercase">En cours</span>
                        <i class="fas fa-spinner text-blue-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['en_cours'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-green-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-green-600 uppercase">Terminées</span>
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['terminees'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-emerald-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-emerald-600 uppercase">Validées</span>
                        <i class="fas fa-check-double text-emerald-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['validees'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-red-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-red-600 uppercase">Rejetées</span>
                        <i class="fas fa-times-circle text-red-400"></i>
                    </div>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['rejetees'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @include('partials.alerts')

        <!-- Filtres -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-filter text-orange-500 mr-2"></i>
                    Filtres
                </h2>
            </div>
            <div class="p-6">
                <form action="{{ route('evaluations.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                            placeholder="Numéro, prestataire, lot...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400">
                            <option value="">Tous les statuts</option>
                            <option value="0" {{ request('statut') === '0' ? 'selected' : '' }}>En attente</option>
                            <option value="1" {{ request('statut') === '1' ? 'selected' : '' }}>En cours</option>
                            <option value="2" {{ request('statut') === '2' ? 'selected' : '' }}>Terminée</option>
                            <option value="3" {{ request('statut') === '3' ? 'selected' : '' }}>Validée</option>
                            <option value="4" {{ request('statut') === '4' ? 'selected' : '' }}>Rejetée</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all">
                            <i class="fas fa-search mr-2"></i>Filtrer
                        </button>
                        <a href="{{ route('evaluations.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des évaluations -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-clipboard-check text-indigo-500 mr-2"></i>
                    Liste des évaluations
                    <span class="ml-2 px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-full">
                        {{ $evaluations->total() }}
                    </span>
                </h2>
            </div>

            @if($evaluations->isEmpty())
                <div class="p-12 text-center">
                    <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Aucune évaluation trouvée</h3>
                    <p class="text-gray-500">Les évaluations apparaîtront ici une fois créées depuis les attributions.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Numéro</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lot / Prestataire</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Note</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Rang</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($evaluations as $evaluation)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="font-semibold text-gray-900">{{ $evaluation->numero_evaluation }}</span>
                                            @if($evaluation->version > 1)
                                                <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">
                                                    V{{ $evaluation->version }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-900">
                                                Lot {{ $evaluation->attribution->lot->numero ?? 'N/A' }}
                                            </p>
                                            <p class="text-gray-500">
                                                {{ Str::limit($evaluation->attribution->prestataire->raison_sociale_prestataire ?? 'N/A', 30) }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $evaluation->statut_badge_class }}">
                                            <i class="fas fa-{{ $evaluation->statut_icon }} mr-1"></i>
                                            {{ $evaluation->statut_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-lg font-bold {{ $evaluation->pourcentage_final >= 70 ? 'text-green-600' : ($evaluation->pourcentage_final >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ number_format($evaluation->pourcentage_final, 1) }}%
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                {{ $evaluation->resultat_evaluation }}/{{ $evaluation->note_maximale }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($evaluation->rang)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $evaluation->rang === 1 ? 'bg-yellow-100 text-yellow-800' : ($evaluation->rang === 2 ? 'bg-gray-200 text-gray-700' : ($evaluation->rang === 3 ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-600')) }} font-bold">
                                                {{ $evaluation->rang }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $evaluation->date_evaluation ? $evaluation->date_evaluation->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('evaluations.show', $evaluation->id_evaluation) }}"
                                                class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                                title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($evaluation->peutEtreModifiee())
                                                <a href="{{ route('evaluations.edit', $evaluation->id_evaluation) }}"
                                                    class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                                                    title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $evaluations->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </main>
@endsection
