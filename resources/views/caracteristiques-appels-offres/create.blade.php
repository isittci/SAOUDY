@extends('layouts.main')
@section('title', 'Nouvelle Caractéristique')
@section('breadcrumb')
    <a @can('appels_offres.read') href="{{ route('appels-offres.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Appels d'offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('appels_offres.view-details') href="{{ route('appels-offres.show', $appelOffre->id_appel_offre) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">{{ $appelOffre->numero_appel_offre }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('caracteristiques_appels_offres.read') href="{{ route('caracteristiques-appels-offres.index', $appelOffre->id_appel_offre) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Caractéristiques</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Nouvelle</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @can('caracteristiques_appels_offres.read')
                        <a href="{{ route('caracteristiques-appels-offres.index', $appelOffre->id_appel_offre) }}"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                            <i class="fas fa-plus-circle text-green-500"></i>
                            <span>Nouvelle Caractéristique</span>
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">{{ $appelOffre->numero_appel_offre }} -
                            {{ $appelOffre->libelle_critere_appel_offre }}</p>
                    </div>
                </div>

                @can('caracteristiques_appels_offres.read')
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('caracteristiques-appels-offres.index', $appelOffre->id_appel_offre) }}"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-all duration-200 flex items-center space-x-2">
                            <i class="fas fa-times text-sm"></i>
                            <span class="text-sm font-medium hidden sm:inline">Annuler</span>
                        </a>
                    </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages d'erreur -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <h3 class="text-red-800 font-semibold mb-2">Erreurs de validation</h3>
                        <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
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

        @can('caracteristiques_appels_offres.create')
            <!-- Formulaire -->
            <form method="POST" action="{{ route('caracteristiques-appels-offres.store', $appelOffre->id_appel_offre) }}"
                class="max-w-5xl mx-auto">
                @csrf

                <div class="space-y-6">

                    <!-- Informations de l'AO (lecture seule) -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-bullhorn text-indigo-500 mr-2"></i>
                                Appel d'offres associé
                            </h2>
                        </div>

                        <div class="p-6">
                            <div class="p-4 bg-gradient-to-r from-indigo-50 to-white border border-indigo-200 rounded-lg">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">
                                                {{ $appelOffre->numero_appel_offre }}
                                            </span>
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-700">
                                                {{ $appelOffre->typeAppelOffre->code_type_appel_offre }}
                                            </span>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            {{ $appelOffre->libelle_critere_appel_offre }}
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-coins mr-1"></i>
                                            Montant global:
                                            <strong>{{ number_format($appelOffre->montant_global_appel_offre, 0, ',', ' ') }}
                                                FCFA</strong>
                                        </p>
                                    </div>
                                    <a href="{{ route('appels-offres.show', $appelOffre->id_appel_offre) }}" target="_blank"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                        title="Voir l'appel d'offres">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Planning et Dates -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                Planning et Dates
                            </h2>
                        </div>

                        {{-- {{ dd($appelOffre) }} --}}

                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Date de démarrage prévue -->
                            <div>
                                <label for="date_demarrage_prevue_caracteristique_appel_offre"
                                    class="block text-sm font-semibold text-gray-700 mb-2">
                                    Date de démarrage prévue
                                </label>
                                <input type="date"
                                    min="{{ \Carbon\Carbon::parse($appelOffre->date_ouverture_plis_critere_appel_offre)->toDateString() }}"
                                    onchange="verifyLivraisonDate(this)" id="date_demarrage_prevue_caracteristique_appel_offre"
                                    name="date_demarrage_prevue_caracteristique_appel_offre"
                                    value="{{ old('date_demarrage_prevue_caracteristique_appel_offre') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent @error('date_demarrage_prevue_caracteristique_appel_offre') border-red-500 @enderror">
                                @error('date_demarrage_prevue_caracteristique_appel_offre')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>



                            <!-- Date de livraison prévisionnelle -->
                            <div>
                                <label for="date_livraison_previsionnelle_caracteristique_appel_offre"
                                    class="block text-sm font-semibold text-gray-700 mb-2">
                                    Date de livraison prévisionnelle
                                </label>
                                <input type="date" id="date_livraison_previsionnelle_caracteristique_appel_offre"
                                    min="{{ \Carbon\Carbon::parse($appelOffre->date_ouverture_plis_critere_appel_offre)->toDateString() }}"
                                    name="date_livraison_previsionnelle_caracteristique_appel_offre"
                                    value="{{ old('date_livraison_previsionnelle_caracteristique_appel_offre') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent @error('date_livraison_previsionnelle_caracteristique_appel_offre') border-red-500 @enderror">
                                @error('date_livraison_previsionnelle_caracteristique_appel_offre')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Durée estimée (CALCULÉE AUTOMATIQUEMENT - AFFICHAGE SEULEMENT) -->
                            <div class="md:col-span-2">
                                <div
                                    class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-calculator text-blue-600 text-xl"></i>
                                            <div>
                                                <p class="text-sm font-medium text-gray-700">Durée estimée (calculée
                                                    automatiquement)</p>
                                                <p class="text-xs text-gray-500 mt-0.5">Basée sur la différence entre les deux
                                                    dates ci-dessus</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span id="duree_calculee_display" class="text-2xl font-bold text-blue-600">-</span>
                                            <p class="text-xs text-gray-600">jours</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Lieu d'exécution -->
                            <div>
                                <label for="lieu_execution_caracteristique_appel_offre"
                                    class="block text-sm font-semibold text-gray-700 mb-2">
                                    Lieu d'exécution
                                </label>
                                <input type="text" id="lieu_execution_caracteristique_appel_offre"
                                    name="lieu_execution_caracteristique_appel_offre" maxlength="255"
                                    value="{{ old('lieu_execution_caracteristique_appel_offre') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent @error('lieu_execution_caracteristique_appel_offre') border-red-500 @enderror"
                                    placeholder="Ex: Abidjan, Plateau">
                                @error('lieu_execution_caracteristique_appel_offre')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                  

                    <!-- Boutons d'action -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                    Tous les champs sont optionnels
                                </div>

                                <div class="flex items-center space-x-3 w-full sm:w-auto">
                                    @php
                                        $previous = url()->previous();
                                        $current = url()->current();
                                        $fallback = route(
                                            'caracteristiques-appels-offres.index',
                                            $appelOffre->id_appel_offre,
                                        );
                                    @endphp

                                    <a href="{{ $previous === $current ? $fallback : $previous }}"
                                        class="flex-1 sm:flex-none px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium text-center">
                                        Annuler
                                    </a>
                                    <button type="submit"
                                        class="flex-1 sm:flex-none px-6 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                                        <i class="fas fa-save"></i>
                                        <span>Créer la caractéristique</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endcan
    </main>

    @can('caracteristiques_appels_offres.create')
        @push('scripts')
            <script>
                const verifyLivraisonDate = (input) => {
                    document.getElementById('date_livraison_previsionnelle_caracteristique_appel_offre').min = input.value;
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const dateDebut = document.getElementById('date_demarrage_prevue_caracteristique_appel_offre');
                    const dateFin = document.getElementById('date_livraison_previsionnelle_caracteristique_appel_offre');
                    const dureeDisplay = document.getElementById('duree_calculee_display');




                    // Fonction pour calculer la durée en jours
                    function calculerDuree() {
                        if (dateDebut.value && dateFin.value) {
                            const debut = new Date(dateDebut.value);
                            const fin = new Date(dateFin.value);

                            const diffTime = fin - debut;
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                            if (diffDays >= 0) {
                                dureeDisplay.textContent = diffDays;
                                dureeDisplay.classList.remove('text-red-600');
                                dureeDisplay.classList.add('text-blue-600');
                            } else {
                                dureeDisplay.textContent = 'Invalide';
                                dureeDisplay.classList.remove('text-blue-600');
                                dureeDisplay.classList.add('text-red-600');

                                alert('La date de livraison doit être postérieure à la date de démarrage');
                                dateFin.value = '';
                            }
                        } else {
                            dureeDisplay.textContent = '-';
                            dureeDisplay.classList.remove('text-red-600', 'text-blue-600');
                            dureeDisplay.classList.add('text-gray-400');
                        }
                    }

                    // Écouter les changements de dates
                    dateDebut.addEventListener('change', calculerDuree);
                    dateFin.addEventListener('change', calculerDuree);

                    // Calcul initial si les valeurs sont présentes (en cas de retour avec old())
                    calculerDuree();
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
