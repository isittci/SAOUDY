@extends('layouts.main')
@section('title', 'Gestion des Lots')
@section('breadcrumb', 'Lots')

@section('content')
    <!-- Filters Bar -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Titre et bouton créer -->
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                        <span class="p-2 bg-indigo-100 rounded-lg">
                            <i class="fas fa-boxes text-indigo-600"></i>
                        </span>
                        <span>Gestion des Lots</span>
                    </h1>

                </div>

                <!-- Filtres -->
                <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
                    <!-- Recherche -->
                    <div class="relative flex-1 sm:min-w-[280px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Rechercher par numéro, libellé..."
                            value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" />
                    </div>

                    <!-- Filtre Appel d'offres -->
                    <select id="appelOffreFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all cursor-pointer min-w-[200px]">
                        <option value="">Tous les appels d'offres</option>
                        @foreach ($appelsOffres as $ao)
                            <option value="{{ $ao->id_appel_offre }}"
                                {{ request('appel_offre_id') == $ao->id_appel_offre ? 'selected' : '' }}>
                                {{ $ao->numero_appel_offre }} - {{ Str::limit($ao->libelle_critere_appel_offre, 20) }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filtre Attribution -->
                    <select id="attributionFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all cursor-pointer">
                        <option value="">Attribution</option>
                        <option value="1" {{ request('attribution') == '1' ? 'selected' : '' }}>Attribués</option>
                        <option value="0" {{ request('attribution') == '0' ? 'selected' : '' }}>Non attribués</option>
                    </select>

                    <!-- Filtre Statut -->
                    <select id="statutFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all cursor-pointer">
                        <option value="">Statut</option>
                        <option value="1" {{ request('statut') == '1' ? 'selected' : '' }}>Actifs</option>
                        <option value="0" {{ request('statut') == '0' ? 'selected' : '' }}>Inactifs</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6 lg:p-8">
        <!-- Messages de succès/erreur -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3 animate-fadeIn">
                <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-green-800">Succès</h4>
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3 animate-fadeIn">
                <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation text-red-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-red-800">Erreur</h4>
                    <p class="text-red-700 text-sm">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <!-- Card Tableau -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- En-tête du tableau -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <h2 class="text-lg font-semibold text-gray-800">Liste des lots</h2>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                            <span id="totalCount">{{ $lots->total() }}</span> résultat(s)
                        </span>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <!-- Export Excel -->
                        <a href="{{ route('exports.lots-en-cours.excel') }}" title="Exporter en Excel"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all text-sm font-medium">
                            <i class="fas fa-file-excel"></i>
                            <span class="hidden sm:inline">Excel</span>
                        </a>

                        <!-- Export PDF -->
                        <a href="{{ route('exports.lots-en-cours.pdf') }}" title="Exporter en PDF"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all text-sm font-medium">
                            <i class="fas fa-file-pdf"></i>
                            <span class="hidden sm:inline">PDF</span>
                        </a>

                        <!-- Rafraîchir -->
                        <button onclick="refreshTable()" title="Rafraîchir"
                            class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table responsive -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">
                                Numéro
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Libellé
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-44">
                                Appel d'Offres
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-36">
                                Période
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-36">
                                Attribution
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">
                                Statut
                            </th>
                            @canany(['lots.view-details', 'lots.update', 'attributions_lots.assign', 'attributions_lots.withdraw', 'lots.view-history', 'lots.duplicate', 'lots.delete'])
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">
                                    Actions
                                </th>
                            @endcanany
                        </tr>
                    </thead>

                    <tbody id="tableBody" class="divide-y divide-gray-100">
                        @forelse($lots as $lot)
                            <tr class="hover:bg-gray-50/70 transition-colors group">
                                <!-- Numéro -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $lot->numero }}
                                    </span>
                                </td>

                                <!-- Libellé -->
                                <td class="px-4 py-3">
                                    <div class="max-w-xs">
                                        <p class="text-sm font-medium text-gray-900 truncate" title="{{ $lot->libelle }}">
                                            {{ $lot->libelle }}
                                        </p>
                                        @if ($lot->description_critere)
                                            <p class="text-xs text-gray-500 mt-0.5 truncate" title="{{ $lot->description_critere }}">
                                                {{ Str::limit($lot->description_critere, 50) }}
                                            </p>
                                        @endif
                                    </div>
                                </td>

                                <!-- Appel d'Offres -->
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-medium text-gray-900 whitespace-nowrap">
                                            {{ $lot->appelOffre->numero_appel_offre }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                            <i class="fas fa-tag text-gray-400"></i>
                                            {{ $lot->appelOffre->typeAppelOffre->code_type_appel_offre }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Dates Prévues -->
                                <td class="px-4 py-3 text-center">
                                    @if ($lot->date_debut_prevue && $lot->date_fin_prevue)
                                        <div class="inline-flex flex-col items-center gap-1 text-xs">
                                            <div class="flex items-center gap-1.5 text-gray-600">
                                                <i class="fas fa-play text-green-500 text-[10px]"></i>
                                                <span>{{ $lot->date_debut_prevue->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-gray-600">
                                                <i class="fas fa-stop text-red-500 text-[10px]"></i>
                                                <span>{{ $lot->date_fin_prevue->format('d/m/Y') }}</span>
                                            </div>
                                            @if ($lot->calculerDuree())
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-full text-[10px] font-medium">
                                                    <i class="fas fa-clock"></i>
                                                    {{ $lot->calculerDuree() }}j
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Non défini</span>
                                    @endif
                                </td>

                                <!-- Attribution -->
                                <td class="px-4 py-3 text-center">
                                    @if ($lot->attribution_lot)
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <i class="fas fa-check-circle"></i>
                                                Attribué
                                            </span>
                                            @if ($lot->attributionActive)
                                                <span class="text-[10px] text-gray-500 max-w-[120px] truncate" title="{{ $lot->attributionActive->prestataire->raison_sociale_prestataire ?? '' }}">
                                                    {{ Str::limit($lot->attributionActive->prestataire->raison_sociale_prestataire ?? 'N/A', 18) }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                            <i class="fas fa-hourglass-half"></i>
                                            En attente
                                        </span>
                                    @endif
                                </td>

                                <!-- Statut -->
                                <td class="px-4 py-3 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        @if ($lot->statut_lot)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                                Actif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                                Inactif
                                            </span>
                                        @endif
                                        @if ($lot->isRetire())
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-100 text-red-700">
                                                <i class="fas fa-ban"></i>
                                                Retiré
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Actions -->
                                @canany(['lots.view-details', 'lots.update', 'attributions_lots.assign', 'attributions_lots.withdraw', 'lots.view-history', 'lots.duplicate', 'lots.delete'])
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            @can('lots.view-details')
                                                <a href="{{ route('lots-appels-offres.show', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan

                                            @can('lots.update')
                                                {{-- @if (!$lot->attributionActive) --}}
                                                    <a href="{{ route('lots-appels-offres.edit', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                                                        class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                {{-- @endif --}}
                                            @endcan

                                            @canany(['attributions_lots.assign', 'attributions_lots.withdraw', 'lots.view-history', 'lots.duplicate', 'lots.delete'])
                                                <!-- Menu Actions -->
                                                <div class="relative">
                                                    <button onclick="toggleMenu(event, '{{ $lot->id_lot }}')"
                                                        class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-all" title="Plus d'actions">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div id="menu-{{ $lot->id_lot }}"
                                                        class="hidden fixed w-48 bg-white rounded-xl shadow-xl border border-gray-200 z-[9999] py-1">
                                                        @can('attributions_lots.assign')
                                                            @if (!$lot->attribution_lot)
                                                                <button onclick="openAttributionModal('{{ $lot->id_lot }}')"
                                                                    class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition-colors">
                                                                    <i class="fas fa-user-check text-green-500 w-4"></i>
                                                                    Attribuer
                                                                </button>
                                                            @endif
                                                        @endcan

                                                        @can('attributions_lots.withdraw')
                                                            @if ($lot->isAttribue() && !$lot->isRetire())
                                                                <button onclick="openRetraitModal('{{ $lot->id_lot }}')"
                                                                    class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition-colors">
                                                                    <i class="fas fa-ban text-red-500 w-4"></i>
                                                                    Retirer
                                                                </button>
                                                            @endif
                                                        @endcan

                                                        @can('lots.view-history')
                                                            <button onclick="viewHistorique('{{ $lot->id_lot }}')"
                                                                class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition-colors">
                                                                <i class="fas fa-history text-blue-500 w-4"></i>
                                                                Historique
                                                            </button>
                                                        @endcan

                                                        @can('lots.delete')
                                                            @if (!$lot->isAttribue())
                                                                <div class="border-t border-gray-100 my-1"></div>
                                                                <button onclick="confirmDelete('{{ $lot->id_lot }}', '{{ $lot->numero }}')"
                                                                    class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 transition-colors">
                                                                    <i class="fas fa-trash w-4"></i>
                                                                    Supprimer
                                                                </button>
                                                            @endif
                                                        @endcan
                                                    </div>
                                                </div>
                                            @endcanany
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                        </div>
                                        <h3 class="text-gray-600 font-medium mb-1">Aucun lot trouvé</h3>
                                        <p class="text-gray-400 text-sm">Essayez de modifier vos filtres de recherche</p>
                                        @can('lots.create')
                                            <a href="{{ route('lots.create') }}"
                                                class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all text-sm font-medium">
                                                <i class="fas fa-plus"></i>
                                                Créer un lot
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($lots->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $lots->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- Modal Confirmation Suppression -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-scaleIn">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Confirmer la suppression</h3>
                <p id="deleteMessage" class="text-sm text-gray-600 mb-6"></p>

                <div class="flex items-center justify-center gap-3">
                    <button onclick="closeDeleteModal()"
                        class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium text-sm">
                        Annuler
                    </button>
                    @can('lots.delete')
                        <button onclick="executeDelete()"
                            class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all font-medium text-sm">
                            <i class="fas fa-trash mr-2"></i>
                            Supprimer
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    @can('lots.read')
        @push('scripts')
            <script>
                let deleteLotId = null;

                // Menu Actions avec positionnement intelligent
                function toggleMenu(event, id) {
                    event.stopPropagation();

                    const button = event.currentTarget;
                    const menu = document.getElementById('menu-' + id);

                    // Fermer tous les autres menus
                    document.querySelectorAll('[id^="menu-"]').forEach(m => {
                        if (m.id !== 'menu-' + id) m.classList.add('hidden');
                    });

                    menu.classList.toggle('hidden');

                    if (!menu.classList.contains('hidden')) {
                        const rect = button.getBoundingClientRect();
                        const menuHeight = menu.offsetHeight || 150;
                        const menuWidth = menu.offsetWidth || 192;

                        let top = rect.bottom + 8;
                        let left = rect.right - menuWidth;

                        // Ajustement vertical
                        if (top + menuHeight > window.innerHeight - 20) {
                            top = rect.top - menuHeight - 8;
                        }

                        // Ajustement horizontal
                        if (left < 20) {
                            left = rect.left;
                        }

                        menu.style.top = top + 'px';
                        menu.style.left = left + 'px';
                    }
                }

                // Fermer menus au clic extérieur
                document.addEventListener('click', function(event) {
                    if (!event.target.closest('[id^="menu-"]') && !event.target.closest('button[onclick*="toggleMenu"]')) {
                        document.querySelectorAll('[id^="menu-"]').forEach(menu => menu.classList.add('hidden'));
                    }
                });

                // Fermer menus au scroll
                document.addEventListener('scroll', function() {
                    document.querySelectorAll('[id^="menu-"]').forEach(menu => menu.classList.add('hidden'));
                }, true);

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
                    document.getElementById('deleteMessage').textContent = `Êtes-vous sûr de vouloir supprimer le lot "${numero}" ? Cette action est irréversible.`;
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

                // Recherche avec debounce
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

                // Attribution
                window.openAttributionModal = function(id) {
                    window.location.href = `/lots/${id}`;
                }

                // Retrait
                window.openRetraitModal = function(id) {
                    const motif = prompt('Veuillez indiquer le motif du retrait:');
                    if (motif && motif.trim()) {
                        fetch(`/lots/${id}/retirer`, {
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
                    from { opacity: 0; transform: translateY(-10px); }
                    to { opacity: 1; transform: translateY(0); }
                }

                @keyframes scaleIn {
                    from { opacity: 0; transform: scale(0.95); }
                    to { opacity: 1; transform: scale(1); }
                }

                .animate-fadeIn {
                    animation: fadeIn 0.3s ease-out;
                }

                .animate-scaleIn {
                    animation: scaleIn 0.2s ease-out;
                }

                /* Scrollbar personnalisée */
                .overflow-x-auto::-webkit-scrollbar {
                    height: 8px;
                }

                .overflow-x-auto::-webkit-scrollbar-track {
                    background: #f1f5f9;
                    border-radius: 4px;
                }

                .overflow-x-auto::-webkit-scrollbar-thumb {
                    background: #cbd5e1;
                    border-radius: 4px;
                }

                .overflow-x-auto::-webkit-scrollbar-thumb:hover {
                    background: #94a3b8;
                }

                /* Hover effect sur les lignes */
                tbody tr {
                    transition: background-color 0.15s ease;
                }

                /* Amélioration du focus pour l'accessibilité */
                input:focus, select:focus, button:focus {
                    outline: none;
                }
            </style>
        @endpush
    @endcan
@endsection
