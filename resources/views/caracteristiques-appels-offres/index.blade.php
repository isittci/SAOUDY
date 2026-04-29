@extends('layouts.main')
@section('title', 'Gestion des Caractéristiques')
@section('breadcrumb')
    <a @can('appels_offres.read') href="{{ route('appels-offres.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Appels d'Offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('appels_offres.view-details') href="{{ route('appels-offres.show', $appelOffre->id_appel_offre) }}" @endcan class="text-white/80 hover:text-white transition-colors">{{$appelOffre->numero_appel_offre}}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Caractéristiques</span>
@endsection

@section('content')
    <!-- Filters Bar -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et bouton créer -->
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-thumbs-up text-indigo-500"></i>
                        <span>Gestion des Caractéristiques</span>
                    </h1>
                    @can('caracteristiques_appels_offres.create')
                    <button onclick="window.location.href='{{ route('caracteristiques-appels-offres.create', $appelOffre->id_appel_offre) }}'"
                        class="md:hidden px-4 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Nouveau</span>
                    </button>
                    @endcan
                </div>

                <!-- Filtres et actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Recherche -->
                    <div class="relative flex-1 sm:min-w-[250px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Chercher par lieu, montant..."
                            value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent hover:border-indigo-300 transition-all" />
                    </div>

                    <!-- Filtre Version -->
                    <select id="versionFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent hover:border-indigo-300 transition-all cursor-pointer">
                        <option value="">Toutes les versions</option>
                        <option value="actuelle" {{ request('version') == 'actuelle' ? 'selected' : '' }}>Versions actuelles uniquement</option>
                    </select>

                    <!-- Tri -->
                    <select id="sortFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent hover:border-indigo-300 transition-all cursor-pointer">
                        <option value="created_at_desc" {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>Plus récent</option>
                        <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>Plus ancien</option>
                        <option value="version_desc" {{ request('sort') == 'version_desc' ? 'selected' : '' }}>Version (décroissant)</option>
                        <option value="version_asc" {{ request('sort') == 'version_asc' ? 'selected' : '' }}>Version (croissant)</option>
                    </select>

                    @can('caracteristiques_appels_offres.create')
                    <!-- Bouton créer (desktop) -->
                    <button
                        onclick="window.location.href='{{ route('caracteristiques-appels-offres.create', $appelOffre->id_appel_offre) }}'"
                        class="hidden md:flex px-6 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-lg transition-all duration-200 items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
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

        <!-- Info AO -->
        <div class="mb-6 bg-white rounded-2xl shadow-lg overflow-hidden border-l-4 border-indigo-500">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">
                                {{ $appelOffre->numero_appel_offre }}
                            </span>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-700">
                                {{ $appelOffre->typeAppelOffre->code_type_appel_offre }}
                            </span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ $appelOffre->libelle_critere_appel_offre }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-coins mr-1"></i>
                            Montant global: <strong>{{ number_format(floor($appelOffre->montant_global_appel_offre), 0, ',', ' ') }} FCFA</strong>
                        </p>
                    </div>
                    @can('appels_offres.view-details')
                    <a href="{{ route('appels-offres.show', $appelOffre->id_appel_offre) }}"
                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                        title="Voir l'appel d'offres">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Tableau -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- En-tête du tableau -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Liste des caractéristiques (<span id="totalCount">{{ $caracteristiques->total() }}</span>)
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
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Version</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lieu d'Exécution</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dates Prévues</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Durée (jours)</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Garantie</th>
                            @canany(['caracteristiques_appels_offres.view-details', 'caracteristiques_appels_offres.update', 'caracteristiques_appels_offres.delete'])
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-gray-200 bg-white">
                        @forelse($caracteristiques as $caract)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-purple-100 text-purple-700">
                                            V{{ $caract->version_caracteristique_appel_offre }}
                                        </span>
                                        @if(!$caract->parent_id)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">
                                                <i class="fas fa-star text-xs mr-1"></i>
                                                Initiale
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 line-clamp-1">
                                            {{ $caract->lieu_execution_caracteristique_appel_offre ?? 'Non spécifié' }}
                                        </div>
                                        @if($caract->motif_modification_caracteristique_appel_offre)
                                            <div class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                {{ Str::limit($caract->motif_modification_caracteristique_appel_offre, 30) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm space-y-1">
                                        @if($caract->date_demarrage_prevue_caracteristique_appel_offre)
                                            <div class="text-gray-900 flex items-center">
                                                <i class="fas fa-play text-green-500 text-xs mr-2"></i>
                                                {{ \Carbon\Carbon::parse($caract->date_demarrage_prevue_caracteristique_appel_offre)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                        @if($caract->date_livraison_previsionnelle_caracteristique_appel_offre)
                                            <div class="text-gray-900 flex items-center">
                                                <i class="fas fa-flag-checkered text-blue-500 text-xs mr-2"></i>
                                                {{ \Carbon\Carbon::parse($caract->date_livraison_previsionnelle_caracteristique_appel_offre)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                        @if(!$caract->date_demarrage_prevue_caracteristique_appel_offre && !$caract->date_livraison_previsionnelle_caracteristique_appel_offre)
                                            <span class="text-gray-400 text-xs">Non définies</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($caract->duree_estimee_jours_caracteristique_appel_offre)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-700">
                                            {{ number_format($caract->duree_estimee_jours_caracteristique_appel_offre, 0, ',', ' ') }} jours
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if($caract->montant_garantie_caracteristique_appel_offre)
                                        <div class="text-sm">
                                            <div class="font-semibold text-green-600">
                                                {{ number_format(floor($caract->montant_garantie_caracteristique_appel_offre), 0, ',', ' ') }} F
                                            </div>
                                            @if($caract->delai_garantie_jours_caracteristique_appel_offre)
                                                <div class="text-xs text-gray-500">
                                                    {{ $caract->delai_garantie_jours_caracteristique_appel_offre }}j
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                @canany(['caracteristiques_appels_offres.view-details', 'caracteristiques_appels_offres.update', 'caracteristiques_appels_offres.delete'])
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center space-x-2">
                                            @can('caracteristiques_appels_offres.view-details')
                                            <button onclick="window.location.href='{{ route('caracteristiques-appels-offres.show', [$appelOffre->id_appel_offre, $caract->id_caracteristique_appel_offre]) }}'"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                                title="Voir les détails">
                                                <i class="fas fa-eye text-sm"></i>
                                            </button>
                                            @endcan

                                            @can('caracteristiques_appels_offres.update')
                                            <button onclick="window.location.href='{{ route('caracteristiques-appels-offres.edit', [$appelOffre->id_appel_offre, $caract->id_caracteristique_appel_offre]) }}'"
                                                class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200"
                                                title="Modifier">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
                                            @endcan

                                            @can('caracteristiques_appels_offres.view-details')
                                            <button onclick="showHistorique('{{ $caract->id_caracteristique_appel_offre }}')"
                                                class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-all duration-200"
                                                title="Historique">
                                                <i class="fas fa-history text-sm"></i>
                                            </button>
                                            @endcan

                                            @can('caracteristiques_appels_offres.delete')
                                            <button onclick="confirmDelete('{{ $caract->id_caracteristique_appel_offre }}', 'V{{ $caract->version_caracteristique_appel_offre }}')"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200"
                                                title="Supprimer">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-cogs text-3xl text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">Aucune caractéristique trouvée</p>
                                        @can('caracteristiques_appels_offres.create')
                                            <button onclick="window.location.href='{{ route('caracteristiques-appels-offres.create', $appelOffre->id_appel_offre) }}'"
                                                class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2">
                                                <i class="fas fa-plus text-sm"></i>
                                                <span>Créer la première caractéristique</span>
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
            @if($caracteristiques->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $caracteristiques->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- Modal de suppression -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full animate-fadeIn">
            <div class="p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Confirmer la suppression</h3>
                </div>

                <p id="deleteMessage" class="text-gray-600 mb-6"></p>

                <div class="flex items-center space-x-3">
                    <button onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                        Annuler
                    </button>
                    @can('caracteristiques_appels_offres.delete')
                        <button onclick="executeDelete()"
                            class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium">
                            Supprimer
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    @can('caracteristiques_appels_offres.read')
        @push('scripts')
            <script>
                let deleteCaractId = null;

                // Afficher historique
                window.showHistorique = function(id) {
                    fetch("{{ route('caracteristiques-appels-offres.historique', [$appelOffre->id_appel_offre, ':caracteristique']) }}".replace(':caracteristique', id), {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data.length > 0) {
                                let message = `Historique des versions:\n\n`;
                                data.data.forEach(v => {
                                    message += `Version ${v.version_caracteristique_appel_offre} - ${new Date(v.created_at).toLocaleString('fr-FR')}\n`;
                                    if (v.motif_modification_caracteristique_appel_offre) {
                                        message += `Motif: ${v.motif_modification_caracteristique_appel_offre}\n`;
                                    }
                                    message += '\n';
                                });
                                alert(message);
                            } else {
                                window.location.href = "{{ route('caracteristiques-appels-offres.historique', [$appelOffre->id_appel_offre, ':caracteristique']) }}".replace(':caracteristique', id);
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Erreur lors de la récupération de l\'historique');
                        });
                }

                // Confirmer suppression
                window.confirmDelete = function(id, version) {
                    deleteCaractId = id;
                    const message = `Êtes-vous sûr de vouloir supprimer la caractéristique "${version}" ?`;
                    document.getElementById('deleteMessage').textContent = message;
                    document.getElementById('deleteModal').classList.remove('hidden');
                }

                // Exécuter suppression
                window.executeDelete = function() {
                    if (!deleteCaractId) return;

                    fetch("{{ route('caracteristiques-appels-offres.destroy', [$appelOffre->id_appel_offre, ':caracteristique']) }}".replace(':caracteristique', deleteCaractId), {
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
                    deleteCaractId = null;
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
                document.getElementById('versionFilter').addEventListener('change', applyFilters);
                document.getElementById('sortFilter').addEventListener('change', applyFilters);

                function applyFilters() {
                    const search = document.getElementById('searchInput').value;
                    const version = document.getElementById('versionFilter').value;
                    const sort = document.getElementById('sortFilter').value;

                    const params = new URLSearchParams();
                    if (search) params.append('search', search);
                    if (version) params.append('version', version);
                    if (sort) params.append('sort', sort);

                    window.location.href = `?${params.toString()}`;
                }

                // Fermer modal avec Escape
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
            </style>
        @endpush
    @endcan
@endsection
