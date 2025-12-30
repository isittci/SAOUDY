@extends('layouts.main')
@section('title', 'Détails Paiement - ' . $paiement->reference_paiement)
@section('breadcrumb')
    <a href="{{ route('factures.index') }}" class="text-white/80 hover:text-white transition-colors">Factures</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('factures.show', $factureId) }}" class="text-white/80 hover:text-white transition-colors">{{ $paiement->facture->numero_facture }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('paiements.index', ['factureId' => $factureId]) }}" class="text-white/80 hover:text-white transition-colors">Paiements</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">{{ $paiement->reference_paiement }}</span>
@endsection

@section('content')
    <!-- Header avec actions -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et retour -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('paiements.index', ['factureId' => $factureId]) }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    


                    <div>
                        <div class="flex items-center space-x-3 flex-wrap">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $paiement->reference_paiement }}</h1>
                            @php
                                $couleurs = [0 => 'yellow', 1 => 'blue', 2 => 'indigo', 3 => 'green', 4 => 'red', 5 => 'gray'];
                                $couleur = $couleurs[$paiement->statut_paiement] ?? 'gray';

                                $icones = [
                                    0 => 'fas fa-clock',           // En attente
                                    1 => 'fas fa-check-circle',    // Validé
                                    2 => 'fas fa-spinner fa-spin', // En traitement (avec animation)
                                    3 => 'fas fa-check-double',    // Payé
                                    4 => 'fas fa-times-circle',    // Rejeté
                                    5 => 'fas fa-ban',             // Annulé
                                ];
                                $icone = $icones[$paiement->statut_paiement] ?? 'fas fa-question-circle';
                            @endphp

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-{{ $paiement->statut_couleur }}-100 text-{{ $paiement->statut_couleur }}-800">
                                <i class="{{ $icone }} mr-1"></i>
                                {{ $paiement->statut_libelle }}
                            </span>
                        </div>
                        <p class="text-gray-600 mt-1">Facture: {{ $paiement->facture->numero_facture }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2 flex-wrap">
                    @if($paiement->peutEtreValide())
                        <button onclick="valider()"
                            class="px-4 py-2.5 bg-white border border-blue-300 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-check text-sm"></i>
                            <span class="text-sm font-medium">Valider</span>
                        </button>
                    @endif

                    @if($paiement->statut_paiement == 1)
                        <button onclick="mettreEnTraitement()"
                            class="px-4 py-2.5 bg-white border border-indigo-300 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-spinner text-sm"></i>
                            <span class="text-sm font-medium">Traitement</span>
                        </button>
                    @endif

                    @if(in_array($paiement->statut_paiement, [1, 2]))
                        <button onclick="confirmer()"
                            class="px-4 py-2.5 bg-white border border-green-300 text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-check-double text-sm"></i>
                            <span class="text-sm font-medium">Confirmer</span>
                        </button>
                    @endif

                    @if($paiement->peutEtreModifie())
                        <a href="{{ route('paiements.edit', ['factureId' => $factureId, 'paiement' => $paiement->id_paiement]) }}"
                            class="px-4 py-2.5 bg-white border border-orange-300 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-edit text-sm"></i>
                            <span class="text-sm font-medium">Modifier</span>
                        </a>
                    @endif

                    <!-- Menu dropdown -->
                    <div class="relative">
                        <button onclick="toggleMenu()" id="menuBtn"
                            class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-ellipsis-v text-sm"></i>
                        </button>
                        <div id="actionMenu"
                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-20">
                            <div class="py-1">
                                @if($paiement->peutEtreRejete())
                                    <button onclick="showRejectModal()"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                        <i class="fas fa-times-circle mr-2 text-red-500"></i>
                                        Rejeter
                                    </button>
                                @endif
                                @if($paiement->peutEtreAnnule())
                                    <button onclick="showCancelModal()"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                        <i class="fas fa-ban mr-2 text-gray-500"></i>
                                        Annuler
                                    </button>
                                @endif
                                @if($paiement->statut_paiement == 4)
                                    <button onclick="remettreEnAttente()"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                        <i class="fas fa-undo mr-2 text-yellow-500"></i>
                                        Remettre en attente
                                    </button>
                                @endif
                                <a href="{{ route('factures.show', $paiement->facture_id) }}"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-file-invoice mr-2 text-purple-500"></i>
                                    Voir la facture
                                </a>
                                @if($paiement->statut_paiement != 3)
                                    <button onclick="confirmDelete()"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                        <i class="fas fa-trash mr-2"></i>
                                        Supprimer
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages -->
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
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Informations principales -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-green-500 mr-2"></i>
                            Informations du paiement
                        </h2>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-semibold text-gray-600 flex items-center mb-2">
                                    <i class="fas fa-hashtag text-gray-400 mr-2"></i>
                                    Référence
                                </label>
                                <p class="text-base font-bold text-gray-900">{{ $paiement->reference_paiement }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-gray-600 flex items-center mb-2">
                                    <i class="fas fa-money-bill-wave text-gray-400 mr-2"></i>
                                    Montant payé
                                </label>
                                <p class="text-2xl font-bold text-green-600">
                                    {{ number_format($paiement->montant_net_paye_paiement, 0, ',', ' ') }} FCFA
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-gray-600 flex items-center mb-2">
                                    <i class="fas fa-file-invoice text-gray-400 mr-2"></i>
                                    Facture
                                </label>
                                <a href="{{ route('factures.show', $paiement->facture_id) }}"
                                    class="text-base font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $paiement->facture->numero_facture }}
                                </a>
                                {{-- {{ dd($paiement) }} --}}
                                <p class="text-sm text-gray-500 mt-1">
                                    Montant facture: {{ number_format($paiement->facture->montant_facture ?? 0, 0, ',', ' ') }} FCFA
                                </p>
                            </div>

                            {{-- <div>
                                <label class="text-sm font-semibold text-gray-600 flex items-center mb-2">
                                    <i class="fas fa-building text-gray-400 mr-2"></i>
                                    Prestataire
                                </label>
                                <p class="text-base font-medium text-gray-900">
                                    {{ $paiement->facture->marche->prestataire->raison_sociale_prestataire ?? 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    IFU: {{ $paiement->facture->marche->prestataire->ifu_prestataire ?? 'N/A' }}
                                </p>
                            </div> --}}

                            <div>
                                <label class="text-sm font-semibold text-gray-600 flex items-center mb-2">
                                    <i class="fas fa-university text-gray-400 mr-2"></i>
                                    Banque destinataire
                                </label>
                                <p class="text-base font-medium text-gray-900">{{ $paiement->banque->nom_banque }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{-- {{ dd($paiement->banque->numero_compte_masque, $paiement->banque) }} --}}
                                    Compte: {{ $paiement->banque->numero_compte_banque }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-gray-600 flex items-center mb-2">
                                    <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                    Date de création
                                </label>
                                <p class="text-base text-gray-900">{{ $paiement->created_at->format('d/m/Y à H:i') }}</p>
                            </div>

                            @if($paiement->date_validation_paiement)
                                <div>
                                    <label class="text-sm font-semibold text-gray-600 flex items-center mb-2">
                                        <i class="fas fa-check-circle text-gray-400 mr-2"></i>
                                        Date de validation
                                    </label>
                                    <p class="text-base text-gray-900">{{ $paiement->date_validation_paiement->format('d/m/Y à H:i') }}</p>
                                </div>
                            @endif
                        </div>

                        @if($paiement->observations_paiement)
                            <div class="pt-4 border-t border-gray-200">
                                <label class="text-sm font-semibold text-gray-600 flex items-center mb-2">
                                    <i class="fas fa-comment-alt text-gray-400 mr-2"></i>
                                    Observations
                                </label>
                                <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-lg">
                                    {{ $paiement->observations_paiement }}
                                </p>
                            </div>
                        @endif

                        @if($paiement->motif_rejet_paiement)
                            <div class="pt-4 border-t border-gray-200">
                                <label class="text-sm font-semibold text-red-600 flex items-center mb-2">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Motif de rejet
                                </label>
                                <p class="text-sm text-red-700 bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                                    {{ $paiement->motif_rejet_paiement }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations bancaires détaillées -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-university text-blue-500 mr-2"></i>
                            Informations bancaires
                        </h2>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600 mb-1 block">Banque</label>
                                <p class="text-base text-gray-900">{{ $paiement->banque->nom_banque }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600 mb-1 block">Code banque</label>
                                <p class="text-base text-gray-900">{{ $paiement->banque->code_banque ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600 mb-1 block">Numéro de compte</label>
                                <p class="text-base font-mono text-gray-900">{{ $paiement->banque->numero_compte_banque }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600 mb-1 block">Titulaire</label>
                                <p class="text-base text-gray-900">{{ $paiement->banque->titulaire_compte_banque ?? 'N/A' }}</p>
                            </div>
                            @if($paiement->banque->iban_banque)
                                <div class="md:col-span-2">
                                    <label class="text-sm font-semibold text-gray-600 mb-1 block">IBAN</label>
                                    <p class="text-base font-mono text-gray-900">{{ $paiement->banque->iban_banque }}</p>
                                </div>
                            @endif
                            @if($paiement->banque->swift_bic_banque)
                                <div>
                                    <label class="text-sm font-semibold text-gray-600 mb-1 block">SWIFT/BIC</label>
                                    <p class="text-base font-mono text-gray-900">{{ $paiement->banque->swift_bic_banque }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">
                <!-- Statut et workflow -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-tasks text-gray-500 mr-2"></i>
                            Workflow
                        </h2>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Statut actuel</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-{{ $couleur }}-100 text-{{ $couleur }}-800">
                                {{ $paiement->statut_libelle }}
                            </span>
                        </div>

                        @if($paiement->validateur)
                            <div class="pt-3 border-t border-gray-200">
                                <label class="text-xs font-semibold text-gray-500 uppercase mb-2 block">Validé par</label>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $paiement->validateur->nom_complet }}</p>
                                        @if($paiement->date_validation_paiement)
                                            <p class="text-xs text-gray-500">{{ $paiement->date_validation_paiement->format('d/m/Y H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($paiement->payeur)
                            <div class="pt-3 border-t border-gray-200">
                                <label class="text-xs font-semibold text-gray-500 uppercase mb-2 block">Payé par</label>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $paiement->payeur->nom_complet }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($paiement->createur)
                            <div class="pt-3 border-t border-gray-200">
                                <label class="text-xs font-semibold text-gray-500 uppercase mb-2 block">Créé par</label>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-gray-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $paiement->createur->nom_complet }}</p>
                                        <p class="text-xs text-gray-500">{{ $paiement->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations facture -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-invoice text-purple-500 mr-2"></i>
                            Facture liée
                        </h2>
                    </div>

                    <div class="p-6 space-y-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Numéro</label>
                            <p class="text-sm font-medium text-gray-900">{{ $paiement->facture->numero_facture }}</p>
                        </div>
                        {{-- <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Marché</label>
                            <p class="text-sm text-gray-900">{{ $paiement->facture->marche->numero_marche }}</p>
                        </div> --}}
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Montant facture</label>

                            <p class="text-sm font-bold text-gray-900">
                                {{ number_format($paiement->facture->montant_facture ?? 0, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                        <div class="pt-3 border-t border-gray-200">
                            <a href="{{ route('factures.show', $paiement->facture_id) }}"
                                class="block w-full px-4 py-2 bg-purple-50 text-purple-600 text-center rounded-lg hover:bg-purple-100 transition-all text-sm font-medium">
                                <i class="fas fa-eye mr-2"></i>
                                Voir la facture
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-bolt text-orange-500 mr-2"></i>
                            Actions rapides
                        </h2>
                    </div>

                    <div class="p-4 space-y-2">
                        @if($paiement->peutEtreValide())
                            <button onclick="valider()"
                                class="w-full px-4 py-2.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-all text-sm font-medium">
                                <i class="fas fa-check mr-2"></i>
                                Valider le paiement
                            </button>
                        @endif

                        @if($paiement->statut_paiement == 1)
                            <button onclick="mettreEnTraitement()"
                                class="w-full px-4 py-2.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-all text-sm font-medium">
                                <i class="fas fa-spinner mr-2"></i>
                                Mettre en traitement
                            </button>
                        @endif

                        @if(in_array($paiement->statut_paiement, [1, 2]))
                            <button onclick="confirmer()"
                                class="w-full px-4 py-2.5 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-all text-sm font-medium">
                                <i class="fas fa-check-double mr-2"></i>
                                Confirmer paiement
                            </button>
                        @endif

                        <a href="{{ route('paiements.index', ['factureId' => $factureId]) }}"
                            class="block w-full px-4 py-2.5 bg-gray-50 text-gray-600 text-center rounded-lg hover:bg-gray-100 transition-all text-sm font-medium">
                            <i class="fas fa-list mr-2"></i>
                            Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Rejet -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Rejeter le paiement</h3>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Motif du rejet *</label>
                <textarea id="rejectMotif" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400 focus:border-transparent"
                    placeholder="Expliquez pourquoi ce paiement est rejeté (minimum 10 caractères)"></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button onclick="closeRejectModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                    Annuler
                </button>
                <button onclick="executeReject()"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all">
                    Confirmer le rejet
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Annulation -->
    <div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Annuler le paiement</h3>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Motif de l'annulation *</label>
                <textarea id="cancelMotif" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 focus:border-transparent"
                    placeholder="Expliquez pourquoi ce paiement est annulé (minimum 10 caractères)"></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button onclick="closeCancelModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                    Annuler
                </button>
                <button onclick="executeCancel()"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all">
                    Confirmer l'annulation
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Suppression -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Confirmer la suppression</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700">Êtes-vous sûr de vouloir supprimer ce paiement ?</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                    Annuler
                </button>
                <button onclick="executeDelete()"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all">
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const factureId = '{{ $factureId }}';
            const paiementId = '{{ $paiement->id_paiement }}';

            function toggleMenu() {
                document.getElementById('actionMenu').classList.toggle('hidden');
            }

            function valider() {
                if (confirm('Voulez-vous valider ce paiement ?')) {
                    fetch("{{ route('paiements.valider', [':factureId', ':paiementId']) }}".replace(':factureId', factureId).replace(':paiementId', paiementId), {
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

            function mettreEnTraitement() {
                if (confirm('Mettre ce paiement en traitement bancaire ?')) {
                    fetch("{{ route('paiements.traitement', [':factureId', ':paiementId']) }}".replace(':factureId', factureId).replace(':paiementId', paiementId), {
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

            function confirmer() {
                if (confirm('Confirmer que ce paiement a été effectué ?')) {
                    fetch("{{ route('paiements.confirmer', [':factureId', ':paiementId']) }}".replace(':factureId', factureId).replace(':paiementId', paiementId), {
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

            function showRejectModal() {
                document.getElementById('rejectMotif').value = '';
                document.getElementById('rejectModal').classList.remove('hidden');
                document.getElementById('actionMenu').classList.add('hidden');
            }

            function closeRejectModal() {
                document.getElementById('rejectModal').classList.add('hidden');
            }

            function executeReject() {
                const motif = document.getElementById('rejectMotif').value.trim();
                if (motif.length < 10) {
                    alert('Le motif doit contenir au moins 10 caractères');
                    return;
                }

                fetch("{{ route('paiements.rejeter', [':factureId', ':paiementId']) }}".replace(':factureId', factureId).replace(':paiementId', paiementId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ motif_rejet: motif })
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

            function showCancelModal() {
                document.getElementById('cancelMotif').value = '';
                document.getElementById('cancelModal').classList.remove('hidden');
                document.getElementById('actionMenu').classList.add('hidden');
            }

            function closeCancelModal() {
                document.getElementById('cancelModal').classList.add('hidden');
            }

            function executeCancel() {
                const motif = document.getElementById('cancelMotif').value.trim();
                if (motif.length < 10) {
                    alert('Le motif doit contenir au moins 10 caractères');
                    return;
                }

                fetch("{{ route('paiements.annuler', [':factureId', ':paiementId']) }}".replace(':factureId', factureId).replace(':paiementId', paiementId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ motif_annulation: motif })
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

            function remettreEnAttente() {
                if (confirm('Remettre ce paiement en attente ?')) {
                    fetch("{{ route('paiements.remettre-attente', [':factureId', ':paiementId']) }}".replace(':factureId', factureId).replace(':paiementId', paiementId), {
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

            function confirmDelete() {
                document.getElementById('deleteModal').classList.remove('hidden');
                document.getElementById('actionMenu').classList.add('hidden');
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
            }

            function executeDelete() {
                fetch("{{ route('paiements.destroy', [':factureId', ':paiementId']) }}".replace(':factureId', factureId).replace(':paiementId', paiementId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = `/${factureId}/paiements`;
                    } else {
                        alert(data.message);
                        closeDeleteModal();
                    }
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeRejectModal();
                    closeCancelModal();
                    closeDeleteModal();
                    document.getElementById('actionMenu').classList.add('hidden');
                }
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('#actionMenu') && !e.target.closest('#menuBtn')) {
                    document.getElementById('actionMenu').classList.add('hidden');
                }
            });
        </script>

        <style>
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fadeIn {
                animation: fadeIn 0.3s ease-out;
            }
        </style>
    @endpush
@endsection
