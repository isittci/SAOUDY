@extends('layouts.main')
@section('title', 'Détails Critère - ' . $critere->numero_critere_evaluation)
@section('breadcrumb')
    <a @can('appels_offres.read') href="{{ route('appels-offres.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Appels d'offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('appels_offres.view-details') href="{{ route('appels-offres.show', $critere->lot->appelOffre->id_appel_offre) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">{{ \Illuminate\Support\Str::limit($critere->lot->appelOffre->libelle_critere_appel_offre, 15) }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('lots.read') href="{{ route('lots-appels-offres.index', [$critere->lot->appelOffre->id_appel_offre]) }}" @endcan
        class="text-white/80 hover:text-white transition-colors"
        title="Liste de lots - {{ $critere->lot->appelOffre->libelle_critere_appel_offre }}">Lots</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('lots.view-details') href="{{ route('lots-appels-offres.show', [$critere->lot->appelOffre->id_appel_offre, $critere->lot->id_lot]) }}" @endcan
        class="text-white/80 hover:text-white transition-colors"
        title="{{ $critere->lot->libelle }}">{{ \Illuminate\Support\Str::limit($critere->lot->libelle, 15) }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('criteres_evaluations.read') href="{{ route('criteres-evaluations.index', [$critere->lot->appelOffre->id_appel_offre, $critere->lot->id_lot]) }}" @endcan
        class="text-white/80 hover:text-white transition-colors"
        title="Liste des critères d'évaluation - {{ $critere->lot->libelle }}">Critères</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span
        class="text-white font-medium">{{ \Illuminate\Support\Str::limit($critere->libelle_critere_evaluation, 25) }}</span>
@endsection


@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    @can('criteres_evaluations.read')
                        <a href="{{ route('criteres-evaluations.index', [$critere->lot->appelOffre->id_appel_offre, $critere->lot->id_lot]) }}"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan

                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                            <i class="fas fa-clipboard-check text-blue-500"></i>
                            <span>Détails du Critère</span>
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">{{ $critere->numero_critere_evaluation }} -
                            {{ $critere->libelle_critere_evaluation }}</p>
                    </div>
                </div>

                @can('criteres_evaluations.update')
                    <div class="flex items-center space-x-2">
                        @if (!$critere->lot->isAttribue() && !$critere->lot->isRetire())
                            <a href="{{ route('criteres-evaluations.edit', [$critere->lot->appelOffre->id_appel_offre, $critere->lot->id_lot, $critere->id_critere_evaluation]) }}"
                                class="px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg">
                                <i class="fas fa-edit text-sm"></i>
                                <span class="text-sm font-medium">Modifier</span>
                            </a>
                        @endif
                    </div>
                @endcan
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

        <div class="max-w-6xl mx-auto">

            <!-- Statistiques Rapides -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Note du critère -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Note du Critère</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1">
                                {{ number_format($critere->note_reference_critere_evaluation, 1) }}</p>
                            <p class="text-xs text-gray-500 mt-1">sur 100</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-star text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Pourcentage -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">% du Total Lot</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($pourcentage, 1) }}%</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-percentage text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <!-- Barre de progression -->
                    <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all duration-300"
                            style="width: {{ $pourcentage }}%"></div>
                    </div>
                </div>

                <!-- Ordre d'exécution -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Ordre d'Exécution</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1">
                                {{ $critere->ordre_execution_critere_evaluation }}</p>
                            <p class="text-xs text-gray-500 mt-1">sur {{ $nombreCriteres }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-list-ol text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Statut -->
                <div
                    class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-{{ $critere->statut_critere_evaluation == 1 ? 'green' : 'gray' }}-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Statut</p>
                            <p class="text-xl font-bold text-gray-800 mt-1">
                                @if ($critere->statut_critere_evaluation == 1)
                                    <span class="text-green-600">Actif</span>
                                @else
                                    <span class="text-gray-600">Inactif</span>
                                @endif
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-{{ $critere->statut_critere_evaluation == 1 ? 'green' : 'gray' }}-100 rounded-lg flex items-center justify-center">
                            <i
                                class="fas fa-{{ $critere->statut_critere_evaluation == 1 ? 'check' : 'times' }}-circle text-{{ $critere->statut_critere_evaluation == 1 ? 'green' : 'gray' }}-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations du Lot -->
            <div class="mb-6 bg-white rounded-2xl shadow-lg overflow-hidden border-l-4 border-indigo-500">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-box text-indigo-500 mr-2"></i>
                        Lot associé
                    </h2>
                </div>

                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">
                                    {{ $lot->numero }}
                                </span>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-700">
                                    {{ $lot->appelOffre->typeAppelOffre->code_type_appel_offre }}
                                </span>
                                @if ($lot->isAttribue())
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">
                                        <i class="fas fa-check-circle mr-1"></i>Attribué
                                    </span>
                                @elseif($lot->isRetire())
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-700">
                                        <i class="fas fa-times-circle mr-1"></i>Retiré
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                {{ $lot->libelle }}
                            </h3>
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <span>
                                    <i class="fas fa-star mr-1"></i>
                                    Total notes du lot: <strong>{{ number_format($totalNotes, 0) }}/100</strong>
                                </span>
                                <span>
                                    <i class="fas fa-list-ol mr-1"></i>
                                    Nombre de critères: <strong>{{ $nombreCriteres }}</strong>
                                </span>
                            </div>
                        </div>
                        @can('lots.view-details')
                            <a href="{{ route('lots-appels-offres.show', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                                target="_blank" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                title="Voir le lot">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Détails du Critère -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Informations Détaillées
                    </h2>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Numéro -->
                        <div class="border-b border-gray-200 pb-4">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Numéro</dt>
                            <dd class="text-base font-semibold text-gray-900">{{ $critere->numero_critere_evaluation }}
                            </dd>
                        </div>

                        <!-- Ordre d'exécution -->
                        <div class="border-b border-gray-200 pb-4">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Ordre d'exécution</dt>
                            <dd class="text-base font-semibold text-gray-900">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-700">
                                    {{ $critere->ordre_execution_critere_evaluation }}
                                </span>
                            </dd>
                        </div>

                        <!-- Libellé -->
                        <div class="md:col-span-2 border-b border-gray-200 pb-4">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Libellé</dt>
                            <dd class="text-base font-semibold text-gray-900">{{ $critere->libelle_critere_evaluation }}
                            </dd>
                        </div>

                        <!-- Description -->
                        @if ($critere->description_critere_evaluation)
                            <div class="md:col-span-2 border-b border-gray-200 pb-4">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Description</dt>
                                <dd class="text-sm text-gray-700 whitespace-pre-line bg-gray-50 p-4 rounded-lg">
                                    {{ $critere->description_critere_evaluation }}</dd>
                            </div>
                        @endif

                        <!-- Note de référence -->
                        <div class="border-b border-gray-200 pb-4">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Note de référence</dt>
                            <dd class="text-base font-semibold text-gray-900">
                                <span class="inline-flex items-center px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-lg font-bold">
                                    {{ number_format($critere->note_reference_critere_evaluation, 2) }} / 100
                                </span>
                            </dd>
                        </div>

                        <!-- Pourcentage du total -->
                        <div class="border-b border-gray-200 pb-4">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Pourcentage du total lot</dt>
                            <dd class="text-base font-semibold text-gray-900">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-1 bg-gray-200 rounded-full h-3 max-w-xs">
                                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full"
                                            style="width: {{ $pourcentage }}%"></div>
                                    </div>
                                    <span class="text-lg font-bold text-green-600">{{ number_format($pourcentage, 2) }}%</span>
                                </div>
                            </dd>
                        </div>

                        <!-- Statut -->
                        <div class="border-b border-gray-200 pb-4">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Statut</dt>
                            <dd class="text-base font-semibold text-gray-900">
                                @if ($critere->statut_critere_evaluation == 1)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-2"></i>Actif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-times-circle mr-2"></i>Inactif
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Métadonnées -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-history text-gray-500 mr-2"></i>
                        Informations de Suivi
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Création -->
                        <div class="flex items-center space-x-4 p-4 bg-blue-50 rounded-lg">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-plus-circle text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Créé le</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $critere->created_at->format('d/m/Y à H:i') }}</p>
                                <p class="text-xs text-gray-600">par {{ $critere->creator->nom_complet ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Dernière modification -->
                        @if ($critere->updated_at && $critere->updated_at != $critere->created_at)
                            <div class="flex items-center space-x-4 p-4 bg-orange-50 rounded-lg">
                                <div
                                    class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-edit text-orange-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Modifié le</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $critere->updated_at->format('d/m/Y à H:i') }}</p>
                                    <p class="text-xs text-gray-600">par {{ $critere->updater->nom_complet ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @canany(['criteres_evaluations.update', 'criteres_evaluations.duplicate', 'criteres_evaluations.delete'])
                <!-- Actions -->
                @if (!$lot->isAttribue() && !$lot->isRetire())
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-cog text-red-500 mr-2"></i>
                                Actions
                            </h2>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @can('criteres_evaluations.update')
                                    <!-- Modifier -->
                                    <a href="{{ route('criteres-evaluations.edit', [$lot->appelOffre->id_appel_offre, $lot->id_lot, $critere->id_critere_evaluation]) }}"
                                        class="flex items-center justify-center px-4 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all shadow-md hover:shadow-lg">
                                        <i class="fas fa-edit mr-2"></i>
                                        Modifier
                                    </a>
                                @endcan

                                @can('criteres_evaluations.create')
                                    
                                        <a href="{{ route('criteres-evaluations.create', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"

                                            class="w-full flex items-center justify-center px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-all shadow-md hover:shadow-lg">
                                            <i class="fas fa-plus text-sm mr-2"></i>
                                            Créer nouveau
                                        </a>
                                @endcan

                                @can('criteres_evaluations.delete')
                                    <!-- Supprimer -->
                                    <button onclick="confirmDelete()"
                                        class="flex items-center justify-center px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all shadow-md hover:shadow-lg">
                                        <i class="fas fa-trash mr-2"></i>
                                        Supprimer
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endif
            @endcanany
        </div>
    </main>

    <!-- Modal de confirmation de suppression -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Confirmer la suppression</h3>
                <p class="text-gray-600 mb-1">Êtes-vous sûr de vouloir supprimer ce critère ?</p>
                <p class="text-sm font-semibold text-gray-900 mb-6">{{ $critere->libelle_critere_evaluation }}</p>

                <div class="flex space-x-3">
                    <button onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                        Annuler
                    </button>
                    @can('criteres_evaluations.delete')
                        <form id="deleteForm" method="POST"
                            action="{{ route('criteres-evaluations.destroy', [$lot->appelOffre->id_appel_offre, $lot->id_lot, $critere->id_critere_evaluation]) }}"
                            class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all">
                                Supprimer
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    @can('criteres_evaluations.view-details')
        @push('scripts')
            <script>
                function confirmDelete() {
                    document.getElementById('deleteModal').classList.remove('hidden');
                }

                function closeDeleteModal() {
                    document.getElementById('deleteModal').classList.add('hidden');
                }

                // Fermer le modal en cliquant en dehors
                document.getElementById('deleteModal')?.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeDeleteModal();
                    }
                });

                // Fermer avec Escape
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeDeleteModal();
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
