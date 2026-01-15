@extends('layouts.main')
@section('title', 'Banques - ' . $prestataire->raison_sociale_prestataire)
@section('breadcrumb')
    <a @can('prestataires.read') href="{{ route('prestataires.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Prestataires</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('prestataires.view-details') href="{{ route('prestataires.show', $prestataire->id_prestataire) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">{{ Str::limit($prestataire->raison_sociale_prestataire, 30) }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Banques</span>
@endsection

@section('content')
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        @can('prestataires.view-details')
                            <a href="{{ route('prestataires.show', $prestataire->id_prestataire) }}"
                                class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                                <i class="fas fa-arrow-left text-gray-600"></i>
                            </a>
                        @endcan
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                                <i class="fas fa-university text-orange-500"></i>
                                <span>Banques</span>
                            </h1>
                            <p class="text-sm text-gray-600 mt-1">{{ $prestataire->raison_sociale_prestataire }}</p>
                        </div>
                    </div>
                    @can('banques_prestataires.create')
                        <button onclick="window.location.href='{{ route('banques.create', $prestataire->id_prestataire) }}'"
                            class="md:hidden px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                            <i class="fas fa-plus text-sm"></i>
                            <span class="text-sm">Nouvelle</span>
                        </button>
                    @endcan

                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1 sm:min-w-[280px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Rechercher..." value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all" />
                    </div>
                    <select id="statutFilter"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all cursor-pointer">
                        <option value="">Tous les statuts</option>
                        <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actives</option>
                        <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactives</option>
                    </select>
                    @can('banques_prestataires.create')
                        <button onclick="window.location.href='{{ route('banques.create', $prestataire->id_prestataire) }}'"
                            class="hidden md:flex px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 items-center space-x-2 shadow-md hover:shadow-lg active:scale-95 font-medium">
                            <i class="fas fa-plus text-sm"></i>
                            <span class="text-sm">Créer</span>
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">
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

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-university text-orange-500 text-xl"></i>
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
                        <p class="text-sm text-gray-500 font-medium">Avec Paiements</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['avec_paiements'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-money-check-alt text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">Liste des banques ({{ $banques->total() }})</h2>
                    <button onclick="location.reload()"
                        class="px-3 py-2 text-gray-600 hover:text-orange-500 hover:bg-orange-50 rounded-lg transition-all duration-200">
                        <i class="fas fa-sync-alt text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Banque</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Compte</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                IBAN / SWIFT</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Statut</th>
                            @canany(['banques_prestataires.view-details', 'banques_prestataires.toggle-status',
                                'banques_prestataires.update', 'banques_prestataires.delete'])
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($banques as $banque)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center text-white font-bold shadow-sm">
                                            <i class="fas fa-university text-sm"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ $banque->nom_banque }}
                                            </div>
                                            @if ($banque->code_banque)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ $banque->code_banque }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $banque->numero_compte_banque ?? '-' }}</div>
                                    @if ($banque->titulaire_compte_banque)
                                        <div class="text-xs text-gray-500"><i
                                                class="fas fa-user text-gray-400 mr-1"></i>{{ $banque->titulaire_compte_banque }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($banque->iban_banque)
                                        <div class="text-sm text-gray-900 font-mono">{{ $banque->iban_banque }}</div>
                                    @endif
                                    @if ($banque->swift_bic_banque)
                                        <div class="text-xs text-gray-500">SWIFT: {{ $banque->swift_bic_banque }}</div>
                                    @endif
                                    @if (!$banque->iban_banque && !$banque->swift_bic_banque)
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if ($banque->actif_banque)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800"><i
                                                class="fas fa-check-circle mr-1"></i> Active</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800"><i
                                                class="fas fa-times-circle mr-1"></i> Inactive</span>
                                    @endif
                                </td>
                                @canany(['banques_prestataires.view-details', 'banques_prestataires.toggle-status',
                                    'banques_prestataires.update', 'banques_prestataires.delete'])
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            @can('banques_prestataires.view-details')
                                                <button
                                                    onclick="window.location.href='{{ route('banques.show', ['prestataireId' => $prestataire->id_prestataire, 'banque' => $banque->id_banque]) }}'"
                                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                                    title="Voir">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </button>
                                            @endcan

                                            @can('banques_prestataires.update')
                                                <button
                                                    onclick="window.location.href='{{ route('banques.edit', ['prestataireId' => $prestataire->id_prestataire, 'banque' => $banque->id_banque]) }}'"
                                                    class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200"
                                                    title="Modifier">
                                                    <i class="fas fa-edit text-sm"></i>
                                                </button>
                                            @endcan

                                            @can('banques_prestataires.toggle-status')
                                                <button
                                                    onclick="toggleStatus('{{ $banque->id_banque }}', {{ $banque->actif_banque ? 'true' : 'false' }})"
                                                    class="p-2 {{ $banque->actif_banque ? 'text-gray-600 hover:bg-gray-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition-all duration-200"
                                                    title="{{ $banque->actif_banque ? 'Désactiver' : 'Activer' }}">
                                                    <i class="fas fa-power-off text-sm"></i>
                                                </button>
                                            @endcan

                                            @can('banques_prestataires.delete')
                                                <button
                                                    onclick="confirmDelete('{{ $banque->id_banque }}', '{{ $banque->nom_banque }}', {{ $banque->hasPaiements() ? 'true' : 'false' }})"
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
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-university text-gray-400 text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-700 mb-2">Aucune banque trouvée</h3>
                                        <p class="text-gray-500 text-sm mb-4">Commencez par créer une banque pour ce
                                            prestataire</p>
                                        @can('banques_prestataires.create')
                                            <button
                                                onclick="window.location.href='{{ route('banques.create', $prestataire->id_prestataire) }}'"
                                                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all text-sm shadow-md">
                                                <i class="fas fa-plus mr-2"></i>Créer une banque
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($banques->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $banques->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </main>

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
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">Annuler</button>
                        @can('banques_prestataires.delete')
                            <button onclick="executeDelete()" id="deleteBtn"
                                class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium">Supprimer</button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const prestataireId = '{{ $prestataire->id_prestataire }}';
            let deleteBanqueId = null;

            window.toggleStatus = function(id, isActive) {
                const action = isActive ? 'désactiver' : 'activer';
                if (confirm(`Voulez-vous vraiment ${action} cette banque ?`)) {
                    fetch("{{ route('banques.toggle-statut', [':prestataire', ':banque']) }}".replace(':prestataire',
                            prestataireId).replace(':banque', id), {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) location.reload();
                            else alert(data.message || 'Erreur');
                        })
                        .catch(() => alert('Une erreur est survenue'));
                }
            }

            window.confirmDelete = function(id, nom, hasPaiements) {
                deleteBanqueId = id;
                if (hasPaiements) {
                    document.getElementById('deleteMessage').innerHTML =
                        '<strong class="text-red-600">Impossible de supprimer car elle possède des paiements associés.</strong>';
                    document.getElementById('deleteBtn').classList.add('hidden');
                } else {
                    document.getElementById('deleteMessage').textContent =
                        `Êtes-vous sûr de vouloir supprimer la banque "${nom}" ?`;
                    document.getElementById('deleteBtn').classList.remove('hidden');
                }
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            window.executeDelete = function() {
                if (!deleteBanqueId) return;
                fetch("{{ route('banques.destroy', [':prestataire', ':banque']) }}".replace(':prestataire', prestataireId)
                        .replace(':banque', deleteBanqueId), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else {
                            alert(data.message || 'Erreur');
                            closeDeleteModal();
                        }
                    })
                    .catch(() => {
                        alert('Une erreur est survenue');
                        closeDeleteModal();
                    });
            }

            window.closeDeleteModal = function() {
                document.getElementById('deleteModal').classList.add('hidden');
                deleteBanqueId = null;
            }

            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => applyFilters(), 500);
            });
            document.getElementById('statutFilter').addEventListener('change', applyFilters);

            function applyFilters() {
                const search = document.getElementById('searchInput').value;
                const statut = document.getElementById('statutFilter').value;
                const params = new URLSearchParams();
                if (search) params.append('search', search);
                if (statut) params.append('statut', statut);
                window.location.href = `?${params.toString()}`;
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeDeleteModal();
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
