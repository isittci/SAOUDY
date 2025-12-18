@extends('layouts.main')
@section('title', 'Nouvelle Attribution')
@section('breadcrumb')
    <a href="{{ route('attributions.index') }}" class="text-white/80 hover:text-white transition-colors">Attributions</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Nouvelle attribution</span>
@endsection

@section('content')
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('attributions.index') }}" class="p-2 hover:bg-gray-100 rounded-lg"><i class="fas fa-arrow-left text-gray-600"></i></a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Nouvelle Attribution de Lot</h1>
                    <p class="text-gray-600 mt-1">Attribuez un lot à un prestataire</p>
                </div>
            </div>
        </div>
    </div>

    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <ul class="text-sm text-red-600 list-disc list-inside">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('attributions.store') }}" method="POST" id="attributionForm">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">

                    <!-- Lot et Prestataire -->
                    <div class="bg-white rounded-2xl shadow-lg">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b rounded-t-2xl"><h2 class="text-lg font-bold text-gray-800"><i class="fas fa-link text-orange-500 mr-2"></i>Informations principales</h2></div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Lot à attribuer <span class="text-red-500">*</span></label>
                                <select name="lot_id" id="lot_id" required class="tom-select-lot w-full">
                                    <option value="">Rechercher un lot...</option>
                                    @foreach($lots as $lot)
                                        <option value="{{ $lot->id_lot }}" {{ old('lot_id', $lotPreselectionne->id_lot ?? '') == $lot->id_lot ? 'selected' : '' }} data-ao="{{ $lot->appelOffre->numero_appel_offre ?? '' }}">
                                            {{ $lot->numero }} - {{ Str::limit($lot->libelle, 50) }} ({{ $lot->appelOffre->numero_appel_offre ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Prestataire <span class="text-red-500">*</span></label>
                                <select name="prestataire_id" id="prestataire_id" required class="tom-select-prestataire w-full">
                                    <option value="">Rechercher un prestataire...</option>
                                    @foreach($prestataires as $prestataire)
                                        <option value="{{ $prestataire->id_prestataire }}" {{ old('prestataire_id') == $prestataire->id_prestataire ? 'selected' : '' }} data-ville="{{ $prestataire->ville_prestataire }}" data-telephone="{{ $prestataire->telephone_prestataire }}">
                                            {{ $prestataire->raison_sociale_prestataire }}@if($prestataire->ville_prestataire) - {{ $prestataire->ville_prestataire }}@endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Proforma -->
                    <div class="bg-white rounded-2xl shadow-lg">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b rounded-t-2xl"><h2 class="text-lg font-bold text-gray-800"><i class="fas fa-file-invoice text-purple-500 mr-2"></i>Proforma</h2></div>
                        <div class="p-6 space-y-5">
                            <!-- Choix mode -->
                            <div class="flex items-center space-x-6">
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="proforma_mode" value="select" id="mode_select" class="w-5 h-5 text-purple-600" {{ old('proforma_mode', 'select') === 'select' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-purple-600"><i class="fas fa-list mr-1"></i>Sélectionner existante</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="proforma_mode" value="create" id="mode_create" class="w-5 h-5 text-purple-600" {{ old('proforma_mode') === 'create' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-purple-600"><i class="fas fa-plus-circle mr-1"></i>Créer nouvelle</span>
                                </label>
                            </div>

                            <!-- Sélection existante -->
                            <div id="proforma_select_section" class="{{ old('proforma_mode') === 'create' ? 'hidden' : '' }}">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Proforma existante <span class="text-red-500">*</span></label>
                                <select name="proforma_id" id="proforma_id" class="tom-select-proforma w-full">
                                    <option value="">Rechercher une proforma...</option>
                                    @foreach($proformas as $proforma)
                                        <option value="{{ $proforma->id_proforma }}" {{ old('proforma_id') == $proforma->id_proforma ? 'selected' : '' }}
                                            data-montant="{{ $proforma->montant_retenu_proforma ?? 0 }}"
                                            data-taxe="{{ $proforma->taxe_montant ?? 0 }}"
                                            data-remise="{{ $proforma->remise_montant_proforma ?? 0 }}"
                                            data-ttc="{{ ($proforma->montant_retenu_proforma ?? 0) + ($proforma->taxe_montant ?? 0) - ($proforma->remise_montant_proforma ?? 0) }}">
                                            {{ $proforma->numero_proforma }} - {{ number_format(($proforma->montant_retenu_proforma ?? 0) + ($proforma->taxe_montant ?? 0) - ($proforma->remise_montant_proforma ?? 0), 0, ',', ' ') }} FCFA
                                        </option>
                                    @endforeach
                                </select>
                                <div id="proforma_preview" class="hidden mt-4 p-4 bg-purple-50 rounded-xl border border-purple-100">
                                    <h4 class="text-sm font-semibold text-purple-800 mb-3"><i class="fas fa-eye mr-1"></i>Aperçu</h4>
                                    <div class="grid grid-cols-4 gap-4 text-sm">
                                        <div><span class="text-gray-500">Montant HT</span><p id="preview_montant_ht" class="font-semibold">-</p></div>
                                        <div><span class="text-gray-500">TVA</span><p id="preview_taxe" class="font-semibold">-</p></div>
                                        <div><span class="text-gray-500">Remise</span><p id="preview_remise" class="font-semibold text-red-600">-</p></div>
                                        <div><span class="text-gray-500">Total TTC</span><p id="preview_ttc" class="font-bold text-purple-700 text-lg">-</p></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Création nouvelle -->
                            <div id="proforma_create_section" class="{{ old('proforma_mode') !== 'create' ? 'hidden' : '' }}">
                                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl border border-purple-200 overflow-hidden">
                                    <div class="px-5 py-4 bg-purple-100/50 border-b border-purple-200">
                                        <h3 class="font-semibold text-purple-800"><i class="fas fa-file-medical mr-2"></i>Nouvelle Proforma</h3>
                                        <p class="text-xs text-purple-600 mt-1">Champs * obligatoires</p>
                                    </div>
                                    <div class="p-5 space-y-5">
                                        <div class="grid grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Numéro proforma <span class="text-gray-400 text-xs font-normal">(optionnel)</span></label>
                                                <input type="text" name="new_numero_proforma" id="new_numero_proforma" value="{{ old('new_numero_proforma') }}" maxlength="20" placeholder="Auto-généré si vide" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-400">
                                                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-magic mr-1"></i>Format auto: PROF-2024-0001</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date proforma <span class="text-red-500">*</span></label>
                                                <input type="date" name="new_date_proforma" id="new_date_proforma" value="{{ old('new_date_proforma', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-400">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date de redémarrage <span class="text-red-500">*</span></label>
                                            <input type="date" name="new_date_redemarrage" id="new_date_redemarrage" value="{{ old('new_date_redemarrage') }}" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-400">
                                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-play-circle mr-1"></i>Date prévue de démarrage des travaux</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Montant retenu HT (FCFA) <span class="text-red-500">*</span></label>
                                            <input type="number" name="new_montant_retenu" id="new_montant_retenu" value="{{ old('new_montant_retenu', 0) }}" min="0" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-400">
                                        </div>
                                        <!-- TVA et Remise -->
                                        <div class="grid grid-cols-2 gap-5">
                                            <div class="bg-white/60 p-4 rounded-lg border border-purple-100">
                                                <label class="block text-sm font-semibold text-gray-700 mb-3"><i class="fas fa-percentage text-orange-500 mr-1"></i>TVA</label>
                                                <div>
                                                    <label class="block text-xs text-gray-500 mb-1">Taux TVA (%)</label>
                                                    <div class="relative">
                                                        <input type="number" id="new_tva_pourcentage" value="18" min="0" max="100" step="0.01" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-400 pr-10">
                                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">%</span>
                                                    </div>
                                                    <p class="text-xs text-gray-400 mt-1 italic">Pour calcul automatique</p>
                                                </div>
                                                <div class="mt-3">
                                                    <label class="block text-xs text-gray-500 mb-1">Montant TVA (FCFA)</label>
                                                    <input type="number" name="new_taxe_montant" id="new_taxe_montant" value="{{ old('new_taxe_montant', 0) }}" min="0" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-400 bg-orange-50 font-semibold">
                                                    <p class="text-xs text-orange-600 mt-1"><i class="fas fa-info-circle mr-1"></i>Modifiable si besoin</p>
                                                </div>
                                            </div>
                                            <div class="bg-white/60 p-4 rounded-lg border border-purple-100">
                                                <label class="block text-sm font-semibold text-gray-700 mb-3"><i class="fas fa-tags text-green-500 mr-1"></i>Remise</label>
                                                <div>
                                                    <label class="block text-xs text-gray-500 mb-1">Taux remise (%)</label>
                                                    <div class="relative">
                                                        <input type="number" id="new_remise_pourcentage" value="0" min="0" max="100" step="0.01" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-400 pr-10">
                                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">%</span>
                                                    </div>
                                                    <p class="text-xs text-gray-400 mt-1 italic">Pour calcul automatique</p>
                                                </div>
                                                <div class="mt-3">
                                                    <label class="block text-xs text-gray-500 mb-1">Montant remise (FCFA)</label>
                                                    <input type="number" name="new_remise_montant" id="new_remise_montant" value="{{ old('new_remise_montant', 0) }}" min="0" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-400 bg-green-50 font-semibold">
                                                    <p class="text-xs text-green-600 mt-1"><i class="fas fa-info-circle mr-1"></i>Modifiable si besoin</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Pénalités (FCFA)</label>
                                                <input type="number" name="new_penalites" id="new_penalites" value="{{ old('new_penalites', 0) }}" min="0" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-400">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Modalités paiement</label>
                                                <input type="text" name="new_modalite" id="new_modalite" value="{{ old('new_modalite') }}" placeholder="30% commande, 70% livraison" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-400">
                                            </div>
                                        </div>
                                        <!-- Récap calculs -->
                                        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl p-5 text-white">
                                            <h4 class="font-semibold mb-4"><i class="fas fa-calculator mr-2"></i>Récapitulatif</h4>
                                            <div class="space-y-2 text-sm">
                                                <div class="flex justify-between"><span class="text-purple-200">Montant HT</span><span id="calc_montant_ht">0 FCFA</span></div>
                                                <div class="flex justify-between"><span class="text-purple-200">TVA (<span id="calc_tva_pct">18</span>%)</span><span id="calc_tva" class="text-orange-300">+ 0 FCFA</span></div>
                                                <div class="flex justify-between"><span class="text-purple-200">Remise (<span id="calc_remise_pct">0</span>%)</span><span id="calc_remise" class="text-green-300">- 0 FCFA</span></div>
                                                <hr class="border-purple-400 my-2">
                                                <div class="flex justify-between text-lg"><span class="font-semibold">Total TTC</span><span id="calc_ttc" class="font-bold text-yellow-300">0 FCFA</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b"><h2 class="text-lg font-bold text-gray-800"><i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Planification</h2></div>
                        <div class="p-6">
                            <div class="grid grid-cols-3 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date attribution <span class="text-red-500">*</span></label>
                                    <input type="date" name="date_attribution" id="date_attribution" required value="{{ old('date_attribution', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date début <span class="text-red-500">*</span></label>
                                    <input type="date" name="date_debut_prevue" id="date_debut_prevue" required value="{{ old('date_debut_prevue') }}" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date fin <span class="text-red-500">*</span></label>
                                    <input type="date" name="date_fin_prevue" id="date_fin_prevue" required value="{{ old('date_fin_prevue') }}" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                                </div>
                            </div>
                            <div class="mt-4 p-3 bg-gray-50 rounded-lg flex justify-between"><span class="text-sm text-gray-600">Durée prévue:</span><span id="dureeCalculee" class="font-semibold">-</span></div>
                        </div>
                    </div>

                    <!-- Observations -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b"><h2 class="text-lg font-bold text-gray-800"><i class="fas fa-comment-alt text-gray-500 mr-2"></i>Informations complémentaires</h2></div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Observations</label>
                                <textarea name="observations" rows="3" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-gray-400" placeholder="Notes...">{{ old('observations') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Conditions particulières</label>
                                <textarea name="conditions_particulieres" rows="3" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-gray-400" placeholder="Conditions spécifiques...">{{ old('conditions_particulieres') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b"><h2 class="text-lg font-bold text-gray-800"><i class="fas fa-coins text-green-500 mr-2"></i>Paramètres financiers</h2></div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Montant engagé (FCFA)</label>
                                <input type="number" name="montant_engage" id="montant_engage" value="{{ old('montant_engage', 0) }}" min="0" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-400">
                                <p class="mt-1 text-xs text-gray-500">Auto-rempli depuis proforma</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Taux pénalités (%)</label>
                                <input type="number" name="taux_penalites" id="taux_penalites" value="{{ old('taux_penalites', 0) }}" min="0" max="100" step="0.01" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-400">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-orange-200"><h2 class="text-lg font-bold text-gray-800"><i class="fas fa-clipboard-check text-orange-500 mr-2"></i>Récapitulatif</h2></div>
                        <div class="p-6 space-y-3 text-sm">
                            <div class="flex justify-between"><span class="text-gray-600">Lot:</span><span id="recap_lot" class="font-medium truncate max-w-[150px]">-</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Prestataire:</span><span id="recap_prestataire" class="font-medium truncate max-w-[150px]">-</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Proforma:</span><span id="recap_proforma" class="font-medium">-</span></div>
                            <hr class="border-orange-200">
                            <div class="flex justify-between"><span class="text-gray-600">Durée:</span><span id="recap_duree" class="font-medium">-</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Montant:</span><span id="recap_montant" class="font-bold text-orange-700">-</span></div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 space-y-3">
                        <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium rounded-lg shadow-md flex items-center justify-center"><i class="fas fa-check mr-2"></i>Enregistrer</button>
                        <a href="{{ route('attributions.index') }}" class="w-full px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg flex items-center justify-center"><i class="fas fa-times mr-2"></i>Annuler</a>
                    </div>
                </div>
            </div>
        </form>
    </main>


@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    /* Correction z-index pour les dropdowns Tom Select */
    .ts-wrapper {
        position: relative;
        z-index: 10;
    }
    .ts-wrapper.single .ts-control {
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        border-color: #d1d5db;
        min-height: 50px;
        background-color: #fff;
    }
    .ts-wrapper.single .ts-control:hover {
        border-color: #f97316;
    }
    .ts-wrapper.focus .ts-control {
        border-color: #f97316;
        box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.2);
    }
    /* IMPORTANT: z-index élevé pour le dropdown */
    .ts-dropdown {
        z-index: 9999 !important;
        position: absolute !important;
        border-radius: 0.5rem;
        border-color: #e5e7eb;
        box-shadow: 0 10px 40px -5px rgba(0, 0, 0, 0.15);
        background-color: #fff;
        max-height: 300px;
        overflow-y: auto;
    }
    .ts-dropdown .option {
        padding: 0.75rem 1rem;
    }
    .ts-dropdown .option.active {
        background-color: #fff7ed;
        color: #c2410c;
    }
    .ts-dropdown .option:hover {
        background-color: #ffedd5;
    }
    /* Assurer que le wrapper parent ne coupe pas le dropdown */
    .ts-wrapper.dropdown-active {
        z-index: 9999;
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
    const lotSelect = new TomSelect('#lot_id', {placeholder:'Rechercher un lot...',searchField:['text'],maxOptions:50});
    const prestataireSelect = new TomSelect('#prestataire_id', {
        placeholder:'Rechercher un prestataire...',searchField:['text'],maxOptions:100,
        render:{
            option:function(d,e){const el=d.$option,v=el?.dataset?.ville||'',t=el?.dataset?.telephone||'';return '<div class="py-2"><div class="font-medium">'+e(d.text.split(' - ')[0])+'</div>'+(v?'<div class="text-xs text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i>'+e(v)+'</div>':'')+(t?'<div class="text-xs text-gray-500"><i class="fas fa-phone mr-1"></i>'+e(t)+'</div>':'')+'</div>';},
            item:function(d,e){return '<div>'+e(d.text.split(' - ')[0])+'</div>';}
        }
    });
    const proformaSelect = new TomSelect('#proforma_id', {
        placeholder:'Rechercher une proforma...',searchField:['text'],maxOptions:100,
        render:{option:function(d,e){const el=d.$option,ttc=el?.dataset?.ttc||0;return '<div class="py-2"><div class="font-medium">'+e(d.text.split(' - ')[0])+'</div><div class="text-sm text-purple-600 font-semibold">'+fmt(ttc)+' FCFA TTC</div></div>';}},
        onChange:function(){updatePreview();updateMontant();}
    });

    const modeSelect=document.getElementById('mode_select'),modeCreate=document.getElementById('mode_create');
    const selectSection=document.getElementById('proforma_select_section'),createSection=document.getElementById('proforma_create_section');
    const montantRetenu=document.getElementById('new_montant_retenu'),tvaPct=document.getElementById('new_tva_pourcentage');
    const taxeMontant=document.getElementById('new_taxe_montant'),remisePct=document.getElementById('new_remise_pourcentage');
    const remiseMontant=document.getElementById('new_remise_montant');

    function toggleMode(){
        const isSelect=modeSelect.checked;
        selectSection.classList.toggle('hidden',!isSelect);
        createSection.classList.toggle('hidden',isSelect);
        document.getElementById('proforma_id').required=isSelect;
        ['new_date_proforma','new_montant_retenu','new_date_redemarrage'].forEach(id=>{const el=document.getElementById(id);if(el)el.required=!isSelect;});
        updateRecap();updateMontant();
    }
    modeSelect.addEventListener('change',toggleMode);
    modeCreate.addEventListener('change',toggleMode);

    function calcProforma(){
        const ht=parseFloat(montantRetenu.value)||0,tva=parseFloat(tvaPct.value)||0,rem=parseFloat(remisePct.value)||0;
        const taxe=Math.round(ht*tva/100),remise=Math.round(ht*rem/100),ttc=ht+taxe-remise;
        taxeMontant.value=taxe;remiseMontant.value=remise;
        document.getElementById('calc_montant_ht').textContent=fmt(ht)+' FCFA';
        document.getElementById('calc_tva_pct').textContent=tva;
        document.getElementById('calc_tva').textContent='+ '+fmt(taxe)+' FCFA';
        document.getElementById('calc_remise_pct').textContent=rem;
        document.getElementById('calc_remise').textContent='- '+fmt(remise)+' FCFA';
        document.getElementById('calc_ttc').textContent=fmt(ttc)+' FCFA';
        if(modeCreate.checked){document.getElementById('montant_engage').value=ttc;updateRecap();}
    }
    montantRetenu.addEventListener('input',calcProforma);
    tvaPct.addEventListener('input',calcProforma);
    remisePct.addEventListener('input',calcProforma);

    function updatePreview(){
        const sel=document.getElementById('proforma_id'),opt=sel.options[sel.selectedIndex],prev=document.getElementById('proforma_preview');
        if(opt&&opt.value){
            document.getElementById('preview_montant_ht').textContent=fmt(opt.dataset.montant||0)+' FCFA';
            document.getElementById('preview_taxe').textContent=fmt(opt.dataset.taxe||0)+' FCFA';
            document.getElementById('preview_remise').textContent='- '+fmt(opt.dataset.remise||0)+' FCFA';
            document.getElementById('preview_ttc').textContent=fmt(opt.dataset.ttc||0)+' FCFA';
            prev.classList.remove('hidden');
        }else{prev.classList.add('hidden');}
    }

    function updateMontant(){
        if(modeSelect.checked){
            const sel=document.getElementById('proforma_id'),opt=sel.options[sel.selectedIndex];
            if(opt&&opt.value&&opt.dataset.ttc)document.getElementById('montant_engage').value=parseFloat(opt.dataset.ttc);
        }
        updateRecap();
    }

    function updateRecap(){
        const lotOpt=document.querySelector('#lot_id option:checked');
        document.getElementById('recap_lot').textContent=lotOpt&&lotOpt.value?lotOpt.text.split(' - ')[0]:'-';
        const prestOpt=document.querySelector('#prestataire_id option:checked');
        let pt=prestOpt&&prestOpt.value?prestOpt.text.split(' - ')[0]:'-';
        document.getElementById('recap_prestataire').textContent=pt.substring(0,25)+(pt.length>25?'...':'');
        if(modeSelect.checked){const profOpt=document.querySelector('#proforma_id option:checked');document.getElementById('recap_proforma').textContent=profOpt&&profOpt.value?profOpt.text.split(' - ')[0]:'-';}
        else{const num=document.getElementById('new_numero_proforma').value;document.getElementById('recap_proforma').textContent=num?num+' (nouvelle)':'Nouvelle';}
        updateDuree();
        const m=parseFloat(document.getElementById('montant_engage').value)||0;
        document.getElementById('recap_montant').textContent=m>0?fmt(m)+' FCFA':'-';
    }

    function updateDuree(){
        const d=new Date(document.getElementById('date_debut_prevue').value),f=new Date(document.getElementById('date_fin_prevue').value);
        if(document.getElementById('date_debut_prevue').value&&document.getElementById('date_fin_prevue').value&&f>d){
            const diff=Math.ceil(Math.abs(f-d)/(1000*60*60*24)),txt=diff+' jour'+(diff>1?'s':'');
            document.getElementById('dureeCalculee').textContent=txt;document.getElementById('recap_duree').textContent=txt;
        }else{document.getElementById('dureeCalculee').textContent='-';document.getElementById('recap_duree').textContent='-';}
    }

    function fmt(m){return new Intl.NumberFormat('fr-FR').format(Math.round(m));}

    document.getElementById('date_attribution').addEventListener('change',function(){
        const dd=document.getElementById('date_debut_prevue');dd.min=this.value;
        if(dd.value&&dd.value<this.value)dd.value=this.value;updateRecap();
    });
    document.getElementById('date_debut_prevue').addEventListener('change',function(){
        const df=document.getElementById('date_fin_prevue');df.min=this.value;
        if(df.value&&df.value<=this.value){const d=new Date(this.value);d.setDate(d.getDate()+30);df.value=d.toISOString().split('T')[0];}
        updateRecap();
    });
    document.getElementById('date_fin_prevue').addEventListener('change',updateRecap);
    document.getElementById('montant_engage').addEventListener('input',updateRecap);
    document.getElementById('new_numero_proforma').addEventListener('input',updateRecap);
    lotSelect.on('change',updateRecap);prestataireSelect.on('change',updateRecap);

    toggleMode();calcProforma();updateRecap();
});
</script>
@endpush

@endsection
