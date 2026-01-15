@extends('layouts.main')
@section('title', 'Paiements supprimés')
@section('breadcrumb')
    <a @can('factures.read') href="{{ route('factures.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Factures</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('factures.view-details') href="{{ route('factures.show', $factureId) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">{{ $facture->numero_facture }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('paiements.read') href="{{ route('paiements.index', ['factureId' => $factureId]) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Paiements</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Corbeille</span>
@endsection

@section('content')
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    @can('paiements.read')
                        <a href="{{ route('paiements.index', ['factureId' => $factureId]) }}"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-trash-restore text-red-500"></i>
                        <span>Paiements supprimés</span>
                    </h1>
                </div>

                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Rechercher..." value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400" />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Facture -->
            <div class="mt-4 bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-file-invoice mr-2"></i>
                    Facture : <a @can('factures.view-details') href="{{ route('factures.show', $factureId) }}" @endcan
                        class="font-bold hover:underline">{{ $facture->numero_facture }}</a>
                </p>
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

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Paiements dans la corbeille ({{ $paiements->total() }})
                    </h2>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Référence</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Montant</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Banque</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Supprimé le</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Par</th>
                            @canany(['paiements.update', 'paiements.delete'])
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($paiements as $paiement)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $paiement->reference_paiement }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                    {{ number_format($paiement->montant_net_paye_paiement, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $paiement->banque->nom_banque ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    {{ $paiement->deleted_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-900">
                                    {{ $paiement->suppresseur->nom_complet ?? 'N/A' }}
                                </td>
                                @canany(['paiements.update', 'paiements.delete'])
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            @can('paiements.update')
                                                <button onclick="restore('{{ $paiement->id_paiement }}')"
                                                    class="text-green-600 hover:text-green-800 transition-colors" title="Restaurer">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            @endcan

                                            @can('paiements.delete')
                                                @if ($paiement->statut_paiement != 3)
                                                    <button onclick="confirmForceDelete('{{ $paiement->id_paiement }}')"
                                                        class="text-red-600 hover:text-red-800 transition-colors"
                                                        title="Supprimer définitivement">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center text-gray-500">
                                        <i class="fas fa-trash text-5xl mb-4 text-gray-300"></i>
                                        <p class="text-lg font-medium">La corbeille est vide</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($paiements->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $paiements->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- Modal Suppression définitive -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Suppression définitive</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700">Êtes-vous sûr de vouloir supprimer définitivement ce paiement ? Cette action est
                    irréversible.</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                    Annuler
                </button>
                @can('paiements.update')
                <button onclick="executeForceDelete()"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all">
                    Supprimer définitivement
                </button>
                @endcan
            </div>
        </div>
    </div>

    @can('paiements.update')
    @push('scripts')
        <script>
            const factureId = '{{ $factureId }}';
            let currentPaiementId = null;

            function restore(id) {
                if (confirm('Restaurer ce paiement ?')) {
                    fetch("{{ route('paiements.restore', [':factureId', ':id']) }}".replace(':factureId', factureId).replace(
                            ':id', id), {
                            method: 'POST',
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
                                alert(data.message);
                            }
                        });
                }
            }

            function confirmForceDelete(id) {
                currentPaiementId = id;
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
                currentPaiementId = null;
            }

            function executeForceDelete() {
                fetch("{{ route('paiements.force-delete', [':factureId', ':currentPaiementId']) }}".replace(':factureId',
                        factureId).replace(':currentPaiementId', currentPaiementId), {
                        method: 'DELETE',
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
                            alert(data.message);
                            closeDeleteModal();
                        }
                    });
            }

            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const params = new URLSearchParams();
                    if (e.target.value) params.append('search', e.target.value);
                    window.location.href = `/${factureId}/paiements/trashed?${params.toString()}`;
                }, 500);
            });

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
        </style>
    @endpush
    @endcan
@endsection
