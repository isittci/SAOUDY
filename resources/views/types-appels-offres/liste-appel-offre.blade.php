@extends('layouts.main')
@section('title', 'Appels d\'Offres')
@section('breadcrumb')
    <a @can('type_appels_offres.read') href="{{ route('types-appels-offres.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Types AO</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('type_appels_offres.view-details') href="{{ route('types-appels-offres.show', $typeAO->id_type_appel_offre) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">{{ $typeAO->code_type_appel_offre }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Appels d'offres</span>
@endsection

@section('content')
    <!-- Filters Bar -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et bouton créer -->
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-bullhorn text-orange-500"></i>
                        <span>Appels d'Offres</span>
                    </h1>
                    @can('appels_offres.create')
                        <button onclick="window.location.href='{{ route('appels-offres.create') }}'"
                            class="md:hidden px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                            <i class="fas fa-plus text-sm"></i>
                            <span class="text-sm">Créer nouveau</span>
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
                        <input type="text" id="searchInput" placeholder="Rechercher par numéro, libellé..."
                            value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all" />
                    </div>
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
                        Liste des appels d'offres (<span id="totalCount">{{ $appelOffres->total() }}</span>)
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
                                Numéro</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Libellé / Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Montant Retenu</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Dates Clés</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Nb Lots</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Statut</th>
                            @canany(['appels_offres.read', 'lots.create', 'appels_offres.update', 'appels_offres.duplicate',
                                'appels_offres.delete'])
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-gray-200 bg-white">
                        @forelse($appelOffres as $ao)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-orange-100 text-orange-700">
                                            {{ $ao->numero_appel_offre }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $ao->libelle_critere_appel_offre }}
                                    </div>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-tag mr-1"></i>{{ $ao->typeAppelOffre->code_type_appel_offre }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ number_format($ao->montant_global_appel_offre, 0, ',', ' ') }} FCFA
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs text-gray-700 space-y-1">
                                        @if ($ao->date_publication_critere_appel_offre)
                                            <div><span class="font-medium">Pub:</span>
                                                {{ $ao->date_publication_critere_appel_offre->format('d/m/Y') }}</div>
                                        @endif
                                        @if ($ao->date_limite_depot_critere_appel_offre)
                                            <div><span class="font-medium">Limite:</span>
                                                {{ $ao->date_limite_depot_critere_appel_offre->format('d/m/Y') }}</div>
                                        @endif
                                        @if ($ao->joursRestants() > 0)
                                            <div class="text-orange-600 font-semibold">
                                                <i class="fas fa-clock mr-1"></i>{{ $ao->joursRestants() }} jour(s)
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $ao->lots->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex flex-col items-center space-y-1">
                                        @if ($ao->statut_evaluation_critere_appel_offre)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i> Actif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                <i class="fas fa-times-circle mr-1"></i> Inactif
                                            </span>
                                        @endif

                                        @if ($ao->isCloture())
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                Clôturé
                                            </span>
                                        @elseif($ao->isEnCours())
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                En cours
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                @canany(['appels_offres.read', 'lots.create', 'appels_offres.update',
                                    'appels_offres.duplicate', 'appels_offres.delete'])
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            @can('appels_offres.read')
                                                <!-- Voir détails -->
                                                <button
                                                    onclick="window.location.href='{{ route('appels-offres.show', $ao->id_appel_offre) }}'"
                                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                                    title="Voir détails">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </button>
                                            @endcan

                                            @can('appels_offres.update')
                                                <!-- Modifier -->
                                                <button
                                                    onclick="window.location.href='{{ route('appels-offres.edit', $ao->id_appel_offre) }}'"
                                                    class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200"
                                                    title="Modifier">
                                                    <i class="fas fa-edit text-sm"></i>
                                                </button>

                                                <!-- Toggle Status -->
                                                <button
                                                    onclick="toggleStatus('{{ $ao->id_appel_offre }}', {{ $ao->statut_evaluation_critere_appel_offre ? 'true' : 'false' }})"
                                                    class="p-2 {{ $ao->statut_evaluation_critere_appel_offre ? 'text-gray-600 hover:bg-gray-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition-all duration-200"
                                                    title="{{ $ao->statut_evaluation_critere_appel_offre ? 'Désactiver' : 'Activer' }}">
                                                    <i class="fas fa-power-off text-sm"></i>
                                                </button>
                                            @endcan

                                            @canany(['lots.create', 'appels_offres.update', 'appels_offres.duplicate',
                                                'appels_offres.delete'])
                                                <!-- Menu Actions -->
                                                <div class="relative">
                                                    <button onclick="toggleMenu('{{ $ao->id_appel_offre }}')"
                                                        class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-all duration-200"
                                                        title="Plus d'actions">
                                                        <i class="fas fa-ellipsis-v text-sm"></i>
                                                    </button>
                                                    <div id="menu-{{ $ao->id_appel_offre }}"
                                                        class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                                        <div class="py-1">
                                                            @can('lots.create')
                                                                <button
                                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                                    <i class="fas fa-boxes text-indigo-500 mr-2"></i>
                                                                    Ajouter lot
                                                                </button>
                                                            @endcan

                                                            @can('appels_offres.update')
                                                                <a href="{{ route('caracteristiques-appels-offres.index', $ao->id_appel_offre) }}"
                                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                                    <i class="fas fa-thumbs-up text-indigo-500 mr-2"></i>
                                                                    Caractéristiques
                                                                </a>
                                                                @if (!$ao->date_publication_critere_appel_offre)
                                                                    <button onclick="publier('{{ $ao->id_appel_offre }}')"
                                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-paper-plane mr-2 text-blue-500"></i>
                                                                        Publier
                                                                    </button>
                                                                @endif
                                                                @if ($ao->isEnCours())
                                                                    <button onclick="cloturer('{{ $ao->id_appel_offre }}')"
                                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-lock mr-2 text-yellow-500"></i>
                                                                        Clôturer
                                                                    </button>
                                                                @endif
                                                            @endcan

                                                            @can('appels_offres.duplicate')
                                                                <button onclick="duplicate('{{ $ao->id_appel_offre }}')"
                                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                                    <i class="fas fa-copy mr-2 text-purple-500"></i>
                                                                    Dupliquer
                                                                </button>
                                                            @endcan

                                                            @can('appels_offres.delete')
                                                                <button
                                                                    onclick="confirmDelete('{{ $ao->id_appel_offre }}', '{{ $ao->numero_appel_offre }}', {{ $ao->lots_count }})"
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
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <i class="fas fa-inbox text-gray-300 text-5xl"></i>
                                        <p class="text-gray-500 font-medium">Aucun appel d'offres trouvé</p>
                                        <button onclick="window.location.href=`{{ route('appels-offres.create') }}`"
                                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all duration-200">
                                            Créer le premier appel d'offres
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($appelOffres->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $appelOffres->links() }}
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
            let deleteAOId = null;

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
                if (confirm(`Voulez-vous vraiment ${action} cet appel d'offres ?`)) {
                    fetch("{{ route('appels-offres.toggle-status', ':id') }}".replace(':id', id), {
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

            // Publier
            window.publier = function(id) {
                if (confirm('Voulez-vous publier cet appel d\'offres ?')) {
                    fetch("{{ route('appels-offres.publier', ':id') }}".replace(':id', id), {
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

            // Clôturer
            window.cloturer = function(id) {
                if (confirm('Voulez-vous clôturer cet appel d\'offres ? Cette action modifiera la date limite de dépôt.')) {
                    fetch("{{ route('appels-offres.cloturer', ':id') }}".replace(':id', id), {
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

            // Dupliquer
            window.duplicate = function(id) {
                if (confirm('Voulez-vous dupliquer cet appel d\'offres ?')) {
                    fetch("{{ route('appels-offres.duplicate', ':id') }}".replace(':id', id), {
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
                                window.location.href = `/appels-offres/${data.data.id_appel_offre}/edit`;
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
            window.confirmDelete = function(id, numero, nbLots) {
                deleteAOId = id;
                let message = `Êtes-vous sûr de vouloir supprimer l'appel d'offres "${numero}" ?`;

                if (nbLots > 0) {
                    message = `Impossible de supprimer cet appel d'offres car il contient ${nbLots} lot(s).`;
                    document.getElementById('deleteMessage').innerHTML =
                        `<strong class="text-red-600">${message}</strong>`;
                    document.querySelector('#deleteModal button[onclick="executeDelete()"]').classList.add(
                        'hidden');
                } else {
                    document.getElementById('deleteMessage').textContent = message;
                    document.querySelector('#deleteModal button[onclick="executeDelete()"]').classList.remove(
                        'hidden');
                }

                document.getElementById('deleteModal').classList.remove('hidden');
            }

            // Exécuter suppression
            window.executeDelete = function() {
                if (!deleteAOId) return;

                fetch("{{ route('appels-offres.destroy', ':id') }}".replace(':id', deleteAOId), {
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
                deleteAOId = null;
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
            document.getElementById('typeFilter').addEventListener('change', applyFilters);
            document.getElementById('statutFilter').addEventListener('change', applyFilters);
            document.getElementById('etatFilter').addEventListener('change', applyFilters);

            function applyFilters() {
                const search = document.getElementById('searchInput').value;
                const type = document.getElementById('typeFilter').value;
                const statut = document.getElementById('statutFilter').value;
                const etat = document.getElementById('etatFilter').value;

                const params = new URLSearchParams();
                if (search) params.append('search', search);
                if (type) params.append('type_appel_offre_id', type);
                if (statut) params.append('statut', statut);
                if (etat) params.append('etat', etat);

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
@endsection
