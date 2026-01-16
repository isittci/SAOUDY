@extends('layouts.main')
@section('title', 'Prestataires')
@section('breadcrumb', 'Prestataires')

@section('content')
    <!-- Filters Bar -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et bouton créer -->
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-building text-orange-500"></i>
                        <span>Prestataires</span>
                    </h1>
                    @can('prestataires.create')
                    <button onclick="window.location.href='{{ route('prestataires.create') }}'"
                        class="md:hidden px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Nouveau</span>
                    </button>
                    @endcan
                </div>

                <!-- Filtres et actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Recherche -->
                    <div class="relative flex-1 sm:min-w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Rechercher par raison sociale, email, téléphone..."
                            value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all" />
                    </div>

                    <!-- Filtre statut -->
                    <select id="statutFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all cursor-pointer">
                        <option value="">Tous les statuts</option>
                        <option value="1" {{ request('statut') == '1' ? 'selected' : '' }}>Actifs</option>
                        <option value="0" {{ request('statut') == '0' ? 'selected' : '' }}>Inactifs</option>
                    </select>



                    @can('prestataires.create')
                    <!-- Bouton créer (desktop) -->
                    <button onclick="window.location.href='{{ route('prestataires.create') }}'"
                        class="hidden md:flex px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Créer</span>
                    </button>
                    @endcan
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

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Prestataires</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $prestataires->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-building text-orange-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Actifs</p>
                        <p class="text-2xl font-bold text-green-600">{{ $prestataires->where('statut_prestataire', true)->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Inactifs</p>
                        <p class="text-2xl font-bold text-gray-600">{{ $prestataires->where('statut_prestataire', false)->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-pause-circle text-gray-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Avec lots en cours</p>
                        <p class="text-2xl font-bold text-blue-600">-</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tasks text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- En-tête du tableau -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Liste des prestataires (<span id="totalCount">{{ $prestataires->total() }}</span>)
                    </h2>
                    <div class="flex items-center space-x-2">
                        <button onclick="refreshTable()"
                            class="px-3 py-2 text-gray-600 hover:text-orange-500 hover:bg-orange-50 rounded-lg transition-all duration-200">
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
                                Prestataire</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Identification</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Contact</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Localisation</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Statut</th>
                            @canany(['prestataires.view-details', 'prestataires.update', 'prestataires.toggle-status', 'banques_prestataires.read', 'capacites_techniques.read', 'situations_financieres.read', 'prestataires.delete'])
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-gray-200 bg-white">
                        @forelse($prestataires as $prestataire)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center text-white font-bold shadow-sm">
                                            {{ strtoupper(substr($prestataire->raison_sociale_prestataire, 0, 2)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $prestataire->raison_sociale_prestataire }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <i class="fas fa-id-card mr-1"></i>{{ $prestataire->numero_identification_prestataire }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <div class="flex items-center mb-1">
                                            <span class="text-xs text-gray-500 w-16">CC:</span>
                                            <span class="font-medium">{{ $prestataire->numero_cc_prestataire ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-xs text-gray-500 w-16">RCCM:</span>
                                            <span class="font-medium">{{ $prestataire->numero_rccm_prestataire ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="flex items-center text-gray-900 mb-1">
                                            <i class="fas fa-envelope text-gray-400 mr-2 w-4"></i>
                                            <a href="mailto:{{ $prestataire->email_prestataire }}" class="hover:text-orange-500 transition-colors">
                                                {{ $prestataire->email_prestataire }}
                                            </a>
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-phone text-gray-400 mr-2 w-4"></i>
                                            {{ $prestataire->telephone_principal_prestataire ?? $prestataire->telephone_prestataire ?? '-' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                            <span>{{ $prestataire->ville_prestataire ?? '-' }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 ml-5">
                                            {{ $prestataire->pays_prestataire ?? $prestataire->pays ?? '-' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if ($prestataire->statut_prestataire)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            <i class="fas fa-times-circle mr-1"></i> Inactif
                                        </span>
                                    @endif
                                </td>
                                @canany(['prestataires.view-details', 'prestataires.update', 'prestataires.toggle-status', 'banques_prestataires.read', 'capacites_techniques.read', 'situations_financieres.read', 'prestataires.delete'])
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        @can('prestataires.view-details')
                                        <!-- Voir détails -->
                                        <button onclick="window.location.href='{{ route('prestataires.show', $prestataire->id_prestataire) }}'"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                            title="Voir détails">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        @endcan

                                        @can('prestataires.update')
                                        <!-- Modifier -->
                                        <button onclick="window.location.href='{{ route('prestataires.edit', $prestataire->id_prestataire) }}'"
                                            class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200"
                                            title="Modifier">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        @endcan

                                        @can('prestataires.toggle-status')
                                        <!-- Toggle Status -->
                                        <button
                                            onclick="toggleStatus('{{ $prestataire->id_prestataire }}', {{ $prestataire->statut_prestataire ? 'true' : 'false' }})"
                                            class="p-2 {{ $prestataire->statut_prestataire ? 'text-gray-600 hover:bg-gray-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition-all duration-200"
                                            title="{{ $prestataire->statut_prestataire ? 'Désactiver' : 'Activer' }}">
                                            <i class="fas fa-power-off text-sm"></i>
                                        </button>
                                        @endcan

                                        @canany(['banques_prestataires.read', 'capacites_techniques.read', 'situations_financieres.read', 'prestataires.delete'])
                                        <!-- Menu Actions -->
                                        <div class="relative">
                                            <button onclick="toggleMenu('{{ $prestataire->id_prestataire }}')"
                                                class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-all duration-200"
                                                title="Plus d'actions">
                                                <i class="fas fa-ellipsis-v text-sm"></i>
                                            </button>
                                            <div id="menu-{{ $prestataire->id_prestataire }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                                <div class="py-1">

                                                   <a href="{{ route('exports.prestataires.fiche.excel', $prestataire->id_prestataire) }}"
                                                    title="Télécharger la fiche Excel"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fa fa-file-excel"></i> Exporter Excel
                                                    </a>

                                                    {{-- Fiche PDF --}}
                                                    <a href="{{ route('exports.prestataires.fiche.pdf', $prestataire->id_prestataire) }}"
                                                    title="Télécharger la fiche PDF"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fa fa-file-pdf"></i> Exporter PDF
                                                    </a>



                                                    @can('banques_prestataires.read')
                                                    <button onclick="viewBanques('{{ $prestataire->id_prestataire }}')"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fas fa-university text-green-500 mr-2"></i>
                                                        Banques
                                                    </button>
                                                    @endcan

                                                    @can('capacites_techniques.read')
                                                    <button onclick="viewCapacites('{{ $prestataire->id_prestataire }}')"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fas fa-cogs text-purple-500 mr-2"></i>
                                                        Capacités techniques
                                                    </button>
                                                    @endcan

                                                    @can('situations_financieres.read')
                                                    <button onclick="viewFinances('{{ $prestataire->id_prestataire }}')"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fas fa-chart-line text-indigo-500 mr-2"></i>
                                                        Situation financière
                                                    </button>
                                                    @endcan

                                                    @can('prestataires.delete')
                                                    <hr class="my-1">
                                                    <button
                                                        onclick="confirmDelete('{{ $prestataire->id_prestataire }}', '{{ addslashes($prestataire->raison_sociale_prestataire) }}')"
                                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                                        <i class="fas fa-trash mr-2"></i>
                                                        Supprimer
                                                    </button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                        @endcanany
                                    </div>
                                </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-building text-gray-400 text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-700 mb-2">Aucun prestataire trouvé</h3>
                                        @can('prestataires.create')
                                            <p class="text-gray-500 text-sm mb-4">Commencez par créer votre premier prestataire</p>
                                            <button onclick="window.location.href='{{ route('prestataires.create') }}'"
                                                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all text-sm shadow-md">
                                                <i class="fas fa-plus mr-2"></i>Créer un prestataire
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($prestataires->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $prestataires->appends(request()->query())->links() }}
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
                        @can('prestataires.delete')
                        <button onclick="executeDelete()"
                            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium">
                            Supprimer
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('prestataires.read')
    @push('scripts')
        <script>
            let deletePrestataireId = null;

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

            // Toggle statut
            window.toggleStatus = function(id, isActive) {
                const action = isActive ? 'désactiver' : 'activer';
                if (confirm(`Voulez-vous vraiment ${action} ce prestataire ?`)) {
                    fetch(`/prestataires/${id}/toggle-status`, {
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

            // Confirmer suppression
            window.confirmDelete = function(id, raisonSociale) {
                deletePrestataireId = id;
                document.getElementById('deleteMessage').textContent =
                    `Êtes-vous sûr de vouloir supprimer le prestataire "${raisonSociale}" ?`;
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            // Exécuter suppression
            window.executeDelete = function() {
                if (!deletePrestataireId) return;

                fetch(`/prestataires/${deletePrestataireId}`, {
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
                deletePrestataireId = null;
            }

            // Actions supplémentaires
            // window.viewDocuments = function(id) {
            //     window.location.href = `/prestataires/${id}/documents`;
            // }

            window.viewBanques = function(id) {
                window.location.href = "{{ route('banques.index', ':prestataireId') }}".replace(':prestataireId', id);
            }

            window.viewCapacites = function(id) {
                window.location.href = `/prestataires/${id}/capacites-techniques`;
            }

            window.viewFinances = function(id) {
                window.location.href = `/prestataires/${id}/situations-financieres`;
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
            document.getElementById('statutFilter').addEventListener('change', applyFilters);

            function applyFilters() {
                const search = document.getElementById('searchInput').value;
                const statut = document.getElementById('statutFilter').value;

                const params = new URLSearchParams();
                if (search) params.append('search', search);
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
