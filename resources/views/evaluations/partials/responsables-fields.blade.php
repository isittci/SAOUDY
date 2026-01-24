{{--
    ============================================================================
    PARTIAL: Champs responsables avec auto-complétion
    ============================================================================

    Usage dans create.blade.php ou edit.blade.php:
    @include('evaluations.partials.responsables-fields', [
        'responsablesExistants' => $responsablesExistants ?? [],
        'evaluation' => $evaluation ?? null,
    ])
--}}

<!-- Responsables (OBLIGATOIRES) -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b border-gray-200">
        <h2 class="text-lg font-bold text-gray-800 flex items-center">
            <i class="fas fa-users text-red-500 mr-2"></i>
            Responsables *
        </h2>
        <p class="text-xs text-red-600 mt-1">
            Tous les responsables sont obligatoires -
            <span class="text-gray-500">Commencez à taper pour voir les suggestions</span>
        </p>
    </div>
    <div class="p-6 space-y-6">

        <!-- Responsable technique -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                <i class="fas fa-user-cog text-blue-500 mr-1"></i>
                Responsable technique *
            </label>
            <div class="space-y-2">
                <!-- Champ nom avec auto-complétion -->
                <div class="relative" data-responsable-autocomplete="respo_technique">
                    <input type="text"
                        name="respo_technique[nom_complet]"
                        id="respo_technique_nom_complet"
                        class="responsable-autocomplete-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400 @error('respo_technique.nom_complet') border-red-500 @enderror"
                        placeholder="Nom complet * (tapez pour rechercher)"
                        value="{{ old('respo_technique.nom_complet', isset($evaluation) ? ($evaluation->respo_technique_evaluation['nom_complet'] ?? '') : '') }}"
                        autocomplete="off"
                        required>

                    <!-- Liste des suggestions -->
                    <div class="responsable-suggestions hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <!-- Les suggestions seront injectées ici -->
                    </div>
                </div>
                @error('respo_technique.nom_complet')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror

                <input type="email"
                    name="respo_technique[email]"
                    id="respo_technique_email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400"
                    placeholder="Email"
                    value="{{ old('respo_technique.email', isset($evaluation) ? ($evaluation->respo_technique_evaluation['email'] ?? '') : '') }}">

                <input type="text"
                    name="respo_technique[telephone]"
                    id="respo_technique_telephone"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400"
                    placeholder="Téléphone"
                    value="{{ old('respo_technique.telephone', isset($evaluation) ? ($evaluation->respo_technique_evaluation['telephone'] ?? '') : '') }}">
            </div>
        </div>

        <!-- Superviseur -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                <i class="fas fa-user-shield text-purple-500 mr-1"></i>
                Superviseur *
            </label>
            <div class="space-y-2">
                <!-- Champ nom avec auto-complétion -->
                <div class="relative" data-responsable-autocomplete="superviseur">
                    <input type="text"
                        name="superviseur[nom_complet]"
                        id="superviseur_nom_complet"
                        class="responsable-autocomplete-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-400 @error('superviseur.nom_complet') border-red-500 @enderror"
                        placeholder="Nom complet * (tapez pour rechercher)"
                        value="{{ old('superviseur.nom_complet', isset($evaluation) ? ($evaluation->superviseur_evaluation['nom_complet'] ?? '') : '') }}"
                        autocomplete="off"
                        required>

                    <!-- Liste des suggestions -->
                    <div class="responsable-suggestions hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                    </div>
                </div>
                @error('superviseur.nom_complet')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror

                <input type="email"
                    name="superviseur[email]"
                    id="superviseur_email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-400"
                    placeholder="Email"
                    value="{{ old('superviseur.email', isset($evaluation) ? ($evaluation->superviseur_evaluation['email'] ?? '') : '') }}">

                <input type="text"
                    name="superviseur[telephone]"
                    id="superviseur_telephone"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-400"
                    placeholder="Téléphone"
                    value="{{ old('superviseur.telephone', isset($evaluation) ? ($evaluation->superviseur_evaluation['telephone'] ?? '') : '') }}">
            </div>
        </div>

        <!-- Évaluateur -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                <i class="fas fa-user-check text-green-500 mr-1"></i>
                Évaluateur *
            </label>
            <div class="space-y-2">
                <!-- Champ nom avec auto-complétion -->
                <div class="relative" data-responsable-autocomplete="evalue_par">
                    <input type="text"
                        name="evalue_par[nom_complet]"
                        id="evalue_par_nom_complet"
                        class="responsable-autocomplete-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400 @error('evalue_par.nom_complet') border-red-500 @enderror"
                        placeholder="Nom complet * (tapez pour rechercher)"
                        value="{{ old('evalue_par.nom_complet', isset($evaluation) ? ($evaluation->evalue_par['nom_complet'] ?? '') : '') }}"
                        autocomplete="off"
                        required>

                    <!-- Liste des suggestions -->
                    <div class="responsable-suggestions hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                    </div>
                </div>
                @error('evalue_par.nom_complet')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror

                <input type="email"
                    name="evalue_par[email]"
                    id="evalue_par_email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400"
                    placeholder="Email"
                    value="{{ old('evalue_par.email', isset($evaluation) ? ($evaluation->evalue_par['email'] ?? '') : '') }}">

                <input type="text"
                    name="evalue_par[telephone]"
                    id="evalue_par_telephone"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-400"
                    placeholder="Téléphone"
                    value="{{ old('evalue_par.telephone', isset($evaluation) ? ($evaluation->evalue_par['telephone'] ?? '') : '') }}">
            </div>
        </div>

    </div>
</div>

{{-- Style pour les suggestions --}}
@push('styles')
<style>
    .responsable-suggestion-item {
        padding: 10px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        transition: background-color 0.15s ease;
    }
    .responsable-suggestion-item:last-child {
        border-bottom: none;
    }
    .responsable-suggestion-item:hover,
    .responsable-suggestion-item.active {
        background-color: #f0f9ff;
    }
    .responsable-suggestion-item .suggestion-name {
        font-weight: 600;
        color: #1f2937;
    }
    .responsable-suggestion-item .suggestion-details {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 2px;
    }
    .responsable-suggestion-item .suggestion-icon {
        color: #3b82f6;
    }
    .responsable-suggestions.show {
        display: block !important;
    }
</style>
@endpush
