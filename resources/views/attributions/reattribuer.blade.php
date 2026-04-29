@extends('layouts.main')
@section('title', 'Réattribuer le lot')
@section('breadcrumb')
    <a href="{{ route('attributions.index') }}" class="text-white/80 hover:text-white transition-colors">Attributions</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Réattribution</span>
@endsection

@section('content')
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('attributions.show', $attribution->id_attribution) }}"
                    class="p-2 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Réattribuer le Lot</h1>
                    <p class="text-gray-600 mt-1">Lot {{ $attribution->lot->numero ?? 'N/A' }} - Ancienne:
                        {{ $attribution->numero_attribution }}</p>
                </div>
            </div>
        </div>
    </div>

    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <ul class="text-sm text-red-600 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $facture = $attribution->parentAttribution->proforma->facture;
            $montant_net_paye_paiement = $facture ? $facture->paiementsValides->sum('montant_net_paye_paiement') : 0;
            $montant_reste_paiement = $facture ? $facture->montant_facture - $montant_net_paye_paiement : 0;
        @endphp

        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
            <p class="text-yellow-800 font-medium"><i class="fas fa-info-circle mr-2"></i>Réattribution en cours</p>
            <p class="text-sm text-yellow-700 mt-1">Le lot <strong>{{ $attribution->lot->numero ?? 'N/A' }}</strong> sera
                réattribué avec une nouvelle proforma. L'ancienne attribution devient historique.</p>
        </div>

        <form action="{{ route('attributions.reattribuer', $attribution->id_attribution) }}" method="POST"
            id="reattributionForm">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Motif -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b">
                            <h2 class="text-lg font-bold text-gray-800"><i
                                    class="fas fa-exclamation-circle text-red-500 mr-2"></i>Motif de réattribution <span class="text-red-500">*</span></label></h2>
                        </div>
                        <div class="p-6">
                            <textarea name="motif_reattribution" rows="3" required minlength="10"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-red-400"
                                placeholder="Pourquoi réattribuez-vous ce lot ? (minimum 10 caractères)">{{ old('motif_reattribution') }}</textarea>
                        </div>
                    </div>

                    <!-- Nouveau prestataire -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b">
                            <h2 class="text-lg font-bold text-gray-800"><i
                                    class="fas fa-user-plus text-green-500 mr-2"></i>Nouveau prestataire</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Sélectionner le prestataire
                                    <span class="text-red-500">*</span></label>
                                <select name="prestataire_id" id="prestataire_id" required
                                    class="tom-select-prestataire w-full">
                                    <option value="">Rechercher un prestataire...</option>
                                    @foreach ($prestataires as $p)
                                        <option value="{{ $p->id_prestataire }}"
                                            {{ old('prestataire_id') == $p->id_prestataire ? 'selected' : '' }}
                                            data-ville="{{ $p->ville_prestataire }}"
                                            data-telephone="{{ $p->telephone_principal_prestataire }}"
                                            data-email="{{ $p->email_prestataire }}"
                                            data-ncc="{{ $p->numero_rccm_prestataire }}"
                                            data-adresse="{{ $p->adresse_prestataire }}"
                                            data-contact="{{ $p->contact_prestataire }}"
                                            data-ancien="{{ $p->id_prestataire == $attribution->prestataire_id ? '1' : '0' }}">
                                            {{ $p->raison_sociale_prestataire }}
                                            @if ($p->ville_prestataire)
                                                - {{ $p->ville_prestataire }}
                                            @endif
                                            @if ($p->id_prestataire == $attribution->prestataire_id)
                                                (Ancien prestataire)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Carte informations prestataire sélectionné -->
                            <div id="prestataire_info_card"
                                class="hidden bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 p-5">
                                <div class="flex items-start justify-between mb-4">
                                    <h3 class="font-semibold text-green-800"><i
                                            class="fas fa-building mr-2"></i>Informations du prestataire</h3>
                                    <span id="badge_ancien"
                                        class="hidden px-3 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded-full">
                                        <i class="fas fa-history mr-1"></i>Ancien prestataire
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div class="space-y-3">
                                        <div class="flex items-center">
                                            <i class="fas fa-id-card text-green-500 w-5"></i>
                                            <span class="text-gray-600 ml-2">NCC:</span>
                                            <span id="info_ncc" class="ml-2 font-medium text-gray-800">-</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-phone text-green-500 w-5"></i>
                                            <span class="text-gray-600 ml-2">Téléphone:</span>
                                            <span id="info_telephone" class="ml-2 font-medium text-gray-800">-</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope text-green-500 w-5"></i>
                                            <span class="text-gray-600 ml-2">Email:</span>
                                            <span id="info_email" class="ml-2 font-medium text-gray-800">-</span>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt text-green-500 w-5"></i>
                                            <span class="text-gray-600 ml-2">Ville:</span>
                                            <span id="info_ville" class="ml-2 font-medium text-gray-800">-</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-user text-green-500 w-5"></i>
                                            <span class="text-gray-600 ml-2">Contact:</span>
                                            <span id="info_contact" class="ml-2 font-medium text-gray-800">-</span>
                                        </div>
                                        <div class="flex items-start">
                                            <i class="fas fa-home text-green-500 w-5 mt-0.5"></i>
                                            <span class="text-gray-600 ml-2">Adresse:</span>
                                            <span id="info_adresse" class="ml-2 font-medium text-gray-800">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nouvelle Proforma -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b">
                            <h2 class="text-lg font-bold text-gray-800"><i
                                    class="fas fa-file-invoice text-purple-500 mr-2"></i>Nouvelle Proforma</h2>
                            <p class="text-sm text-gray-500 mt-1">Une nouvelle proforma sera créée pour cette réattribution
                            </p>
                        </div>
                        <div class="p-6">
                            <div
                                class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl border border-purple-200 overflow-hidden">
                                <div class="px-5 py-4 bg-purple-100/50 border-b border-purple-200">
                                    <h3 class="font-semibold text-purple-800"><i
                                            class="fas fa-file-medical mr-2"></i>Informations de la proforma</h3>
                                    <p class="text-xs text-purple-600 mt-1">Champs * obligatoires</p>
                                </div>
                                <div class="p-5 space-y-5">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Numéro
                                                proforma <span class="text-red-500">*</span></label></label>
                                            <input type="text" name="new_numero_proforma" id="new_numero_proforma"
                                                value="{{ old('new_numero_proforma') }}" maxlength="35" required
                                                placeholder="PROF-{{ date('Y') }}-0001"
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-400">
                                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-magic mr-1"></i>Format
                                                auto: PROF-{{ date('Y') }}-0001</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date proforma
                                                <span class="text-red-500">*</span></label>
                                            <input type="date" name="new_date_proforma" id="new_date_proforma"
                                                value="{{ old('new_date_proforma', date('Y-m-d')) }}"
                                                max="{{ date('Y-m-d') }}" required
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-400">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        {{-- <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date de redémarrage</label>
                                            <input type="date" name="new_date_redemarrage" id="new_date_redemarrage"
                                                value="{{ old('new_date_redemarrage') }}"
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-400">
                                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-play-circle mr-1"></i>Date prévue de démarrage des travaux</p>
                                        </div> --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Montant hors taxe
                                                HT (FCFA) <span class="text-red-500">*</span></label>
                                            <input type="text" name="new_montant_retenu_display"
                                                id="new_montant_retenu_display"
                                                value="{{ old('new_montant_retenu', number_format(floor($montant_reste_paiement), 0, ',', ' ')) }}"
                                                required
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-400"
                                                placeholder="0">
                                            <input type="hidden" name="new_montant_retenu" id="new_montant_retenu"
                                                value="{{ number_format(floor(old('new_montant_retenu', $montant_reste_paiement), 0, ',', ' ')) }}">
                                        </div>
                                    </div>

                                    <!-- TVA et Remise -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div class="bg-white/60 p-4 rounded-lg border border-purple-100">
                                            <div class="flex items-center justify-between mb-3">
                                                <label class="block text-sm font-semibold text-gray-700"><i
                                                        class="fas fa-percentage text-orange-500 mr-1"></i>TVA</label>
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="checkbox" id="exoneration_tva"
                                                        class="form-checkbox h-4 w-4 text-blue-600 rounded">
                                                    <span class="ml-2 text-xs font-medium text-gray-700">Exonération</span>
                                                </label>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-500 mb-1">Taux TVA (%)</label>
                                                <div class="relative">
                                                    <input type="number" id="new_tva_pourcentage"
                                                        value="{{ old('new_taux_tva', 18) }}" min="0"
                                                        max="100" step="0.01" name="new_taux_tva"
                                                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-400 pr-10 disabled:bg-gray-100 disabled:cursor-not-allowed">
                                                    <span
                                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">%</span>
                                                </div>
                                                <p class="text-xs text-gray-400 mt-1 italic">Pour calcul automatique</p>
                                            </div>
                                            <div class="mt-3">
                                                <label class="block text-xs text-gray-500 mb-1">Montant TVA (FCFA)</label>
                                                <input type="number" name="new_taxe_montant" id="new_taxe_montant"
                                                    value="{{ old('new_taxe_montant', 0) }}" min="0" readonly
                                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-400 bg-orange-50 font-semibold">
                                                <p class="text-xs text-orange-600 mt-1"><i
                                                        class="fas fa-info-circle mr-1"></i>Modifiable si besoin</p>
                                            </div>
                                        </div>
                                        <div class="bg-white/60 p-4 rounded-lg border border-purple-100">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3"><i
                                                    class="fas fa-tags text-green-500 mr-1"></i>Remise</label>
                                            <div>
                                                <label class="block text-xs text-gray-500 mb-1">Taux remise (%)</label>
                                                <div class="relative">
                                                    <input type="number" id="new_remise_pourcentage"
                                                        value="{{ old('new_remise_pourcentage', 0) }}" min="0"
                                                        max="100" step="0.01"
                                                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-400 pr-10">
                                                    <span
                                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">%</span>
                                                </div>
                                                <p class="text-xs text-gray-400 mt-1 italic">Pour calcul automatique</p>
                                            </div>
                                            <div class="mt-3">
                                                <label class="block text-xs text-gray-500 mb-1">Montant remise
                                                    (FCFA)</label>
                                                <input type="number" name="new_remise_montant" id="new_remise_montant"
                                                    value="{{ old('new_remise_montant', 0) }}" min="0" readonly
                                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-400 bg-green-50 font-semibold">
                                                <p class="text-xs text-green-600 mt-1"><i
                                                        class="fas fa-info-circle mr-1"></i>Modifiable si besoin</p>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="grid grid-cols-1 md:grid-cols-1 gap-5">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Modalités
                                                paiement</label>
                                            <input type="text" name="new_modalite" id="new_modalite"
                                                value="{{ old('new_modalite') }}"
                                                placeholder="Ex: 30% commande, 70% livraison"
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-400">
                                        </div>
                                    </div>

                                    <!-- Récap calculs -->
                                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl p-5 text-white">
                                        <h4 class="font-semibold mb-4"><i class="fas fa-calculator mr-2"></i>Récapitulatif
                                            Proforma</h4>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between"><span class="text-purple-200">Montant
                                                    HT</span><span id="calc_montant_ht">0 FCFA</span></div>
                                            <div class="flex justify-between"><span class="text-purple-200">TVA (<span
                                                        id="calc_tva_pct">18</span>%)</span><span id="calc_tva"
                                                    class="text-orange-300">+ 0 FCFA</span></div>
                                            <div class="flex justify-between"><span class="text-purple-200">Remise (<span
                                                        id="calc_remise_pct">0</span>%)</span><span id="calc_remise"
                                                    class="text-green-300">- 0 FCFA</span></div>
                                            <hr class="border-purple-400 my-2">
                                            <div class="flex justify-between text-lg"><span class="font-semibold">Total
                                                    TTC</span><span id="calc_ttc" class="font-bold text-yellow-300">0
                                                    FCFA</span></div>
                                            <input type="hidden" name="new_total_ttc" id="new_total_ttc">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b">
                            <h2 class="text-lg font-bold text-gray-800"><i
                                    class="fas fa-calendar-alt text-blue-500 mr-2"></i>Planification</h2>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date attribution <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="date_attribution" id="date_attribution"
                                    value="{{ old('date_attribution', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                                    required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date début <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="date_debut_prevue" id="date_debut_prevue"
                                    value="{{ old('date_debut_prevue', date('Y-m-d')) }}" required
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date fin <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="date_fin_prevue" id="date_fin_prevue"
                                    value="{{ old('date_fin_prevue') }}" required
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                                <p id="dureeCalculee" class="text-xs text-blue-600 mt-1"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Observations -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b">
                            <h2 class="text-lg font-bold text-gray-800"><i
                                    class="fas fa-comment-alt text-gray-500 mr-2"></i>Observations</h2>
                        </div>
                        <div class="p-6">
                            <textarea name="observations" rows="3"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-gray-400"
                                placeholder="Notes complémentaires...">{{ old('observations') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Lot -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b">
                            <h2 class="text-lg font-bold text-gray-800"><i class="fas fa-box text-indigo-500 mr-2"></i>Lot
                                concerné</h2>
                        </div>
                        <div class="p-6 space-y-3">
                            <div>
                                <span class="text-sm text-gray-600">Numéro:</span>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700 ml-2">{{ $attribution->lot->numero ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Libellé:</span>
                                <p class="text-gray-800 font-medium mt-1">{{ $attribution->lot->libelle ?? 'N/A' }}</p>
                            </div>
                            @if ($attribution->lot->appelOffre)
                                <div>
                                    <span class="text-sm text-gray-600">Appel d'offre:</span>
                                    <p class="text-gray-700 text-sm mt-1">
                                        {{ $attribution->lot->appelOffre->numero_appel_offre }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Ancienne attribution -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-100 to-white border-b">
                            <h2 class="text-lg font-bold text-gray-800"><i
                                    class="fas fa-history text-gray-500 mr-2"></i>Ancienne attribution</h2>
                        </div>
                        <div class="p-6 space-y-3 text-sm">
                            <div class="flex justify-between"><span class="text-gray-600">N°:</span><span
                                    class="font-medium">{{ $attribution->numero_attribution }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Version:</span><span
                                    class="font-medium">v{{ $attribution->version_attribution }}</span></div>
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600">Prestataire:</span>
                                <span
                                    class="font-medium text-right max-w-[150px]">{{ $attribution->prestataire->raison_sociale_prestataire ?? 'N/A' }}</span>
                            </div>
                            @if ($attribution->proforma)
                                <div class="flex justify-between"><span class="text-gray-600">Proforma:</span><span
                                        class="font-medium">{{ $attribution->proforma->numero_proforma }}</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Montant TTC:</span><span
                                        class="font-medium">{{ number_format(floor($attribution->proforma->montant_ttc) ?? 0, 0, ',', ' ') }}
                                        FCFA</span></div>
                            @endif
                            <hr class="border-gray-200 my-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Montant payé:</span>
                                <span
                                    class="font-semibold text-green-600">{{ number_format(floor($montant_net_paye_paiement), 0, ',', ' ') }}
                                    FCFA</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Reste à payer:</span>
                                <span
                                    class="font-semibold text-orange-600">{{ number_format(floor($montant_reste_paiement), 0, ',', ' ') }}
                                    FCFA</span>
                            </div>
                        </div>
                    </div>

                    <!-- Finances -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b">
                            <h2 class="text-lg font-bold text-gray-800"><i
                                    class="fas fa-coins text-green-500 mr-2"></i>Montant engagé</h2>
                        </div>
                        <div class="p-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Montant engagé (FCFA)</label>
                            <input type="number" name="montant_engage" id="montant_engage"
                                value="{{ old('montant_engage', 0) }}" min="0" readonly
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-400">
                            <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i>Sera auto-rempli
                                avec le Total TTC de la proforma</p>
                        </div>
                    </div>
                </div>
            </div>

             @canany(['attributions_lots.reassign', 'attributions_lots.view-details'])
                <div class="p-6">
                    <!-- Boutons -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex gap-3">
                            @can('attributions_lots.reassign')
                            <button type="submit"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium rounded-lg shadow-md flex items-center justify-center transition-all">
                                <i class="fas fa-redo mr-2"></i>Réattribuer le lot
                            </button>
                            @endcan
                            @can('attributions_lots.view-details')
                            <a href="{{ route('attributions.show', $attribution->id_attribution) }}"
                                class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg flex items-center justify-center transition-all">
                                <i class="fas fa-times mr-2"></i>Annuler
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            @endcanany
        </form>
    </main>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-wrapper {
            width: 100%;
            position: relative;
            z-index: 1;
        }

        .ts-control {
            padding: 0.75rem 1rem !important;
            border-radius: 0.5rem !important;
        }

        /* Fix z-index pour le dropdown Tom Select */
        .ts-wrapper.dropdown-active {
            z-index: 9999;
        }

        .ts-dropdown {
            z-index: 10000 !important;
            position: absolute !important;
        }

        .ts-dropdown-content {
            max-height: 300px;
            overflow-y: auto;
        }

        /* Fix pour les conteneurs avec overflow hidden */
        .bg-white.rounded-2xl {
            overflow: visible !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fonction de formatage des nombres
            function fmt(m) {
                return new Intl.NumberFormat('fr-FR').format(Math.round(m));
            }

            // Initialisation Tom Select pour le prestataire
            const prestataireSelect = new TomSelect('#prestataire_id', {
                placeholder: 'Rechercher un prestataire...',
                searchField: ['text'],
                maxOptions: 100,
                render: {
                    option: function(data, escape) {
                        const el = data.$option;
                        const ville = el?.dataset?.ville || '';
                        const telephone = el?.dataset?.telephone || '';
                        const email = el?.dataset?.email || '';
                        const ancien = el?.dataset?.ancien === '1';

                        let html = '<div class="py-2">';
                        html += '<div class="font-medium">' + escape(data.text.split(' - ')[0]);
                        if (ancien) {
                            html +=
                                ' <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full ml-2">Ancien</span>';
                        }
                        html += '</div>';
                        if (ville) html +=
                            '<div class="text-xs text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i>' +
                            escape(ville) + '</div>';
                        if (telephone) html +=
                            '<div class="text-xs text-gray-500"><i class="fas fa-phone mr-1"></i>' +
                            escape(telephone) + '</div>';
                        if (email) html +=
                            '<div class="text-xs text-gray-500"><i class="fas fa-envelope mr-1"></i>' +
                            escape(email) + '</div>';
                        html += '</div>';
                        return html;
                    },
                    item: function(data, escape) {
                        return '<div>' + escape(data.text.split(' - ')[0]) + '</div>';
                    }
                },
                onChange: function(value) {
                    updatePrestataireInfo(value);
                }
            });

            // Mise à jour des infos prestataire
            function updatePrestataireInfo(value) {
                const card = document.getElementById('prestataire_info_card');
                const badgeAncien = document.getElementById('badge_ancien');

                if (!value) {
                    card.classList.add('hidden');
                    return;
                }

                const option = document.querySelector('#prestataire_id option[value="' + value + '"]');
                if (!option) {
                    card.classList.add('hidden');
                    return;
                }

                // Afficher la carte
                card.classList.remove('hidden');

                // Remplir les infos
                document.getElementById('info_ncc').textContent = option.dataset.ncc || '-';
                document.getElementById('info_telephone').textContent = option.dataset.telephone || '-';
                document.getElementById('info_email').textContent = option.dataset.email || '-';
                document.getElementById('info_ville').textContent = option.dataset.ville || '-';
                document.getElementById('info_contact').textContent = option.dataset.contact || '-';
                document.getElementById('info_adresse').textContent = option.dataset.adresse || '-';

                // Badge ancien prestataire
                if (option.dataset.ancien === '1') {
                    badgeAncien.classList.remove('hidden');
                } else {
                    badgeAncien.classList.add('hidden');
                }
            }

            // Éléments de calcul proforma
            const montantRetenu = document.getElementById('new_montant_retenu');
            const montantRetenuDisplay = document.getElementById('new_montant_retenu_display');
            const tvaPct = document.getElementById('new_tva_pourcentage');
            const taxeMontant = document.getElementById('new_taxe_montant');
            const remisePct = document.getElementById('new_remise_pourcentage');
            const remiseMontant = document.getElementById('new_remise_montant');
            const exonerationTva = document.getElementById('exoneration_tva');

            // Formatage automatique du montant HT avec séparateur de milliers
            montantRetenuDisplay.addEventListener('input', function(e) {
                // Supprimer tous les caractères non numériques
                let value = e.target.value.replace(/[^\d]/g, '');

                // Mettre à jour la valeur cachée (sans formatage)
                montantRetenu.value = value;

                // Formatter avec séparateur de milliers
                if (value) {
                    e.target.value = parseInt(value).toLocaleString('fr-FR');
                } else {
                    e.target.value = '';
                }

                // Recalculer la proforma
                calcProforma();
            });

            // Empêcher la saisie de caractères non numériques
            montantRetenuDisplay.addEventListener('keypress', function(e) {
                if (!/[\d]/.test(e.key)) {
                    e.preventDefault();
                }
            });

            // Gestion de l'exonération TVA
            exonerationTva.addEventListener('change', function() {
                if (this.checked) {
                    // Si on coche exonération = PAS DE TVA
                    tvaPct.value = '0'; // Mettre à 0
                    tvaPct.disabled = true; // Désactiver le champ
                    calcProforma(); // Recalculer
                } else {
                    // Si on décoche exonération = TVA NORMALE
                    tvaPct.disabled = false; // Activer le champ
                    tvaPct.value = '18'; // Remettre la valeur par défaut
                    tvaPct.focus(); // Mettre le focus pour faciliter la modification
                    calcProforma(); // Recalculer
                }
            });

            // Calcul automatique de la proforma
            function calcProforma() {
                const ht = parseFloat(montantRetenu.value) || 0;
                const tva = parseFloat(tvaPct.value) || 0;
                const rem = parseFloat(remisePct.value) || 0;

                const taxe = Math.round(ht * tva / 100);
                const remise = Math.round(ht * rem / 100);
                const ttc = ht + taxe - remise;

                taxeMontant.value = taxe;
                remiseMontant.value = remise;

                document.getElementById('calc_montant_ht').textContent = fmt(ht) + ' FCFA';
                document.getElementById('calc_tva_pct').textContent = tva;
                document.getElementById('calc_tva').textContent = '+ ' + fmt(taxe) + ' FCFA';
                document.getElementById('calc_remise_pct').textContent = rem;
                document.getElementById('calc_remise').textContent = '- ' + fmt(remise) + ' FCFA';
                document.getElementById('calc_ttc').textContent = fmt(ttc) + ' FCFA';
                document.getElementById('new_total_ttc').value = ttc;


                // Mettre à jour le montant engagé
                document.getElementById('montant_engage').value = ttc;
            }

            montantRetenu.addEventListener('input', calcProforma);
            tvaPct.addEventListener('input', calcProforma);
            remisePct.addEventListener('input', calcProforma);

            // Calcul de la durée
            function updateDuree() {
                const debut = new Date(document.getElementById('date_debut_prevue').value);
                const fin = new Date(document.getElementById('date_fin_prevue').value);
                const dureeEl = document.getElementById('dureeCalculee');

                if (document.getElementById('date_debut_prevue').value &&
                    document.getElementById('date_fin_prevue').value && fin > debut) {
                    const diff = Math.ceil(Math.abs(fin - debut) / (1000 * 60 * 60 * 24));
                    dureeEl.textContent = '📅 Durée: ' + diff + ' jour' + (diff > 1 ? 's' : '');
                } else {
                    dureeEl.textContent = '';
                }
            }

            // Gestion des dates
            document.getElementById('date_attribution').addEventListener('change', function() {
                const dd = document.getElementById('date_debut_prevue');
                dd.min = this.value;
                if (dd.value && dd.value < this.value) dd.value = this.value;
            });

            document.getElementById('date_debut_prevue').addEventListener('change', function() {
                const df = document.getElementById('date_fin_prevue');
                df.min = this.value;
                if (df.value && df.value <= this.value) {
                    const d = new Date(this.value);
                    d.setDate(d.getDate() + 30);
                    df.value = d.toISOString().split('T')[0];
                }
                updateDuree();
            });

            document.getElementById('date_fin_prevue').addEventListener('change', updateDuree);

            // Initialisation
            calcProforma();
            updateDuree();

            // Si un prestataire est déjà sélectionné (old value)
            const selectedPrestataire = document.getElementById('prestataire_id').value;
            if (selectedPrestataire) {
                updatePrestataireInfo(selectedPrestataire);
            }
        });
    </script>
@endpush
