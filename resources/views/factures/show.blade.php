@extends('layouts.main')

@section('title', 'Facture ' . $facture->numero_facture)

@section('breadcrumb')
    <a href="{{ route('factures.index') }}" class="text-white/80 hover:text-white transition-colors">Factures</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">{{ $facture->numero_facture }}</span>
@endsection

@section('content')
    <!-- Header avec actions -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('factures.index') }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <div>
                        <div class="flex items-center space-x-3 flex-wrap gap-2">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $facture->numero_facture }}</h1>
                            @php
                                $statutClasses = [
                                    'en_attente' => 'bg-yellow-100 text-yellow-800',
                                    'validee' => 'bg-blue-100 text-blue-800',
                                    'rejetee' => 'bg-red-100 text-red-800',
                                    'payee' => 'bg-green-100 text-green-800',
                                    'partiellement_payee' => 'bg-orange-100 text-orange-800',
                                    'annulee' => 'bg-gray-100 text-gray-800',
                                ];
                                $statutIcons = [
                                    'en_attente' => 'clock',
                                    'validee' => 'check-circle',
                                    'rejetee' => 'times-circle',
                                    'payee' => 'check-double',
                                    'partiellement_payee' => 'adjust',
                                    'annulee' => 'ban',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statutClasses[$facture->statut_facture] ?? 'bg-gray-100 text-gray-800' }}">
                                <i class="fas fa-{{ $statutIcons[$facture->statut_facture] ?? 'question' }} mr-1"></i>
                                {{ $facture->statut_libelle }}
                            </span>
                        </div>
                        <p class="text-gray-600 mt-1">
                            Proforma: {{ $facture->proforma->numero_proforma ?? 'Non définie' }}
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2 flex-wrap gap-2">
                    @if($facture->peutEtreModifiee())
                        <a href="{{ route('factures.edit', $facture->id_facture) }}"
                            class="px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md">
                            <i class="fas fa-edit text-sm"></i>
                            <span class="text-sm font-medium">Modifier</span>
                        </a>
                    @endif

                    @if($facture->peutEtreValidee())
                        <form action="{{ route('factures.valider', $facture->id_facture) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                <i class="fas fa-check text-sm"></i>
                                <span class="text-sm font-medium">Valider</span>
                            </button>
                        </form>
                    @endif

                    @if($facture->peutEtreRejetee())
                        <button onclick="openRejeterModal()"
                            class="px-4 py-2.5 bg-white border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-times text-sm"></i>
                            <span class="text-sm font-medium">Rejeter</span>
                        </button>
                    @endif

                    @if($facture->peutRecevoirPaiement())
                    {{-- {{ route('paiements.create', ['facture_id' => $facture->id_facture]) }} --}}
                        <a href="#"
                            class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-money-bill-wave text-sm"></i>
                            <span class="text-sm font-medium">Ajouter Paiement</span>
                        </a>
                    @endif

                    @if($facture->statut_facture === 'rejetee')
                        <form action="{{ route('factures.remettre-en-attente', $facture->id_facture) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                <i class="fas fa-redo text-sm"></i>
                                <span class="text-sm font-medium">Remettre en attente</span>
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('factures.dupliquer', $facture->id_facture) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2.5 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-copy text-sm"></i>
                            <span class="text-sm font-medium">Dupliquer</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @include('partials.alerts')

        <!-- Alerte si facture rejetée -->
        @if($facture->statut_facture === 'rejetee' && $facture->comment_facture)
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-red-800 font-semibold mb-2">Facture Rejetée</h3>
                        <p class="text-red-700 text-sm">{{ $facture->comment_facture }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Informations de la facture -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-invoice-dollar text-orange-500 mr-2"></i>
                            Informations de la Facture
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Numéro de facture</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $facture->numero_facture }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Montant TTC</label>
                                <p class="text-2xl font-bold text-orange-600">{{ $facture->montant_formate }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Date de facture</label>
                                <p class="text-gray-900">{{ $facture->date_facture->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Date de réception</label>
                                <p class="text-gray-900">{{ $facture->date_reception_facture->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        @if($facture->comment_facture && $facture->statut_facture !== 'rejetee')
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <label class="block text-sm font-medium text-gray-500 mb-2">Commentaire</label>
                                <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $facture->comment_facture }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Progression du paiement -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-chart-pie text-green-500 mr-2"></i>
                            Progression du Paiement
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-sm text-gray-500">Montant payé</p>
                                <p class="text-2xl font-bold text-green-600">
                                    {{ number_format($statistiquesPaiements['montant_total_paye'], 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Reste à payer</p>
                                <p class="text-2xl font-bold text-red-600">
                                    {{ number_format($statistiquesPaiements['montant_restant'], 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                        </div>

                        <!-- Barre de progression -->
                        <div class="relative pt-1">
                            <div class="flex mb-2 items-center justify-between">
                                <div>
                                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full {{ $statistiquesPaiements['pourcentage_paye'] >= 100 ? 'text-green-600 bg-green-200' : 'text-orange-600 bg-orange-200' }}">
                                        {{ $statistiquesPaiements['pourcentage_paye'] }}% payé
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold text-gray-600">
                                        {{ $statistiquesPaiements['total'] }} paiement(s)
                                    </span>
                                </div>
                            </div>
                            <div class="overflow-hidden h-3 text-xs flex rounded-full bg-gray-200">
                                <div style="width: {{ $statistiquesPaiements['pourcentage_paye'] }}%"
                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $statistiquesPaiements['pourcentage_paye'] >= 100 ? 'bg-green-500' : 'bg-orange-500' }} transition-all duration-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des paiements -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-white border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-money-check-alt text-emerald-500 mr-2"></i>
                            Paiements
                            <span class="ml-2 px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-full">
                                {{ $facture->paiements->count() }}
                            </span>
                        </h2>
                        @if($facture->peutRecevoirPaiement())
                        {{-- {{ route('paiements.create', ['facture_id' => $facture->id_facture]) }} --}}
                            <a href="#"
                                class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm rounded-lg transition-colors">
                                <i class="fas fa-plus mr-1"></i> Ajouter
                            </a>
                        @endif
                    </div>

                    @if($facture->paiements->isEmpty())
                        <div class="p-8 text-center">
                            <i class="fas fa-money-check-alt text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Aucun paiement enregistré</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Référence</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Banque</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Montant</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($facture->paiements as $paiement)
                                        @php
                                            $paiementStatutClasses = [
                                                0 => 'bg-yellow-100 text-yellow-800',
                                                1 => 'bg-blue-100 text-blue-800',
                                                2 => 'bg-indigo-100 text-indigo-800',
                                                3 => 'bg-green-100 text-green-800',
                                                4 => 'bg-red-100 text-red-800',
                                                5 => 'bg-gray-100 text-gray-800',
                                            ];
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="font-medium text-gray-900">{{ $paiement->reference_paiement }}</span>
                                                <p class="text-xs text-gray-500">{{ $paiement->created_at->format('d/m/Y') }}</p>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">{{ $paiement->banque->nom_banque ?? 'N/A' }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <span class="font-bold text-gray-900">{{ $paiement->montant_formate }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paiementStatutClasses[$paiement->statut_paiement] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $paiement->statut_libelle }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <a href="{{ route('paiements.show', $paiement->id_paiement) }}"
                                                    class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors inline-flex">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">

                <!-- Proforma associée -->
                @if($facture->proforma)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-file-alt text-indigo-500 mr-2"></i>
                                Proforma
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Numéro</span>
                                <span class="font-semibold text-gray-900">{{ $facture->proforma->numero_proforma }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Montant HT</span>
                                <span class="text-gray-900">{{ number_format($facture->proforma->montant_retenu_proforma, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">TVA (18%)</span>
                                <span class="text-gray-900">{{ number_format($facture->proforma->taxe_montant, 0, ',', ' ') }} FCFA</span>
                            </div>
                            @if($facture->proforma->remise_montant_proforma > 0)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Remise</span>
                                    <span class="text-green-600">-{{ number_format($facture->proforma->remise_montant_proforma, 0, ',', ' ') }} FCFA</span>
                                </div>
                            @endif
                            @if($facture->proforma->penalites_proforma > 0)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Pénalités</span>
                                    <span class="text-red-600">+{{ number_format($facture->proforma->penalites_proforma, 0, ',', ' ') }} FCFA</span>
                                </div>
                            @endif
                            <div class="pt-4 border-t border-gray-200">
                                <a href="{{ route('proformas.show', $facture->proforma->id_proforma) }}"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    Voir la proforma
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Résumé rapide -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-purple-500 mr-2"></i>
                            Résumé
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Facture soldée</span>
                            @if($facture->est_soldee)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Oui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times mr-1"></i> Non
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Modifiable</span>
                            @if($facture->peutEtreModifiee())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Oui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-lock mr-1"></i> Non
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Paiements</span>
                            <span class="font-semibold text-gray-900">{{ $statistiquesPaiements['total'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Audit -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-fingerprint text-gray-500 mr-2"></i>
                            Audit
                        </h2>
                    </div>
                    <div class="p-6 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Créé le</span>
                            <span class="text-gray-900">{{ $facture->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($facture->createur)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Créé par</span>
                                <span class="text-gray-900">{{ $facture->createur->nom_complet ?? 'N/A' }}</span>
                            </div>
                        @endif
                        @if($facture->updated_at && $facture->updated_at != $facture->created_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Modifié le</span>
                                <span class="text-gray-900">{{ $facture->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                        @if($facture->modificateur)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Modifié par</span>
                                <span class="text-gray-900">{{ $facture->modificateur->nom_complet ?? 'N/A' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Rejeter -->
    <div id="rejeterModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeRejeterModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-times-circle text-red-500 mr-2"></i>Rejeter la facture
                    </h3>
                </div>
                <form action="{{ route('factures.rejeter', $facture->id_facture) }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="bg-red-50 p-4 rounded-lg text-sm text-red-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            Cette action marquera la facture comme rejetée. Le prestataire devra soumettre une nouvelle facture corrigée.
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Motif du rejet *</label>
                            <textarea name="motif" rows="4" required minlength="10"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400 focus:border-transparent"
                                placeholder="Expliquez la raison du rejet (minimum 10 caractères)..."></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeRejeterModal()" class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">
                            Rejeter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Annuler -->
    <div id="annulerModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeAnnulerModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-100 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-ban text-gray-500 mr-2"></i>Annuler la facture
                    </h3>
                </div>
                <form action="{{ route('factures.annuler', $facture->id_facture) }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="bg-yellow-50 p-4 rounded-lg text-sm text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Cette action annulera définitivement la facture. Cette opération est irréversible.
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Motif d'annulation *</label>
                            <textarea name="motif" rows="4" required minlength="10"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 focus:border-transparent"
                                placeholder="Expliquez la raison de l'annulation (minimum 10 caractères)..."></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeAnnulerModal()" class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">
                            Retour
                        </button>
                        <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                            Confirmer l'annulation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openRejeterModal() {
        document.getElementById('rejeterModal').classList.remove('hidden');
    }
    function closeRejeterModal() {
        document.getElementById('rejeterModal').classList.add('hidden');
    }
    function openAnnulerModal() {
        document.getElementById('annulerModal').classList.remove('hidden');
    }
    function closeAnnulerModal() {
        document.getElementById('annulerModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRejeterModal();
            closeAnnulerModal();
        }
    });
</script>
@endpush
