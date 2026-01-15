@extends('layouts.main')

@section('title', 'Modifier la Capacité Technique - ' . $prestataire->raison_sociale_prestataire)

@push('styles')
<style>
    .form-section {
        transition: all 0.3s ease;
    }
    .form-section:hover {
        box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.1);
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
    <span class="text-white font-medium">Modifier</span>
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
                        <i class="fas fa-edit text-orange-500 mr-2"></i>
                        Modifier la Capacité Technique
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

        <!-- Score actuel -->
        @php
            $niveau = $capacite->getNiveau();
            $score = $capacite->calculerScore();
        @endphp
        <div class="mb-6 bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Score actuel de la capacité technique</h3>
                    <p class="text-sm text-gray-500">Basé sur les informations renseignées</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-{{ $niveau['classe'] }}-600">{{ $score }}/100</div>
                    <div class="text-sm font-medium text-{{ $niveau['classe'] }}-600">
                        <i class="fas fa-{{ $niveau['icon'] }} mr-1"></i>{{ $niveau['niveau'] }}
                    </div>
                </div>
            </div>
        </div>

        @can('capacites_techniques.manage')
        <form action="{{ route('prestataires.capacites-techniques.update', [$prestataire->id_prestataire, $capacite->id_capacite_technique]) }}" method="POST">
            @csrf
            @method('PUT')

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
                                           value="{{ old('effectif_permanent_capacite_technique', $capacite->effectif_permanent_capacite_technique) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                                    <p class="mt-1 text-xs text-gray-400">Employés en CDI</p>
                                </div>
                                <div>
                                    <label for="effectif_temporaire_capacite_technique" class="block text-sm font-medium text-gray-700 mb-2">
                                        Effectif temporaire
                                    </label>
                                    <input type="number" name="effectif_temporaire_capacite_technique" id="effectif_temporaire_capacite_technique"
                                           min="0" max="99999"
                                           value="{{ old('effectif_temporaire_capacite_technique', $capacite->effectif_temporaire_capacite_technique) }}"
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
                                       value="{{ old('references_capacite_technique', $capacite->references_capacite_technique) }}"
                                       placeholder="Ex: 15+"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            </div>
                            <div>
                                <label for="competences_cles_capacite_technique" class="block text-sm font-medium text-gray-700 mb-2">
                                    Compétences clés
                                </label>
                                <input type="text" name="competences_cles_capacite_technique" id="competences_cles_capacite_technique"
                                       maxlength="25"
                                       value="{{ old('competences_cles_capacite_technique', $capacite->competences_cles_capacite_technique) }}"
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
                                      placeholder="Décrivez les moyens matériels..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none">{{ old('moyens_materiels_capacite_technique', $capacite->moyens_materiels_capacite_technique) }}</textarea>
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
                                      placeholder="Listez les certifications (séparées par des virgules)"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none">{{ old('certifications_capacite_technique', $capacite->certifications_capacite_technique) }}</textarea>
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
                                      placeholder="Listez les agréments (séparés par des virgules)"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none">{{ old('agrements_capacite_technique', $capacite->agrements_capacite_technique) }}</textarea>
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
                                      placeholder="Listez les domaines d'expertise (séparés par des virgules)"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none">{{ old('domaines_expertise_capacite_technique', $capacite->domaines_expertise_capacite_technique) }}</textarea>
                            <p class="mt-2 text-xs text-gray-400">
                                <i class="fas fa-info-circle mr-1"></i>
                                Séparez chaque domaine par une virgule
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="mt-8 flex flex-col sm:flex-row justify-between gap-3">
                <button type="button" onclick="confirmDelete()"
                        class="px-6 py-3 bg-red-100 hover:bg-red-200 text-red-600 font-medium rounded-xl transition-colors">
                    <i class="fas fa-trash mr-2"></i>Supprimer
                </button>
                <div class="flex gap-3">
                    <a href="{{ route('prestataires.capacites-techniques.index', $prestataire->id_prestataire) }}"
                       class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl transition-colors text-center">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-xl transition-colors shadow-sm">
                        <i class="fas fa-save mr-2"></i>Enregistrer
                    </button>
                </div>
            </div>
        </form>
        @endcan

        

        <!-- Formulaire de suppression caché -->
        <form id="deleteForm" action="{{ route('prestataires.capacites-techniques.destroy', [$prestataire->id_prestataire, $capacite->id_capacite_technique]) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

    </main>
@endsection

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette fiche de capacité technique ?')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>
@endpush
