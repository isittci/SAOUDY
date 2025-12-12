@extends('layouts.main')
@section('title', 'Gestion des Lots')
@section('breadcrumb')
    <a href="{{ route('appels-offres.index') }}" class="text-white/80 hover:text-white transition-colors">Appels d'Offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('appels-offres.show', $appelOffre->id_appel_offre) }}" class="text-white/80 hover:text-white transition-colors">{{$appelOffre->numero_appel_offre}}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Lots</span>
@endsection

@section('content')
    <!-- Filters Bar -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et bouton créer -->
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-boxes text-indigo-500"></i>
                        <span>Gestion des Lots</span>
                    </h1>
                    <button onclick="window.location.href='{{ route('lots.create') }}{{ request('appel_offre_id') ? '?appel_offre_id=' . request('appel_offre_id') : '' }}'"
                        class="md:hidden px-4 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Nouveau</span>
                    </button>
                </div>

                <!-- Filtres et actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Recherche -->
                    <div class="relative flex-1 sm:min-w-[250px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Chercher par numéro, libellé..."
                            value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent hover:border-indigo-300 transition-all" />
                    </div>

                    <!-- Filtre Appel d'offres -->
                    <select id="appelOffreFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent hover:border-indigo-300 transition-all cursor-pointer">
                        <option value="">Tous les appels d'offres</option>
                        @foreach ($appelsOffres as $ao)
                            <option value="{{ $ao->id_appel_offre }}"
                                {{ request('appel_offre_id') == $ao->id_appel_offre ? 'selected' : '' }}>
                                {{ $ao->numero_appel_offre }} - {{ Str::limit($ao->libelle_critere_appel_offre, 15) }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filtre Attribution -->
                    <select id="attributionFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent hover:border-indigo-300 transition-all cursor-pointer">
                        <option value="">Tous</option>
                        <option value="1" {{ request('attribution') == '1' ? 'selected' : '' }}>Attribués</option>
                        <option value="0" {{ request('attribution') == '0' ? 'selected' : '' }}>Non attribués</option>
                    </select>

                    <!-- Filtre Statut -->
                    <select id="statutFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent hover:border-indigo-300 transition-all cursor-pointer">
                        <option value="">Tous les statuts</option>
                        <option value="1" {{ request('statut') == '1' ? 'selected' : '' }}>Actifs</option>
                        <option value="0" {{ request('statut') == '0' ? 'selected' : '' }}>Inactifs</option>
                    </select>

                    <!-- Bouton créer (desktop) -->
                    <button
                        onclick="window.location.href='{{ route('lots.create') }}{{ request('appel_offre_id') ? '?appel_offre_id=' . request('appel_offre_id') : '' }}'"
                        class="hidden md:flex px-6 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-lg transition-all duration-200 items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Créer</span>
                    </button>
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

        <!-- Tableau -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- En-tête du tableau -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Liste des lots (<span id="totalCount">{{ $lots->total() }}</span>)
                    </h2>
                    <div class="flex items-center space-x-2">
                        <button onclick="refreshTable()"
                            class="px-3 py-2 text-gray-600 hover:text-indigo-500 hover:bg-indigo-50 rounded-lg transition-all duration-200">
                            <i class="fas fa-sync-alt text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table responsive -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Numéro</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Libellé</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Appel d'Offres</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Dates Prévues</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Attribution</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Statut</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-gray-200 bg-white">
                        @forelse($lots as $lot)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-indigo-100 text-indigo-700">
                                            {{ $lot->numero }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $lot->libelle }}</div>
                                    @if ($lot->description_critere)
                                        <div class="text-xs text-gray-500 mt-1 line-clamp-1">
                                            {{ Str::limit($lot->description_critere, 60) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $lot->appelOffre->numero_appel_offre }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i
                                            class="fas fa-tag mr-1"></i>{{ $lot->appelOffre->typeAppelOffre->code_type_appel_offre }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($lot->date_debut_prevue && $lot->date_fin_prevue)
                                        <div class="text-xs text-gray-700 space-y-1">
                                            <div><span class="font-medium">Début:</span>
                                                {{ $lot->date_debut_prevue->format('d/m/Y') }}</div>
                                            <div><span class="font-medium">Fin:</span>
                                                {{ $lot->date_fin_prevue->format('d/m/Y') }}</div>
                                            @if ($lot->calculerDuree())
                                                <div class="text-indigo-600 font-semibold">
                                                    <i class="fas fa-calendar-day mr-1"></i>{{ $lot->calculerDuree() }}
                                                    jour(s)
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">Non définies</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if ($lot->attribution_lot)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Attribué
                                        </span>
                                        @if ($lot->attributionActive)
                                            <div class="text-xs text-gray-600 mt-1">
                                                {{ $lot->attributionActive->prestataire->raison_sociale_prestataire ?? 'N/A' }}
                                            </div>
                                        @endif
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i> Non attribué
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if ($lot->statut_lot)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            <i class="fas fa-check-circle mr-1"></i> Actif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            <i class="fas fa-times-circle mr-1"></i> Inactif
                                        </span>
                                    @endif
                                    @if ($lot->isRetire())
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 mt-1">
                                            <i class="fas fa-ban mr-1"></i> Retiré
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Voir détails -->
                                        <button onclick="window.location.href='{{ route('lots-appels-offres.show', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}'"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                            title="Voir détails">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>

                                        <!-- Modifier -->
                                        <button onclick="window.location.href='{{ route('lots-appels-offres.edit', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}'"
                                            class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200"
                                            title="Modifier">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>

                                        <!-- Menu Actions -->
                                        <div class="relative">
                                            <button onclick="toggleMenu('{{ $lot->id_lot }}')"
                                                class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-all duration-200"
                                                title="Plus d'actions">
                                                <i class="fas fa-ellipsis-v text-sm"></i>
                                            </button>
                                            <div id="menu-{{ $lot->id_lot }}"
                                                class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                                <div class="py-1">
                                                    <a href="{{ route('criteres-evaluations.index', [$lot->appel_offre_id, $lot->id_lot]) }}"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fas fa-history mr-2 text-blue-500"></i>
                                                        Critère d'évaluation
                                                    </a>

                                                    @if (!$lot->attribution_lot)
                                                        <button onclick="openAttributionModal('{{ $lot->appel_offre_id }}', '{{ $lot->id_lot }}')"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                            <i class="fas fa-user-check mr-2 text-green-500"></i>
                                                            Attribuer
                                                        </button>
                                                    @endif
                                                    @if ($lot->isAttribue() && !$lot->isRetire())
                                                        <button onclick="openRetraitModal('{{ $lot->id_lot }}')"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                            <i class="fas fa-ban mr-2 text-red-500"></i>
                                                            Retirer
                                                        </button>
                                                    @endif
                                                    <button onclick="viewHistorique('{{ $lot->id_lot }}')"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fas fa-history mr-2 text-blue-500"></i>
                                                        Historique
                                                    </button>
                                                    <button onclick="duplicate('{{ $lot->id_lot }}')"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fas fa-copy mr-2 text-purple-500"></i>
                                                        Dupliquer
                                                    </button>
                                                    @if (!$lot->isAttribue())
                                                        <button
                                                            onclick="confirmDelete('{{ $lot->id_lot }}', '{{ $lot->numero }}')"
                                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                                            <i class="fas fa-trash mr-2"></i>
                                                            Supprimer
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <i class="fas fa-inbox text-gray-300 text-5xl"></i>
                                        <p class="text-gray-500 font-medium">Aucun lot trouvé</p>
                                        <button
                                            onclick="window.location.href='{{ route('lots.create') }}{{ request('appel_offre_id') ? '?appel_offre_id=' . request('appel_offre_id') : '' }}'"
                                            class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition-all duration-200">
                                            Créer le premier lot
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($lots->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $lots->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- Modal Confirmation Suppression -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Confirmer la suppression</h3>
                    <p id="deleteMessage" class="text-sm text-gray-600 mb-6"></p>

                    <div class="flex items-center justify-center space-x-3">
                        <button onclick="closeDeleteModal()"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                            Annuler
                        </button>
                        <button onclick="executeDelete()"
                            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium">
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let deleteLotId = null;

            // Toggle menu
            window.toggleMenu = function(id) {
                const menu = document.getElementById(`menu-${id}`);
                const allMenus = document.querySelectorAll('[id^="menu-"]');

                allMenus.forEach(m => {
                    if (m.id !== `menu-${id}`) {
                        m.classList.add('hidden');
                    }
                });

                menu.classList.toggle('hidden');
            }

            // Fermer les menus en cliquant ailleurs
            document.addEventListener('click', function(e) {
                if (!e.target.closest('[onclick^="toggleMenu"]') && !e.target.closest('[id^="menu-"]')) {
                    document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
                }
            });

            // Dupliquer
            window.duplicate = function(id) {
                if (confirm('Voulez-vous dupliquer ce lot ?')) {
                    fetch(`/lots/${id}/duplicate`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = `/lots/${data.data.id_lot}/edit`;
                            } else {
                                alert(data.message || 'Une erreur est survenue');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue');
                        });
                }
            }

            // Voir historique
            window.viewHistorique = function(id) {
                fetch(`/lots/${id}/historique`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Afficher l'historique dans une alerte simple ou rediriger
                            alert('Historique disponible. Consultez la page de détails pour plus d\'informations.');
                            window.location.href = `/lots/${id}`;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors de la récupération de l\'historique');
                    });
            }

            // Confirmer suppression
            window.confirmDelete = function(id, numero) {
                deleteLotId = id;
                const message = `Êtes-vous sûr de vouloir supprimer le lot "${numero}" ?`;
                document.getElementById('deleteMessage').textContent = message;
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            // Exécuter suppression
            window.executeDelete = function() {
                if (!deleteLotId) return;

                fetch(`/lots/${deleteLotId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Une erreur est survenue');
                            closeDeleteModal();
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue');
                        closeDeleteModal();
                    });
            }

            // Fermer modal suppression
            window.closeDeleteModal = function() {
                document.getElementById('deleteModal').classList.add('hidden');
                deleteLotId = null;
            }

            // Rafraîchir le tableau
            window.refreshTable = function() {
                location.reload();
            }

            // Recherche en temps réel
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applyFilters();
                }, 500);
            });

            // Filtres
            document.getElementById('appelOffreFilter').addEventListener('change', applyFilters);
            document.getElementById('attributionFilter').addEventListener('change', applyFilters);
            document.getElementById('statutFilter').addEventListener('change', applyFilters);

            function applyFilters() {
                const search = document.getElementById('searchInput').value;
                const appelOffre = document.getElementById('appelOffreFilter').value;
                const attribution = document.getElementById('attributionFilter').value;
                const statut = document.getElementById('statutFilter').value;

                const params = new URLSearchParams();
                if (search) params.append('search', search);
                if (appelOffre) params.append('appel_offre_id', appelOffre);
                if (attribution) params.append('attribution', attribution);
                if (statut) params.append('statut', statut);

                window.location.href = `?${params.toString()}`;
            }

            // Fermer modal avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeDeleteModal();
                    document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
                }
            });

            // Placeholder pour modales d'attribution et retrait
            window.openAttributionModal = function(appelOffreId, lotId) {
                // alert('Modal d\'attribution à implémenter. Redirection vers la page de détails...');
                window.location.href = "{{ route('lots-appels-offres.show', [':appel_offre', ':id']) }}".replace(':appelOffreId', appelOffreId).replace(':id', lotId);
            }

            

            window.openRetraitModal = function(id) {
                const motif = prompt('Veuillez indiquer le motif du retrait:');
                if (motif && motif.trim()) {
                    fetch("{{ route('lots.retirer', ':id') }}".replace(':id', id), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                motif_retrait: motif.trim()
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message || 'Une erreur est survenue');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue');
                        });
                }
            }
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
