@extends('layouts.main')
@section('title', 'Types d\'Appels d\'Offres')
@section('breadcrumb', 'Types d\'Appels d\'Offres')

@section('content')
    <!-- Filters Bar -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et bouton créer -->
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-tags text-orange-500"></i>
                        <span>Types d'Appels d'Offres</span>
                    </h1>
                    <button onclick="openCreateModal()"
                        class="md:hidden px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Nouveau</span>
                    </button>
                </div>

                <!-- Filtres et actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Recherche -->

                    <div class="relative flex-1 sm:min-w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="searchInput" name="search"
                            value="{{ request('search') }}"
                            placeholder="Rechercher par code ou libellé..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all" />
                    </div>


                    <!-- Filtre statut -->
                    <select id="statutFilter" name="actif"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all cursor-pointer">
                        <option value="">Tous les statuts</option>
                        <option value="1" @selected(request('actif') === '1')>Actifs</option>
                        <option value="0" @selected(request('actif') === '0')>Inactifs</option>
                    </select>

                    <!-- Bouton créer (desktop) -->
                    <button onclick="openCreateModal()"
                        class="hidden md:flex px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Nouveau Type</span>
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
                        Liste des types (<span id="totalCount">{{ $typesAO->total() }}</span>)
                    </h2>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('types-appels-offres.index') }}"
                            class="px-3 py-2 text-gray-600 hover:text-orange-500 hover:bg-orange-50 rounded-lg transition-all duration-200">
                            <i class="fas fa-sync-alt text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Table responsive -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Code</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Libellé</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Intervalle de valeur</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Appels d'Offre</th>
                            {{-- <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Version</th> --}}
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Statut</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-gray-200 bg-white">
                        @forelse($typesAO as $type)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-orange-100 text-orange-700">
                                            {{ $type->code_type_appel_offre }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $type->libelle_type_appel_offre }}
                                    </div>
                                    @if ($type->description_critere_type_appel_offre)
                                        <div class="text-xs text-gray-500 mt-1 line-clamp-1">
                                            {{ Str::limit($type->description_critere_type_appel_offre, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">
                                        <div class="font-medium">
                                            {{ number_format($type->valeur_minimuim_type_appel_offre, 0, ',', ' ') }} FCFA
                                        </div>
                                        <div class="text-xs text-gray-500">à
                                            {{ number_format($type->valeur_maximuim_type_appel_offre, 0, ',', ' ') }} FCFA
                                        </div>
                                    </div>
                                </td>
                                {{-- <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $count = $type->appel_offres_count;
                                        $badgeClass = match(true) {
                                            $count == 0 => 'bg-gray-100 text-gray-500',
                                            $count <= 5 => 'bg-blue-100 text-blue-800',
                                            $count <= 20 => 'bg-orange-100 text-orange-800',
                                            default => 'bg-green-100 text-green-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClass }}" title="{{ $count }} appel(s) d'offres">
                                        <i class="fas fa-file-contract mr-1"></i>
                                        {{ $count }}
                                    </span>
                                </td> --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $count = $type->appel_offres_count;
                                        $badgeClass = match(true) {
                                            $count == 0 => 'bg-gray-100 text-gray-500',
                                            $count <= 5 => 'bg-blue-100 text-blue-800',
                                            $count <= 20 => 'bg-orange-100 text-orange-800',
                                            default => 'bg-green-100 text-green-800',
                                        };
                                    @endphp

{{-- {{ $type }} --}}
                                    {{-- Lien vers les appels d'offres --}}
                                    <a href="{{ $count > 0 ? route('types-appels-offres.appels-offres.index',  $type->id_type_appel_offre) : '#' }}"
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClass }} hover:underline"
                                    title="{{ $count }} appel(s) d'offres">
                                        <i class="fas fa-file-contract mr-1"></i>
                                        {{ $count }}
                                    </a>
                                </td>

                                {{-- <td class="px-6 py-4 whitespace-nowrap text-center">

                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-purple-50 to-indigo-50 text-purple-700 border border-purple-200">
                                        <i class="fas fa-code-branch mr-1"></i>
                                        {{ 'V.' . $type->version_type_appel_offre }}
                                    </span>
                                </td> --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if ($type->actif_type_appel_offre)
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Voir détails -->
                                        <button onclick="viewDetails('{{ $type->id_type_appel_offre }}')"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                            title="Voir détails">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>

                                        @if ($type->actif_type_appel_offre)
                                            <!-- Modifier -->
                                            <button onclick='openEditModal(@json($type))'
                                                class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200"
                                                title="Modifier">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
                                        @endif



                                        <button
                                            onclick="toggleStatus('{{ $type->id_type_appel_offre }}', {{ $type->actif_type_appel_offre ? 'true' : 'false' }})"
                                            class="p-2 rounded-lg transition-all duration-200 {{ $type->actif_type_appel_offre ? 'text-green-600 hover:bg-green-50' : 'text-red-600 hover:bg-red-50' }}"
                                            title="{{ $type->actif_type_appel_offre ? 'Désactiver' : 'Activer' }}">
                                            <i class="fas {{ $type->actif_type_appel_offre ? 'fa-toggle-on' : 'fa-toggle-off' }} text-sm"></i>
                                        </button>

                                        <!-- Supprimer -->
                                        <button
                                            onclick="confirmDelete('{{ $type->id_type_appel_offre }}', '{{ $type->libelle_type_appel_offre }}', {{ $type->appel_offres_count }})"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200"
                                            title="Supprimer">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <i class="fas fa-inbox text-gray-300 text-5xl"></i>
                                        <p class="text-gray-500 font-medium">Aucun type d'appel d'offres trouvé</p>
                                        <button onclick="openCreateModal()"
                                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all duration-200">
                                            Créer le premier type
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($typesAO->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $typesAO->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- Modal Créer/Modifier -->
    <div id="formModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full transform transition-all">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Nouveau Type d'Appel d'Offres</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Form -->
                <form id="typeForm" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" id="formMethod" name="_method" value="POST">
                    <input type="hidden" id="typeId" name="type_id">

                    <div class="space-y-5">
                        <!-- Libellé -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Libellé <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="libelle_type_appel_offre" id="libelle" required
                                maxlength="160"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                placeholder="Ex: Appel d'offres ouvert">
                            <div id="error_libelle" class="hidden text-red-500 text-sm mt-1"></div>
                        </div>

                        <!-- Code -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code_type_appel_offre" id="code" required maxlength="10"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent uppercase"
                                placeholder="Ex: AOO">
                            <div id="error_code" class="hidden text-red-500 text-sm mt-1"></div>
                        </div>

                        <!-- Valeurs -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Valeur minimale (FCFA) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="valeur_minimuim_type_appel_offre" id="valeur_min" required
                                    min="0" step="0.01"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                    placeholder="0">
                                <div id="error_valeur_min" class="hidden text-red-500 text-sm mt-1"></div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Valeur maximale (FCFA) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="valeur_maximuim_type_appel_offre" id="valeur_max" required
                                    min="0" step="0.01"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent"
                                    placeholder="0">
                                <div id="error_valeur_max" class="hidden text-red-500 text-sm mt-1"></div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea name="description_critere_type_appel_offre" id="description" rows="4"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none"
                                placeholder="Description détaillée du type d'appel d'offres..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Motif de modification
                            </label>
                            <textarea name="motif_modification_type_appel_offre" id="description" rows="2"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent resize-none"
                                placeholder="Pourquoi voulez-vous modifier du type d'appel d'offres..."></textarea>
                        </div>

                        <!-- Statut -->
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="actif_type_appel_offre" id="actif" value="1" checked
                                class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                            <label for="actif" class="text-sm font-medium text-gray-700">Type actif</label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                        <button type="button" onclick="closeModal()"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                            Annuler
                        </button>
                        <button type="submit" id="submitBtn"
                            class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 font-medium shadow-md hover:shadow-lg">
                            <i class="fas fa-save mr-2"></i>
                            <span id="submitText">Enregistrer</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
            document.addEventListener('DOMContentLoaded', function() {
                let deleteTypeId = null;

                // Ouvrir modal création
                window.openCreateModal = function() {
                    document.getElementById('modalTitle').textContent = 'Nouveau Type d\'Appel d\'Offres';
                    document.getElementById('typeForm').action = "{{ route('types-appels-offres.store') }}";
                    document.getElementById('formMethod').value = 'POST';
                    document.getElementById('typeForm').reset();
                    document.getElementById('actif').checked = true;
                    clearErrors();
                    document.getElementById('formModal').classList.remove('hidden');
                }

                // Ouvrir modal édition
                window.openEditModal = function(type) {
                    document.getElementById('modalTitle').textContent = 'Modifier Type d\'Appel d\'Offres';
                    document.getElementById('typeForm').action = "{{ route('types-appels-offres.show', ':id') }}"
                        .replace(':id', type.id_type_appel_offre);
                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('typeId').value = type.id_type_appel_offre;

                    document.getElementById('libelle').value = type.libelle_type_appel_offre;
                    document.getElementById('code').value = type.code_type_appel_offre;
                    document.getElementById('code').readOnly = true;
                    document.getElementById('code').disabled = true;
                    document.getElementById('valeur_min').value = type.valeur_minimuim_type_appel_offre;
                    document.getElementById('valeur_max').value = type.valeur_maximuim_type_appel_offre;
                    document.getElementById('description').value = type.description_critere_type_appel_offre || '';
                    document.getElementById('actif').checked = type.actif_type_appel_offre;

                    clearErrors();
                    document.getElementById('formModal').classList.remove('hidden');
                }

                // Fermer modal
                window.closeModal = function() {
                    document.getElementById('formModal').classList.add('hidden');
                    document.getElementById('typeForm').reset();
                    clearErrors();
                }

                // Effacer les erreurs
                window.clearErrors = function() {
                    const errorDivs = document.querySelectorAll('[id^="error_"]');
                    errorDivs.forEach(div => {
                        div.classList.add('hidden');
                        div.textContent = '';
                    });
                }

                // Voir détails
                window.viewDetails = function(id) {
                    window.location.href = "{{ route('types-appels-offres.show', ':id') }}".replace(':id', id);
                }

                // Toggle statut
                window.toggleStatus = function(id, isActive) {
                    const action = isActive ? 'désactiver' : 'activer';
                    if (confirm(`Voulez-vous vraiment ${action} ce type d'appel d'offres ?`)) {
                        fetch("{{ route('types-appels-offres.toggle-status', ':id') }}".replace(':id', id), {
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
                window.confirmDelete = function(id, libelle, nbAO) {
                    deleteTypeId = id;
                    let message = `Êtes-vous sûr de vouloir supprimer le type "${libelle}" ?`;

                    if (nbAO > 0) {
                        message =
                            `Impossible de supprimer ce type car il est utilisé dans ${nbAO} appel(s) d'offres.`;
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
                    if (!deleteTypeId) return;

                    fetch("{{ route('types-appels-offres.destroy', ':id') }}".replace(':id', deleteTypeId), {
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
                    deleteTypeId = null;
                }

                // Rafraîchir le tableau
                window.refreshTable = function() {
                    location.reload();
                }

                // Gestion du formulaire
                const typeForm = document.getElementById('typeForm');


                typeForm.addEventListener('submit', function(e) {

                    e.preventDefault();

                    const submitBtn = document.getElementById('submitBtn');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;

                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...';

                    clearErrors();

                    const formData = new FormData(this);
                    const url = this.action;
                    const method = document.getElementById('formMethod').value;

                    fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                // Afficher les erreurs de validation
                                if (data.errors) {
                                    Object.keys(data.errors).forEach(key => {
                                        const errorDiv = document.getElementById(
                                            `error_${key.replace('_type_appel_offre', '').replace('_critere', '')}`
                                        );
                                        if (errorDiv) {
                                            errorDiv.textContent = data.errors[key][0];
                                            errorDiv.classList.remove('hidden');
                                        }
                                    });
                                } else {
                                    alert(data.message || 'Une erreur est survenue');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue lors de l\'enregistrement');
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        });
                });

                // // Recherche en temps réel
                // let searchTimeout;
                // document.getElementById('searchInput').addEventListener('input', function(e) {
                //     clearTimeout(searchTimeout);
                //     searchTimeout = setTimeout(() => {
                //         const search = e.target.value;
                //         const statut = document.getElementById('statutFilter').value;
                //         window.location.href = `?search=${search}&actif=${statut}`;
                //     }, 500);
                // });

                // Recherche sur perte de focus ou touche Entrée
                const searchInput = document.getElementById('searchInput');
                const statutFilter = document.getElementById('statutFilter');
                let initialValue = searchInput.value;

                function executeSearch() {
                    const search = searchInput.value.trim();
                    const statut = statutFilter.value;

                    // Éviter une requête si la valeur n'a pas changé
                    if (search === initialValue.trim()) {
                        return;
                    }

                    window.location.href = `?search=${encodeURIComponent(search)}&actif=${statut}`;
                }

                // Déclencher sur perte de focus
                searchInput.addEventListener('blur', function() {
                    executeSearch();
                });

                // Déclencher sur touche Entrée
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        executeSearch();
                    }
                });

                // Mettre à jour la valeur initiale après chargement
                document.addEventListener('DOMContentLoaded', function() {
                    initialValue = searchInput.value;
                });

                // Filtre statut
                document.getElementById('statutFilter').addEventListener('change', function(e) {
                    const search = document.getElementById('searchInput').value;
                    const statut = e.target.value;
                    window.location.href = `?search=${search}&actif=${statut}`;
                });

                // Fermer modales avec Escape
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeModal();
                        closeDeleteModal();
                    }
                });

                // Forcer majuscules pour le code
                document.getElementById('code').addEventListener('input', function(e) {
                    e.target.value = e.target.value.toUpperCase();
                });
            })
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
