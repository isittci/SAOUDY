@extends('layouts.main')
@section('title', 'Critères d\'Évaluation - ' . $lot->numero)
@section('breadcrumb')
    <a href="{{ route('appels-offres.index') }}" class="text-white/80 hover:text-white transition-colors">Appels d'offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('appels-offres.show', $lot->appelOffre->id_appel_offre) }}" class="text-white/80 hover:text-white transition-colors">{{ $lot->appelOffre->numero_appel_offre }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>

    <a href="{{ route('lots-appels-offres.index', [$lot->appelOffre->id_appel_offre]) }}" class="text-white/80 hover:text-white transition-colors">Lots</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('lots-appels-offres.show', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}" class="text-white/80 hover:text-white transition-colors">{{ $lot->numero }}</a>

    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Critères</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('lots.show', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                            <i class="fas fa-clipboard-check text-blue-500"></i>
                            <span>Critères d'Évaluation</span>
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">{{ $lot->numero }} - {{ $lot->libelle }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    @if(!$lot->isAttribue() && !$lot->isRetire())
                        <a href="{{ route('criteres-evaluations.create', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg">
                            <i class="fas fa-plus text-sm"></i>
                            <span class="text-sm font-medium">Nouveau Critère</span>
                        </a>
                    @endif
                </div>
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

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- Total critères -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Critères</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $criteres->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-list-ol text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Somme des notes -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Points Utilisés</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalNotes, 0) }}/100</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-green-600 text-xl"></i>
                    </div>
                </div>
                <!-- Barre de progression -->
                <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all duration-300"
                         style="width: {{ $totalNotes }}%"></div>
                </div>
            </div>

            <!-- Points restants -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Points Restants</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format(100 - $totalNotes, 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-balance-scale text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Statut du lot -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-{{ $lot->isAttribue() ? 'green' : 'purple' }}-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Statut du Lot</p>
                        <p class="text-lg font-bold text-gray-800 mt-1">
                            @if($lot->isAttribue())
                                <span class="text-green-600">Attribué</span>
                            @elseif($lot->isRetire())
                                <span class="text-red-600">Retiré</span>
                            @else
                                <span class="text-purple-600">En cours</span>
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-info-circle text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres et Recherche -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Recherche -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <div class="relative">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Numéro ou libellé..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                <!-- Statut -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select name="statut" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400">
                        <option value="">Tous</option>
                        <option value="1" {{ request('statut') == '1' ? 'selected' : '' }}>Actifs</option>
                        <option value="0" {{ request('statut') == '0' ? 'selected' : '' }}>Inactifs</option>
                    </select>
                </div>

                <!-- Bouton -->
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-all">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Table des critères -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Ordre</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Numéro</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Libellé</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Note (/100)</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">% du Total</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($criteres as $critere)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <!-- Ordre -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full text-sm font-bold text-gray-700">
                                        {{ $critere->ordre_execution_critere_evaluation }}
                                    </span>
                                </td>

                                <!-- Numéro -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $critere->numero_critere_evaluation }}</span>
                                </td>

                                <!-- Libellé -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $critere->libelle_critere_evaluation }}</div>
                                    @if($critere->description_critere_evaluation)
                                        <div class="text-xs text-gray-500 mt-1 line-clamp-1">{{ Str::limit($critere->description_critere_evaluation, 50) }}</div>
                                    @endif
                                </td>

                                <!-- Note -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 text-blue-700">
                                        {{ number_format($critere->note_reference_critere_evaluation, 1) }}
                                    </span>
                                </td>

                                <!-- Pourcentage -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 max-w-[100px]">
                                            <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full"
                                                 style="width: {{ $critere->pourcentage_note }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">{{ number_format($critere->pourcentage_note, 1) }}%</span>
                                    </div>
                                </td>

                                <!-- Statut -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($critere->statut_critere_evaluation == 1)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-times-circle mr-1"></i>Inactif
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('criteres-evaluations.show', [$lot->appelOffre->id_appel_offre, $lot->id_lot, $critere->id_critere_evaluation]) }}"
                                           class="text-blue-600 hover:text-blue-900 transition-colors"
                                           title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if(!$lot->isAttribue() && !$lot->isRetire())
                                            <a href="{{ route('criteres-evaluations.edit', [$lot->appelOffre->id_appel_offre, $lot->id_lot, $critere->id_critere_evaluation]) }}"
                                               class="text-orange-600 hover:text-orange-900 transition-colors"
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <button onclick="confirmDelete('{{ $critere->id_critere_evaluation }}', '{{ $critere->libelle_critere_evaluation }}')"
                                                    class="text-red-600 hover:text-red-900 transition-colors"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-500 text-lg font-medium">Aucun critère d'évaluation</p>
                                        <p class="text-gray-400 text-sm mt-1">Commencez par créer votre premier critère</p>
                                        @if(!$lot->isAttribue() && !$lot->isRetire())
                                            <a href="{{ route('criteres-evaluations.create', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                                                class="mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-all">
                                                <i class="fas fa-plus mr-2"></i>Créer un critère
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($criteres->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $criteres->links() }}
                </div>
            @endif
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
                <p id="critereName" class="text-sm font-semibold text-gray-900 mb-6"></p>

                <div class="flex space-x-3">
                    <button onclick="closeDeleteModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                        Annuler
                    </button>
                    <form id="deleteForm" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete(critereId, critereName) {
                document.getElementById('critereName').textContent = critereName;
                document.getElementById('deleteForm').action =
                    `/appels-offres/{{ $lot->appelOffre->id_appel_offre }}/lots/{{ $lot->id_lot }}/criteres/${critereId}`;
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

            .line-clamp-1 {
                display: -webkit-box;
                -webkit-line-clamp: 1;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>
    @endpush
@endsection
