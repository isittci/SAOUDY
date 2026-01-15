@extends('layouts.main')
@section('title', 'Critères d\'Évaluation - ' . $lot->numero)
@section('breadcrumb')
    <a @can('appels_offres.read') href="{{ route('appels-offres.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Appels d'offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('appels_offres.view-details') href="{{ route('appels-offres.show', $lot->appelOffre->id_appel_offre) }}" @endcan class="text-white/80 hover:text-white transition-colors" title="{{ $lot->appelOffre->libelle_critere_appel_offre }}">{{ \Illuminate\Support\Str::limit($lot->appelOffre->libelle_critere_appel_offre, 15) }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>

    <a @can('lots.read') href="{{ route('lots-appels-offres.index', [$lot->appelOffre->id_appel_offre]) }}" @endcan class="text-white/80 hover:text-white transition-colors" title="Liste de lots - {{ $lot->appelOffre->libelle_critere_appel_offre }}">Lots</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('lots.view-details') href="{{ route('lots-appels-offres.show', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}" @endcan class="text-white/80 hover:text-white transition-colors" title="{{ $lot->libelle }}">{{ \Illuminate\Support\Str::limit($lot->libelle, 15) }}</a>

    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium" title="Critères d'évaluation du lot - {{ $lot->numero }} : ">Critères d'évaluation</span>
@endsection





@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    @can('lots.view-details')
                    <a href="{{ route('lots-appels-offres.show', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    @endcan
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                            <i class="fas fa-clipboard-check text-blue-500"></i>
                            <span>Critères d'Évaluation</span>
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">{{ $lot->numero }} - {{ $lot->libelle }}</p>
                    </div>
                </div>

                @can('criteres_evaluations.create')
                <div class="flex items-center space-x-2">
                    @if(!$lot->isAttribue() && !$lot->isRetire() && number_format($totalNotes, 0) < 100)
                        <a href="{{ route('criteres-evaluations.create', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg">
                            <i class="fas fa-plus text-sm"></i>
                            <span class="text-sm font-medium">Nouveau Critère</span>
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

        <!-- Toast de notification pour les actions AJAX -->
        <div id="toast-notification" class="fixed top-4 right-4 z-50 hidden transform transition-all duration-300 translate-x-full">
            <div class="bg-white rounded-lg shadow-xl border-l-4 p-4 min-w-[300px]">
                <div class="flex items-center">
                    <i id="toast-icon" class="text-xl mr-3"></i>
                    <p id="toast-message" class="font-medium"></p>
                </div>
            </div>
        </div>

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

        <!-- Indicateur de mode réordonnement -->
        @if(!$lot->isAttribue() && !$lot->isRetire() && $criteres->count() > 1)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <div class="flex items-center text-blue-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span class="text-sm">
                        <strong>Réordonner les critères :</strong>
                        Glissez-déposez les lignes ou utilisez les flèches <i class="fas fa-arrows-alt-v mx-1"></i> pour modifier l'ordre d'évaluation.
                    </span>
                </div>
            </div>
        @endif

        <!-- Table des critères -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="criteres-table">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <tr>
                            @if(!$lot->isAttribue() && !$lot->isRetire())
                                <th class="px-3 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-16">
                                    <i class="fas fa-grip-vertical text-gray-400"></i>
                                </th>
                            @endif
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Ordre</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Numéro</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Libellé</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Note (/100)</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">% du Total</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Statut</th>
                            @canany(['criteres_evaluations.view-details', 'criteres_evaluations.update', 'criteres_evaluations.delete'])
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody id="criteres-tbody" class="divide-y divide-gray-200">
                        @forelse($criteres as $index => $critere)
                            <tr class="hover:bg-gray-50 transition-colors critere-row {{ !$lot->isAttribue() && !$lot->isRetire() ? 'cursor-move' : '' }}"
                                data-id="{{ $critere->id_critere_evaluation }}"
                                data-ordre="{{ $critere->ordre_execution_critere_evaluation }}"
                                draggable="{{ !$lot->isAttribue() && !$lot->isRetire() ? 'true' : 'false' }}">

                                @if(!$lot->isAttribue() && !$lot->isRetire())
                                    <!-- Colonne Drag Handle et Boutons Flèches -->
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <div class="flex flex-col items-center space-y-1">
                                            <button type="button"
                                                    onclick="moveUp('{{ $critere->id_critere_evaluation }}')"
                                                    class="move-btn p-1 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-all {{ $loop->first ? 'opacity-30 cursor-not-allowed' : '' }}"
                                                    {{ $loop->first ? 'disabled' : '' }}
                                                    title="Monter">
                                                <i class="fas fa-chevron-up text-xs"></i>
                                            </button>
                                            <i class="fas fa-grip-vertical text-gray-300 drag-handle cursor-grab active:cursor-grabbing"></i>
                                            <button type="button"
                                                    onclick="moveDown('{{ $critere->id_critere_evaluation }}')"
                                                    class="move-btn p-1 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-all {{ $loop->last ? 'opacity-30 cursor-not-allowed' : '' }}"
                                                    {{ $loop->last ? 'disabled' : '' }}
                                                    title="Descendre">
                                                <i class="fas fa-chevron-down text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif

                                <!-- Ordre -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="ordre-badge inline-flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full text-sm font-bold text-gray-700">
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

                                @canany(['criteres_evaluations.view-details', 'criteres_evaluations.update', 'criteres_evaluations.delete'])
                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        @can('criteres_evaluations.view-details')
                                        <a href="{{ route('criteres-evaluations.show', [$lot->appelOffre->id_appel_offre, $lot->id_lot, $critere->id_critere_evaluation]) }}"
                                           class="text-blue-600 hover:text-blue-900 transition-colors"
                                           title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan

                                        @canany(['criteres_evaluations.update', 'criteres_evaluations.delete'])
                                        @if(!$lot->isAttribue() && !$lot->isRetire())
                                        @can('criteres_evaluations.update')
                                            <a href="{{ route('criteres-evaluations.edit', [$lot->appelOffre->id_appel_offre, $lot->id_lot, $critere->id_critere_evaluation]) }}"
                                               class="text-orange-600 hover:text-orange-900 transition-colors"
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan

                                            @can('criteres_evaluations.delete')
                                            <button onclick="confirmDelete('{{ $critere->id_critere_evaluation }}', '{{ $critere->libelle_critere_evaluation }}')"
                                                    class="text-red-600 hover:text-red-900 transition-colors"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        @endif
                                        @endcanany
                                    </div>
                                </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ !$lot->isAttribue() && !$lot->isRetire() ? 8 : 7 }}" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-500 text-lg font-medium">Aucun critère d'évaluation</p>
                                        <p class="text-gray-400 text-sm mt-1">Commencez par créer votre premier critère</p>
                                        @can('criteres_evaluations.create')
                                        @if(!$lot->isAttribue() && !$lot->isRetire())
                                            <a href="{{ route('criteres-evaluations.create', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                                                class="mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-all">
                                                <i class="fas fa-plus mr-2"></i>Créer un critère
                                            </a>
                                        @endif
                                        @endcan
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
                    @can('criteres_evaluations.delete')
                    <form id="deleteForm" method="POST" class="flex-1">
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

    @can('criteres_evaluations.read')
    @push('scripts')
        <script>
            // Configuration des URLs
            const BASE_URL = "{{ route('criteres-evaluations.index', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}";
            const CSRF_TOKEN = '{{ csrf_token() }}';
            const CAN_REORDER = {{ (!$lot->isAttribue() && !$lot->isRetire()) ? 'true' : 'false' }};

            // Variables pour le drag and drop
            let draggedRow = null;
            let placeholder = null;

            // ================================
            // FONCTIONS TOAST NOTIFICATION
            // ================================
            function showToast(message, type = 'success') {
                const toast = document.getElementById('toast-notification');
                const toastMessage = document.getElementById('toast-message');
                const toastIcon = document.getElementById('toast-icon');
                const toastContainer = toast.querySelector('div');

                // Configuration selon le type
                const config = {
                    success: {
                        icon: 'fas fa-check-circle text-green-500',
                        border: 'border-green-500',
                        textColor: 'text-green-700'
                    },
                    error: {
                        icon: 'fas fa-exclamation-circle text-red-500',
                        border: 'border-red-500',
                        textColor: 'text-red-700'
                    },
                    info: {
                        icon: 'fas fa-info-circle text-blue-500',
                        border: 'border-blue-500',
                        textColor: 'text-blue-700'
                    }
                };

                const cfg = config[type] || config.info;

                toastIcon.className = cfg.icon;
                toastMessage.textContent = message;
                toastMessage.className = `font-medium ${cfg.textColor}`;
                toastContainer.className = `bg-white rounded-lg shadow-xl border-l-4 p-4 min-w-[300px] ${cfg.border}`;

                // Afficher
                toast.classList.remove('hidden');
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                }, 10);

                // Masquer après 3 secondes
                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                    setTimeout(() => {
                        toast.classList.add('hidden');
                    }, 300);
                }, 3000);
            }

            // ================================
            // FONCTIONS DE RÉORDONNEMENT
            // ================================

            // Réordonner un critère via AJAX
            async function reorderCritere(critereId, newOrder) {
                try {
                    const response = await fetch(`${BASE_URL}/${critereId}/reordonner`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            nouvel_ordre: newOrder
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast('Ordre mis à jour avec succès', 'success');
                        return true;
                    } else {
                        showToast(data.message || 'Erreur lors de la mise à jour', 'error');
                        return false;
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    showToast('Erreur de connexion', 'error');
                    return false;
                }
            }

            // Monter un critère (ordre - 1)
            async function moveUp(critereId) {
                const row = document.querySelector(`tr[data-id="${critereId}"]`);
                const prevRow = row.previousElementSibling;

                if (!prevRow || !prevRow.classList.contains('critere-row')) return;

                const currentOrder = parseInt(row.dataset.ordre);
                const prevOrder = parseInt(prevRow.dataset.ordre);

                // Animation visuelle
                row.classList.add('bg-blue-50');
                prevRow.classList.add('bg-blue-50');

                // Permutation visuelle
                row.parentNode.insertBefore(row, prevRow);

                // Mise à jour via AJAX
                const success = await reorderCritere(critereId, prevOrder);

                if (success) {
                    // Mettre à jour les attributs data
                    row.dataset.ordre = prevOrder;
                    prevRow.dataset.ordre = currentOrder;

                    // Mettre à jour les badges d'ordre affichés
                    updateOrderBadges();
                    updateMoveButtons();
                } else {
                    // Annuler la permutation visuelle
                    prevRow.parentNode.insertBefore(row, prevRow.nextElementSibling);
                }

                // Retirer l'animation
                setTimeout(() => {
                    row.classList.remove('bg-blue-50');
                    prevRow.classList.remove('bg-blue-50');
                }, 500);
            }

            // Descendre un critère (ordre + 1)
            async function moveDown(critereId) {
                const row = document.querySelector(`tr[data-id="${critereId}"]`);
                const nextRow = row.nextElementSibling;

                if (!nextRow || !nextRow.classList.contains('critere-row')) return;

                const currentOrder = parseInt(row.dataset.ordre);
                const nextOrder = parseInt(nextRow.dataset.ordre);

                // Animation visuelle
                row.classList.add('bg-blue-50');
                nextRow.classList.add('bg-blue-50');

                // Permutation visuelle
                nextRow.parentNode.insertBefore(nextRow, row);

                // Mise à jour via AJAX
                const success = await reorderCritere(critereId, nextOrder);

                if (success) {
                    // Mettre à jour les attributs data
                    row.dataset.ordre = nextOrder;
                    nextRow.dataset.ordre = currentOrder;

                    // Mettre à jour les badges d'ordre affichés
                    updateOrderBadges();
                    updateMoveButtons();
                } else {
                    // Annuler la permutation visuelle
                    row.parentNode.insertBefore(row, nextRow);
                }

                // Retirer l'animation
                setTimeout(() => {
                    row.classList.remove('bg-blue-50');
                    nextRow.classList.remove('bg-blue-50');
                }, 500);
            }

            // Mettre à jour les badges d'ordre après réordonnement
            function updateOrderBadges() {
                const rows = document.querySelectorAll('.critere-row');
                rows.forEach((row, index) => {
                    const badge = row.querySelector('.ordre-badge');
                    if (badge) {
                        badge.textContent = index + 1;
                    }
                });
            }

            // Mettre à jour l'état des boutons de déplacement
            function updateMoveButtons() {
                const rows = document.querySelectorAll('.critere-row');
                rows.forEach((row, index) => {
                    const upBtn = row.querySelector('.move-btn:first-child');
                    const downBtn = row.querySelector('.move-btn:last-child');

                    if (upBtn) {
                        if (index === 0) {
                            upBtn.classList.add('opacity-30', 'cursor-not-allowed');
                            upBtn.disabled = true;
                        } else {
                            upBtn.classList.remove('opacity-30', 'cursor-not-allowed');
                            upBtn.disabled = false;
                        }
                    }

                    if (downBtn) {
                        if (index === rows.length - 1) {
                            downBtn.classList.add('opacity-30', 'cursor-not-allowed');
                            downBtn.disabled = true;
                        } else {
                            downBtn.classList.remove('opacity-30', 'cursor-not-allowed');
                            downBtn.disabled = false;
                        }
                    }
                });
            }

            // ================================
            // DRAG AND DROP
            // ================================

            if (CAN_REORDER) {
                const tbody = document.getElementById('criteres-tbody');

                // Créer le placeholder
                placeholder = document.createElement('tr');
                placeholder.className = 'bg-blue-100 border-2 border-dashed border-blue-400';
                placeholder.innerHTML = '<td colspan="8" class="py-4 text-center text-blue-600"><i class="fas fa-arrow-down mr-2"></i>Déposer ici</td>';

                // Événements drag
                tbody.addEventListener('dragstart', function(e) {
                    if (!e.target.classList.contains('critere-row')) return;

                    draggedRow = e.target;
                    draggedRow.classList.add('opacity-50', 'bg-gray-100');

                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', e.target.dataset.id);
                });

                tbody.addEventListener('dragend', function(e) {
                    if (draggedRow) {
                        draggedRow.classList.remove('opacity-50', 'bg-gray-100');
                    }
                    if (placeholder.parentNode) {
                        placeholder.parentNode.removeChild(placeholder);
                    }
                    draggedRow = null;
                });

                tbody.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';

                    const targetRow = e.target.closest('.critere-row');
                    if (targetRow && targetRow !== draggedRow) {
                        const rect = targetRow.getBoundingClientRect();
                        const midY = rect.top + rect.height / 2;

                        if (e.clientY < midY) {
                            targetRow.parentNode.insertBefore(placeholder, targetRow);
                        } else {
                            targetRow.parentNode.insertBefore(placeholder, targetRow.nextElementSibling);
                        }
                    }
                });

                tbody.addEventListener('drop', async function(e) {
                    e.preventDefault();

                    if (!draggedRow || !placeholder.parentNode) return;

                    // Insérer la ligne à la place du placeholder
                    placeholder.parentNode.insertBefore(draggedRow, placeholder);
                    placeholder.parentNode.removeChild(placeholder);

                    // Calculer le nouvel ordre
                    const rows = Array.from(document.querySelectorAll('.critere-row'));
                    const newIndex = rows.indexOf(draggedRow);
                    const newOrder = newIndex + 1;

                    // Sauvegarder via AJAX
                    const success = await reorderCritere(draggedRow.dataset.id, newOrder);

                    if (success) {
                        // Mettre à jour tous les ordres visuellement
                        rows.forEach((row, index) => {
                            row.dataset.ordre = index + 1;
                        });
                        updateOrderBadges();
                        updateMoveButtons();
                    } else {
                        // Recharger la page en cas d'erreur
                        window.location.reload();
                    }
                });
            }

            // ================================
            // MODAL DE SUPPRESSION
            // ================================

            function confirmDelete(critereId, critereName) {
                document.getElementById('critereName').textContent = critereName;
                document.getElementById('deleteForm').action = `${BASE_URL}/${critereId}`;
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

            .line-clamp-1 {
                display: -webkit-box;
                -webkit-line-clamp: 1;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            /* Style pour le drag handle */
            .drag-handle:hover {
                color: #3b82f6;
            }

            /* Animation de transition pour les lignes */
            .critere-row {
                transition: background-color 0.3s ease, transform 0.2s ease;
            }

            .critere-row.dragging {
                transform: scale(1.02);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            /* Style pour le placeholder */
            .drop-placeholder {
                background-color: #dbeafe;
                border: 2px dashed #3b82f6;
            }
        </style>
    @endpush
    @endcan
@endsection
