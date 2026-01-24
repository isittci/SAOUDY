@extends('layouts.main')
@section('title', 'Banque - ' . $banque->nom_banque)
@section('breadcrumb')
    <a @can('prestataires.read') href="{{ route('prestataires.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Prestataires</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('prestataires.view-details') href="{{ route('prestataires.show', $prestataire->id_prestataire) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">{{ Str::limit($prestataire->raison_sociale_prestataire, 20) }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('banques_prestataires.read') href="{{ route('banques.index', $prestataire->id_prestataire) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Banques</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">{{ $banque->nom_banque }}</span>
@endsection

@section('content')
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    @can('banques_prestataires.read')
                        <a href="{{ route('banques.index', $prestataire->id_prestataire) }}"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan
                    <div>
                        <div class="flex items-center space-x-3 flex-wrap">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $banque->nom_banque }}</h1>
                            @if ($banque->code_banque)
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">{{ $banque->code_banque }}</span>
                            @endif
                            @if ($banque->actif_banque)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800"><i
                                        class="fas fa-check-circle mr-1"></i> Active</span>
                            @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800"><i
                                        class="fas fa-times-circle mr-1"></i> Inactive</span>
                            @endif
                        </div>
                        <p class="text-gray-600 mt-1"><i
                                class="fas fa-building mr-1"></i>{{ $prestataire->raison_sociale_prestataire }}</p>
                    </div>
                </div>

                @canany(['banques_prestataires.update', 'banques_prestataires.toggle-status',
                    'banques_prestataires.delete'])
                    <div class="flex items-center space-x-2 flex-wrap">
                        @can('banques_prestataires.update')
                            <button
                                onclick="window.location.href='{{ route('banques.edit', ['prestataireId' => $prestataire->id_prestataire, 'banque' => $banque->id_banque]) }}'"
                                class="px-4 py-2.5 bg-white border border-orange-300 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                <i class="fas fa-edit text-sm"></i>
                                <span class="text-sm font-medium">Modifier</span>
                            </button>
                        @endcan

                        @can('banques_prestataires.toggle-status')
                            <button onclick="toggleStatus({{ $banque->actif_banque ? 'true' : 'false' }})"
                                class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                <i class="fas fa-power-off text-sm"></i>
                                <span class="text-sm font-medium">{{ $banque->actif_banque ? 'Désactiver' : 'Activer' }}</span>
                            </button>
                        @endcan

                        @can('banques_prestataires.delete')
                            <button onclick="confirmDelete()"
                                class="px-4 py-2.5 bg-white border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                <i class="fas fa-trash text-sm"></i>
                                <span class="text-sm font-medium">Supprimer</span>
                            </button>
                        @endcan
                    </div>
                @endcanany
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-university text-orange-500 mr-2"></i>
                            Informations bancaires
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gradient-to-br from-blue-50 to-white p-5 rounded-xl border border-blue-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Nom de la banque</span>
                                    <i class="fas fa-university text-blue-500"></i>
                                </div>
                                <p class="text-xl font-bold text-gray-900">{{ $banque->nom_banque }}</p>
                                @if ($banque->code_banque)
                                    <p class="text-sm text-gray-500 mt-1">Code: {{ $banque->code_banque }}</p>
                                @endif
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-white p-5 rounded-xl border border-green-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Titulaire</span>
                                    <i class="fas fa-user text-green-500"></i>
                                </div>
                                <p class="text-xl font-bold text-gray-900">
                                    {{ $banque->titulaire_compte_banque ?? 'Non renseigné' }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-white p-5 rounded-xl border border-purple-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Numéro de compte</span>
                                    <i class="fas fa-credit-card text-purple-500"></i>
                                </div>
                                <p class="text-xl font-bold text-gray-900 font-mono">
                                    {{ $banque->numero_compte_banque ?? 'Non renseigné' }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-yellow-50 to-white p-5 rounded-xl border border-yellow-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Code guichet</span>
                                    <i class="fas fa-hashtag text-yellow-500"></i>
                                </div>
                                <p class="text-xl font-bold text-gray-900 font-mono">
                                    {{ $banque->code_guichet_banque ?? 'Non renseigné' }}</p>
                            </div>
                        </div>
                        @if ($banque->rib_complet)
                            <div class="mt-6 p-6 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-indigo-100 text-sm font-medium">RIB Complet</p>
                                        <p class="text-2xl font-bold mt-1 font-mono tracking-wider">
                                            {{ $banque->rib_complet }}</p>
                                    </div>
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                        <i class="fas fa-id-card text-3xl"></i>
                                    </div>
                                </div>
                                @if ($banque->cle_rib_banque)
                                    <p class="text-indigo-200 text-sm mt-2">Clé RIB: {{ $banque->cle_rib_banque }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-globe text-blue-500 mr-2"></i>
                            Informations internationales
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">IBAN</label>
                            @if ($banque->iban_banque)
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-blue-100 text-blue-700 font-mono">{{ $banque->iban_banque }}</span>
                            @else
                                <p class="text-gray-500">Non renseigné</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">SWIFT/BIC</label>
                            @if ($banque->swift_bic_banque)
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-purple-100 text-purple-700 font-mono">{{ $banque->swift_bic_banque }}</span>
                            @else
                                <p class="text-gray-500">Non renseigné</p>
                            @endif
                        </div>
                        <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-200">
                            <div class="text-center">
                                <div
                                    class="w-12 h-12 mx-auto rounded-full flex items-center justify-center {{ $stats['rib_complet'] ? 'bg-green-100' : 'bg-gray-100' }}">
                                    <i
                                        class="fas {{ $stats['rib_complet'] ? 'fa-check text-green-500' : 'fa-times text-gray-400' }}"></i>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">RIB Complet</p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="w-12 h-12 mx-auto rounded-full flex items-center justify-center {{ $stats['has_iban'] ? 'bg-green-100' : 'bg-gray-100' }}">
                                    <i
                                        class="fas {{ $stats['has_iban'] ? 'fa-check text-green-500' : 'fa-times text-gray-400' }}"></i>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">IBAN</p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="w-12 h-12 mx-auto rounded-full flex items-center justify-center {{ $stats['has_swift'] ? 'bg-green-100' : 'bg-gray-100' }}">
                                    <i
                                        class="fas {{ $stats['has_swift'] ? 'fa-check text-green-500' : 'fa-times text-gray-400' }}"></i>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">SWIFT</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div
                        class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-money-check-alt text-green-500 mr-2"></i>
                            Paiements associés
                        </h2>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">{{ $stats['nombre_paiements'] }}
                            paiement(s)</span>
                    </div>
                    <div class="p-6">
                        @if ($banque->paiements && $banque->paiements->count() > 0)
                            <div class="space-y-3">
                                @foreach ($banque->paiements as $paiement)
                                    <div
                                        class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-4">
                                            <div
                                                class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-receipt text-green-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">
                                                    {{ number_format($paiement->montant_net_paye_paiement, 2, ',', ' ') }}
                                                    FCFA</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $paiement->created_at->format('d/m/Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-green-700">Montant total</span>
                                    <span
                                        class="text-lg font-bold text-green-600">{{ number_format($stats['montant_total_paiements'], 2, ',', ' ') }}
                                        FCFA</span>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-money-bill-wave text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Aucun paiement associé</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-building text-purple-500 mr-2"></i>
                            Prestataire
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-building text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $prestataire->raison_sociale_prestataire }}</p>
                                <p class="text-sm text-gray-500">{{ $prestataire->ville_prestataire ?? '' }}</p>
                            </div>
                        </div>
                        @can('prestataires.view-details')
                            <a href="{{ route('prestataires.show', $prestataire->id_prestataire) }}"
                                class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors text-sm font-medium">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                Voir le prestataire
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-cog text-gray-500 mr-2"></i>
                            Informations système
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Créée le</span>
                            <span
                                class="text-sm font-medium text-gray-900">{{ $banque->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($banque->createur)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-500">Créée par</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $banque->createur->nom_complet ?? 'N/A' }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Modifiée le</span>
                            <span
                                class="text-sm font-medium text-gray-900">{{ $banque->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($banque->modificateur)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-500">Modifiée par</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $banque->modificateur->nom_complet ?? 'N/A' }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                @canany(['banques_prestataires.duplicate', 'banques_prestataires.update'])
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-bolt text-blue-500 mr-2"></i>
                                Actions rapides
                            </h2>
                        </div>
                        <div class="p-4 space-y-2">
                            {{-- @can('banques_prestataires.duplicate')
                                <button onclick="duplicate()"
                                    class="w-full flex items-center justify-between p-3 text-gray-700 hover:bg-purple-50 rounded-lg transition-colors group">
                                    <span class="flex items-center"><i
                                            class="fas fa-copy text-purple-500 mr-3"></i>Dupliquer</span>
                                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-500"></i>
                                </button>
                            @endcan --}}

                            @can('banques_prestataires.update')
                                <button
                                    onclick="window.location.href='{{ route('banques.edit', ['prestataireId' => $prestataire->id_prestataire, 'banque' => $banque->id_banque]) }}'"
                                    class="w-full flex items-center justify-between p-3 text-gray-700 hover:bg-orange-50 rounded-lg transition-colors group">
                                    <span class="flex items-center"><i
                                            class="fas fa-edit text-orange-500 mr-3"></i>Modifier</span>
                                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-500"></i>
                                </button>
                            @endcan
                        </div>
                    </div>
                @endcanany
            </div>
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
                    @if ($banque->hasPaiements())
                        <p class="text-sm text-red-600 mb-6"><strong>Impossible de supprimer cette banque car elle possède
                                des paiements associés.</strong></p>
                        <button onclick="closeDeleteModal()"
                            class="px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-200 font-medium">Fermer</button>
                    @else
                        <p class="text-sm text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer la banque
                            <strong>{{ $banque->nom_banque }}</strong> ?</p>
                        <div class="flex items-center justify-center space-x-3">
                            <button onclick="closeDeleteModal()"
                                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">Annuler</button>
                            @can('banques_prestataires.delete')
                                <button onclick="executeDelete()"
                                    class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium">Supprimer</button>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    @can('banques_prestataires.view-details')
        @push('scripts')
            <script>
                const prestataireId = '{{ $prestataire->id_prestataire }}';
                const banqueId = '{{ $banque->id_banque }}';

                window.toggleStatus = function(isActive) {
                    const action = isActive ? 'désactiver' : 'activer';
                    if (confirm(`Voulez-vous vraiment ${action} cette banque ?`)) {
                        fetch("{{ route('banques.toggle-statut', [':prestataireId', ':banqueId']) }}".replace(':prestataireId', prestataireId).replace(':banqueId', banqueId), {
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

                window.confirmDelete = function() {
                    document.getElementById('deleteModal').classList.remove('hidden');
                }
                window.closeDeleteModal = function() {
                    document.getElementById('deleteModal').classList.add('hidden');
                }

                window.executeDelete = function() {
                    fetch("{{ route('banques.destroy', [':prestataireId', ':banque']) }}".replace(':prestataireId',
                            prestataireId).replace(':banque', banqueId), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) window.location.href =
                                '{{ route('banques.index', $prestataire->id_prestataire) }}';
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

                // window.duplicate = function() {
                //     if (confirm('Voulez-vous dupliquer cette banque ?')) {
                //         fetch("{{ route('banques.dupliquer', [':prestataireId', ':banque']) }}".replace(':prestataireId',
                //                 prestataireId).replace(':banque', banqueId), {
                //                 method: 'POST',
                //                 headers: {
                //                     'X-CSRF-TOKEN': '{{ csrf_token() }}',
                //                     'Content-Type': 'application/json',
                //                     'Accept': 'application/json'
                //                 }
                //             })
                //             .then(r => r.json())
                //             .then(data => {
                //                 if (data.success) window.location.href =
                //                     "{{ route('banques.edit', [':prestataireId', ':banque']) }}".replace(':prestataireId',
                //                         prestataireId).replace(':banque', data.data.id_banque);
                //                 else alert(data.message || 'Erreur');
                //             })
                //             .catch(() => alert('Une erreur est survenue'));
                //     }
                // }

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

                @media print {
                    .no-print {
                        display: none !important;
                    }
                }
            </style>
        @endpush
    @endcan
@endsection
