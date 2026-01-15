@extends('layouts.main')

@section('title', 'Ajouter une Capacité Technique - ' . $prestataire->raison_sociale_prestataire)

@push('styles')
<style>
    .form-section {
        transition: all 0.3s ease;
    }
    .form-section:hover {
        box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .tag-input {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        min-height: 100px;
        cursor: text;
    }
    .tag-input:focus-within {
        border-color: #f97316;
        box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.2);
    }
    .tag {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
        color: white;
        border-radius: 9999px;
        font-size: 0.875rem;
    }
    .tag button {
        margin-left: 0.5rem;
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        opacity: 0.7;
    }
    .tag button:hover {
        opacity: 1;
    }
</style>
@endpush

@section('breadcrumb')
    <a @can('prestataires.read') href="{{ route('prestataires.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Prestataires</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('prestataires.view-details') href="{{ route('prestataires.show', $prestataire->id_prestataire) }}" @endcan class="text-white/80 hover:text-white transition-colors">{{ Str::limit($prestataire->raison_sociale_prestataire, 20) }}</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('capacites_techniques.read') href="{{ route('prestataires.capacites-techniques.index', $prestataire->id_prestataire) }}" @endcan class="text-white/80 hover:text-white transition-colors">Capacités Techniques</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Ajouter</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center gap-3">
                @can('capacites_techniques.read')
                <a href="{{ route('prestataires.capacites-techniques.index', $prestataire->id_prestataire) }}"
                   class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                @endcan
                <div>
                    <h1 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-plus-circle text-orange-500 mr-2"></i>
                        Ajouter une Capacité Technique
                    </h1>
                    <p class="text-gray-600 text-sm mt-1">{{ $prestataire->raison_sociale_prestataire }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages d'erreur -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle mr-3 mt-0.5 text-red-500"></i>
                    <div>
                        <p class="font-medium">Veuillez corriger les erreurs suivantes :</p>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @can('capacites_techniques.manage')
        <form action="{{ route('prestataires.capacites-techniques.store', $prestataire->id_prestataire) }}" method="POST" id="capaciteForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Colonne gauche -->
                <div class="space-y-6">
                    <!-- Effectifs -->
                    <div class="form-section bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-users text-blue-500 mr-2"></i>
                                Effectifs
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="effectif_permanent_capacite_technique" class="block text-sm font-medium text-gray-700 mb-2">
                                        Effectif permanent
                                    </label>
                                    <input type="number" name="effectif_permanent_capacite_technique" id="effectif_permanent_capacite_technique"
                                           min="0" max="99999"
                                           value="{{ old('effectif_permanent_capacite_technique', 0) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                                    <p class="mt-1 text-xs text-gray-400">Employés en CDI</p>
                                </div>
                                <div>
                                    <label for="effectif_temporaire_capacite_technique" class="block text-sm font-medium text-gray-700 mb-2">
                                        Effectif temporaire
                                    </label>
                                    <input type="number" name="effectif_temporaire_capacite_technique" id="effectif_temporaire_capacite_technique"
                                           min="0" max="99999"
                                           value="{{ old('effectif_temporaire_capacite_technique', 0) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                                    <p class="mt-1 text-xs text-gray-400">CDD, intérimaires, etc.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Références et Compétences -->
                    <div class="form-section bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-star text-yellow-500 mr-2"></i>
                                Références & Compétences
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label for="references_capacite_technique" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre de références
                                </label>
                                <input type="text" name="references_capacite_technique" id="references_capacite_technique"
                                       maxlength="10"
                                       value="{{ old('references_capacite_technique') }}"
                                       placeholder="Ex: 15+"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            </div>
                            <div>
                                <label for="competences_cles_capacite_technique" class="block text-sm font-medium text-gray-700 mb-2">
                                    Compétences clés
                                </label>
                                <input type="text" name="competences_cles_capacite_technique" id="competences_cles_capacite_technique"
                                       maxlength="25"
                                       value="{{ old('competences_cles_capacite_technique') }}"
                                       placeholder="Ex: BTP, Informatique"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                                <p class="mt-1 text-xs text-gray-400">Maximum 25 caractères</p>
                            </div>
                        </div>
                    </div>

                    <!-- Moyens Matériels -->
                    <div class="form-section bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-tools text-gray-500 mr-2"></i>
                                Moyens Matériels
                            </h2>
                        </div>
                        <div class="p-6">
                            <textarea name="moyens_materiels_capacite_technique" id="moyens_materiels_capacite_technique"
                                      rows="5"
                                      placeholder="Décrivez les moyens matériels dont dispose le prestataire (équipements, véhicules, locaux, etc.)..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none">{{ old('moyens_materiels_capacite_technique') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite -->
                <div class="space-y-6">
                    <!-- Certifications -->
                    <div class="form-section bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-certificate text-purple-500 mr-2"></i>
                                Certifications
                            </h2>
                        </div>
                        <div class="p-6">
                            <textarea name="certifications_capacite_technique" id="certifications_capacite_technique"
                                      rows="4"
                                      placeholder="Listez les certifications (séparées par des virgules)&#10;Ex: ISO 9001, ISO 14001, OHSAS 18001"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none">{{ old('certifications_capacite_technique') }}</textarea>
                            <p class="mt-2 text-xs text-gray-400">
                                <i class="fas fa-info-circle mr-1"></i>
                                Séparez chaque certification par une virgule
                            </p>
                        </div>
                    </div>

                    <!-- Agréments -->
                    <div class="form-section bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-award text-green-500 mr-2"></i>
                                Agréments
                            </h2>
                        </div>
                        <div class="p-6">
                            <textarea name="agrements_capacite_technique" id="agrements_capacite_technique"
                                      rows="4"
                                      placeholder="Listez les agréments (séparés par des virgules)&#10;Ex: Agrément ANDE, Agrément Ministère"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none">{{ old('agrements_capacite_technique') }}</textarea>
                            <p class="mt-2 text-xs text-gray-400">
                                <i class="fas fa-info-circle mr-1"></i>
                                Séparez chaque agrément par une virgule
                            </p>
                        </div>
                    </div>

                    <!-- Domaines d'expertise -->
                    <div class="form-section bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-briefcase text-orange-500 mr-2"></i>
                                Domaines d'Expertise
                            </h2>
                        </div>
                        <div class="p-6">
                            <textarea name="domaines_expertise_capacite_technique" id="domaines_expertise_capacite_technique"
                                      rows="4"
                                      placeholder="Listez les domaines d'expertise (séparés par des virgules)&#10;Ex: Construction, Réseaux, Énergie solaire"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none">{{ old('domaines_expertise_capacite_technique') }}</textarea>
                            <p class="mt-2 text-xs text-gray-400">
                                <i class="fas fa-info-circle mr-1"></i>
                                Séparez chaque domaine par une virgule
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="mt-8 flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('prestataires.capacites-techniques.index', $prestataire->id_prestataire) }}"
                   class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl transition-colors text-center">
                    <i class="fas fa-times mr-2"></i>Annuler
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-xl transition-colors shadow-sm">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
            </div>
        </form>
        @endcan

    </main>
@endsection
