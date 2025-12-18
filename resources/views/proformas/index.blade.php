@extends('layouts.main')
@section('title', 'Proformas')
@section('breadcrumb', 'Proformas')

@section('content')
    <!-- Filters Bar -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et bouton créer -->
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-file-invoice-dollar text-orange-500"></i>
                        <span>Proformas</span>
                    </h1>
                    <button onclick="window.location.href='{{ route('proformas.create') }}'"
                        class="md:hidden px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Nouvelle</span>
                    </button>
                </div>

                <!-- Filtres et actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Recherche -->
                    <div class="relative flex-1 sm:min-w-[280px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Rechercher par numéro..."
                            value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all" />
                    </div>

                    <!-- Filtre statut -->
                    <select id="statutFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all cursor-pointer">
                        <option value="">Tous les statuts</option>
                        <option value="1" {{ request('statut') == '1' ? 'selected' : '' }}>Actives</option>
                        <option value="0" {{ request('statut') == '0' ? 'selected' : '' }}>Inactives</option>
                    </select>

                    <!-- Bouton créer (desktop) -->
                    <button onclick="window.location.href='{{ route('proformas.create') }}'"
                        class="hidden md:flex px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
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

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-orange-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Actives</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['actives'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Inactives</p>
                        <p class="text-2xl font-bold text-gray-600">{{ $stats['inactives'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-pause-circle text-gray-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Utilisées</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['utilisees'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-link text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Montant Total</p>
                        <p class="text-lg font-bold text-purple-600">{{ number_format($stats['montant_total'] ?? 0, 0, ',', ' ') }}</p>
                        <p class="text-xs text-gray-400">FCFA</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-coins text-purple-500 text-xl"></i>
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
                        Liste des proformas (<span id="totalCount">{{ $proformas->total() }}</span>)
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
                                Numéro / Version</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Date</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Montant HT</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Remise</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Taxe</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Montant TTC</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Statut</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-gray-200 bg-white">
                        @forelse($proformas as $proforma)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center text-white font-bold shadow-sm">
                                            <i class="fas fa-file-invoice text-sm"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $proforma->numero_proforma }}
                                            </div>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    v{{ $proforma->version_proforma }}
                                                </span>
                                                @if($proforma->parent_id)
                                                    <span class="text-xs text-gray-400">
                                                        <i class="fas fa-code-branch"></i> Modifiée
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $proforma->date_proforma ? $proforma->date_proforma->format('d/m/Y') : '-' }}
                                    </div>
                                    {{-- <div class="text-xs text-gray-500">
                                        {{ $proforma->created_at->diffForHumans() }}
                                    </div> --}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ number_format($proforma->montant_retenu_proforma, 0, ',', ' ') }}
                                    </div>
                                    <div class="text-xs text-gray-500">FCFA</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($proforma->remise_montant_proforma > 0)
                                        <div class="text-sm font-medium text-red-600">
                                            -{{ number_format($proforma->remise_montant_proforma, 0, ',', ' ') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $proforma->pourcentage_remise }}%
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($proforma->taxe_montant > 0)
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($proforma->taxe_montant, 0, ',', ' ') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $proforma->taux_taxe }}%
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-bold text-green-600">
                                        {{ number_format($proforma->montant_ttc, 0, ',', ' ') }}
                                    </div>
                                    <div class="text-xs text-gray-500">FCFA</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if ($proforma->actif_proforma)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            <i class="fas fa-times-circle mr-1"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Voir détails -->
                                        <button onclick="window.location.href='{{ route('proformas.show', $proforma->id_proforma) }}'"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                            title="Voir détails">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>

                                        <!-- Modifier -->
                                        <button onclick="window.location.href='{{ route('proformas.edit', $proforma->id_proforma) }}'"
                                            class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200"
                                            title="Modifier">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>

                                        <!-- Toggle Status -->
                                        <button
                                            onclick="toggleStatus('{{ $proforma->id_proforma }}', {{ $proforma->actif_proforma ? 'true' : 'false' }})"
                                            class="p-2 {{ $proforma->actif_proforma ? 'text-gray-600 hover:bg-gray-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition-all duration-200"
                                            title="{{ $proforma->actif_proforma ? 'Désactiver' : 'Activer' }}">
                                            <i class="fas fa-power-off text-sm"></i>
                                        </button>

                                        <!-- Menu Actions -->
                                        <div class="relative">
                                            <button onclick="toggleMenu('{{ $proforma->id_proforma }}')"
                                                class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-all duration-200"
                                                title="Plus d'actions">
                                                <i class="fas fa-ellipsis-v text-sm"></i>
                                            </button>
                                            <div id="menu-{{ $proforma->id_proforma }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                                <div class="py-1">
                                                    <button onclick="creerVersion('{{ $proforma->id_proforma }}')"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fas fa-code-branch text-indigo-500 mr-2"></i>
                                                        Nouvelle version
                                                    </button>
                                                    <button onclick="duplicate('{{ $proforma->id_proforma }}')"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fas fa-copy text-purple-500 mr-2"></i>
                                                        Dupliquer
                                                    </button>
                                                    <button onclick="voirHistorique('{{ $proforma->id_proforma }}')"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fas fa-history text-blue-500 mr-2"></i>
                                                        Historique
                                                    </button>
                                                    <hr class="my-1">
                                                    <button
                                                        onclick="confirmDelete('{{ $proforma->id_proforma }}', '{{ $proforma->numero_proforma }}', {{ $proforma->estUtilisee() ? 'true' : 'false' }})"
                                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                                        <i class="fas fa-trash mr-2"></i>
                                                        Supprimer
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-file-invoice-dollar text-gray-400 text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-700 mb-2">Aucune proforma trouvée</h3>
                                        <p class="text-gray-500 text-sm mb-4">Commencez par créer votre première proforma</p>
                                        <button onclick="window.location.href='{{ route('proformas.create') }}'"
                                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all text-sm shadow-md">
                                            <i class="fas fa-plus mr-2"></i>Créer une proforma
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($proformas->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $proformas->appends(request()->query())->links() }}
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
                        <button onclick="executeDelete()" id="deleteBtn"
                            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium">
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nouvelle Version -->
    <div id="versionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-code-branch text-indigo-500 mr-2"></i>
                        Créer une nouvelle version
                    </h3>
                    <button onclick="closeVersionModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="versionForm" class="p-6">
                    <input type="hidden" id="version_proforma_id">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Motif de modification <span class="text-red-500">*</span>
                            </label>
                            <textarea id="motif_modification" rows="3" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent resize-none"
                                placeholder="Expliquez la raison de cette nouvelle version..."></textarea>
                        </div>

                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-yellow-500 mr-2 mt-0.5"></i>
                                <p class="text-sm text-yellow-700">
                                    La version actuelle sera désactivée et une nouvelle version sera créée avec les modifications.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeVersionModal()"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                            Créer la version
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let deleteProformaId = null;

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
                if (confirm(`Voulez-vous vraiment ${action} cette proforma ?`)) {
                    fetch(`/proformas/${id}/toggle-status`, {
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
            window.confirmDelete = function(id, numero, estUtilisee) {
                deleteProformaId = id;

                if (estUtilisee) {
                    document.getElementById('deleteMessage').innerHTML =
                        `<strong class="text-red-600">Impossible de supprimer la proforma "${numero}" car elle est utilisée dans des attributions.</strong>`;
                    document.getElementById('deleteBtn').classList.add('hidden');
                } else {
                    document.getElementById('deleteMessage').textContent =
                        `Êtes-vous sûr de vouloir supprimer la proforma "${numero}" ?`;
                    document.getElementById('deleteBtn').classList.remove('hidden');
                }

                document.getElementById('deleteModal').classList.remove('hidden');
            }

            // Exécuter suppression
            window.executeDelete = function() {
                if (!deleteProformaId) return;

                fetch(`/proformas/${deleteProformaId}`, {
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
                deleteProformaId = null;
            }

            // Dupliquer
            window.duplicate = function(id) {
                if (confirm('Voulez-vous dupliquer cette proforma ?')) {
                    fetch(`/proformas/${id}/duplicate`, {
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
                                window.location.href = `/proformas/${data.data.id_proforma}/edit`;
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

            // Créer version
            window.creerVersion = function(id) {
                document.getElementById('version_proforma_id').value = id;
                document.getElementById('motif_modification').value = '';
                document.getElementById('versionModal').classList.remove('hidden');
            }

            // Fermer modal version
            window.closeVersionModal = function() {
                document.getElementById('versionModal').classList.add('hidden');
            }

            // Soumettre nouvelle version
            document.getElementById('versionForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const id = document.getElementById('version_proforma_id').value;
                const motif = document.getElementById('motif_modification').value;

                if (!motif.trim()) {
                    alert('Le motif de modification est obligatoire');
                    return;
                }

                fetch(`/proformas/${id}/creer-version`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            motif_modification_proforma: motif
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = `/proformas/${data.data.id_proforma}`;
                        } else {
                            alert(data.message || 'Une erreur est survenue');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue');
                    });
            });

            // Voir historique
            window.voirHistorique = function(id) {
                window.location.href = `/proformas/${id}/historique`;
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

            // Fermer modals avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeDeleteModal();
                    closeVersionModal();
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
