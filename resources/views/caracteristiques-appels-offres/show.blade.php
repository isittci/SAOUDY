@extends('layouts.main')
@section('title', 'Détails Caractéristique - V' . $caracteristique->version_caracteristique_appel_offre)
@section('breadcrumb')
    <a href="{{ route('appels-offres.index') }}" class="text-white/80 hover:text-white transition-colors">Appels d'offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('appels-offres.show', $appelOffre->id_appel_offre) }}" class="text-white/80 hover:text-white transition-colors">{{ $appelOffre->numero_appel_offre }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('caracteristiques-appels-offres.index', $appelOffre->id_appel_offre) }}" class="text-white/80 hover:text-white transition-colors">Caractéristiques</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">V{{$caracteristique->version_caracteristique_appel_offre}}</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('caracteristiques-appels-offres.index', $appelOffre->id_appel_offre) }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                            <i class="fas fa-cogs text-purple-500"></i>
                            <span>Détails de la Caractéristique</span>
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">Version {{ $caracteristique->version_caracteristique_appel_offre }} - {{ $appelOffre->numero_appel_offre }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('caracteristiques-appels-offres.historique', [$appelOffre->id_appel_offre, $caracteristique->id_caracteristique_appel_offre]) }}"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-all duration-200 flex items-center space-x-2">
                        <i class="fas fa-history text-sm"></i>
                        <span class="text-sm font-medium hidden sm:inline">Historique</span>
                    </a>
                    <a href="{{ route('caracteristiques-appels-offres.edit', [$appelOffre->id_appel_offre, $caracteristique->id_caracteristique_appel_offre]) }}"
                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2">
                        <i class="fas fa-edit text-sm"></i>
                        <span class="text-sm font-medium hidden sm:inline">Modifier</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages de succès/erreur -->
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

        <div class="max-w-5xl mx-auto space-y-6">

            <!-- Informations de l'AO -->
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
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">
                                        {{ $appelOffre->numero_appel_offre }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-700">
                                        {{ $appelOffre->typeAppelOffre->code_type_appel_offre }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-purple-100 text-purple-700">
                                        V{{ $caracteristique->version_caracteristique_appel_offre }}
                                    </span>
                                    @if(!$caracteristique->parent_id)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">
                                            <i class="fas fa-star text-xs mr-1"></i>
                                            Version Initiale
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    {{ $appelOffre->libelle_critere_appel_offre }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-coins mr-1"></i>
                                    Montant global: <strong>{{ number_format($appelOffre->montant_global_appel_offre, 0, ',', ' ') }} FCFA</strong>
                                </p>
                            </div>
                            <a href="{{ route('appels-offres.show', $appelOffre->id_appel_offre) }}"
                                target="_blank"
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                title="Voir l'appel d'offres">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Motif de modification (si existe) -->
            @if($caracteristique->motif_modification_caracteristique_appel_offre)
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg shadow-sm">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-0.5"></i>
                        <div class="flex-1">
                            <h3 class="text-blue-800 font-semibold mb-1">Motif de modification</h3>
                            <p class="text-blue-700 text-sm">{{ $caracteristique->motif_modification_caracteristique_appel_offre }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Planning et Dates -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                        Planning et Dates
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Date de démarrage -->
                        <div class="bg-gradient-to-br from-green-50 to-white p-4 rounded-lg border border-green-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-play text-green-600 text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-600 mb-1">Date de démarrage</p>
                                    @if($caracteristique->date_demarrage_prevue_caracteristique_appel_offre)
                                        <p class="text-lg font-bold text-gray-900">
                                            {{ \Carbon\Carbon::parse($caracteristique->date_demarrage_prevue_caracteristique_appel_offre)->format('d/m/Y') }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-400">Non définie</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Durée estimée -->
                        <div class="bg-gradient-to-br from-blue-50 to-white p-4 rounded-lg border border-blue-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-hourglass-half text-blue-600 text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-600 mb-1">Durée estimée</p>
                                    @if($caracteristique->duree_estimee_jours_caracteristique_appel_offre)
                                        <p class="text-lg font-bold text-gray-900">
                                            {{ number_format($caracteristique->duree_estimee_jours_caracteristique_appel_offre, 0, ',', ' ') }} jours
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-400">Non définie</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Date de livraison -->
                        <div class="bg-gradient-to-br from-purple-50 to-white p-4 rounded-lg border border-purple-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-flag-checkered text-purple-600 text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-600 mb-1">Date de livraison</p>
                                    @if($caracteristique->date_livraison_previsionnelle_caracteristique_appel_offre)
                                        <p class="text-lg font-bold text-gray-900">
                                            {{ \Carbon\Carbon::parse($caracteristique->date_livraison_previsionnelle_caracteristique_appel_offre)->format('d/m/Y') }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-400">Non définie</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Lieu d'exécution -->
                        <div class="bg-gradient-to-br from-orange-50 to-white p-4 rounded-lg border border-orange-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-orange-600 text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-600 mb-1">Lieu d'exécution</p>
                                    @if($caracteristique->lieu_execution_caracteristique_appel_offre)
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $caracteristique->lieu_execution_caracteristique_appel_offre }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-400">Non spécifié</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Garanties et Pénalités -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-shield-alt text-red-500 mr-2"></i>
                        Garanties et Pénalités
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Pénalités -->
                        <div class="bg-gradient-to-br from-red-50 to-white p-4 rounded-lg border border-red-200">
                            <div class="flex items-start space-x-3">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-600 mb-2">Pénalités de retard/jour</p>
                                    @if($caracteristique->penalites_retard_journalier_caracteristique_appel_offre)
                                        <p class="text-xl font-bold text-red-600">
                                            {{ number_format($caracteristique->penalites_retard_journalier_caracteristique_appel_offre, 0, ',', ' ') }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">FCFA par jour de retard</p>
                                    @else
                                        <p class="text-sm text-gray-400">Non défini</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Montant de garantie -->
                        <div class="bg-gradient-to-br from-green-50 to-white p-4 rounded-lg border border-green-200">
                            <div class="flex items-start space-x-3">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-shield-alt text-green-600 text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-600 mb-2">Montant de garantie</p>
                                    @if($caracteristique->montant_garantie_caracteristique_appel_offre)
                                        <p class="text-xl font-bold text-green-600">
                                            {{ number_format($caracteristique->montant_garantie_caracteristique_appel_offre, 0, ',', ' ') }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">FCFA</p>
                                    @else
                                        <p class="text-sm text-gray-400">Non défini</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Délai de garantie -->
                        <div class="bg-gradient-to-br from-blue-50 to-white p-4 rounded-lg border border-blue-200">
                            <div class="flex items-start space-x-3">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-clock text-blue-600 text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-600 mb-2">Délai de garantie</p>
                                    @if($caracteristique->delai_garantie_jours_caracteristique_appel_offre)
                                        <p class="text-xl font-bold text-blue-600">
                                            {{ number_format($caracteristique->delai_garantie_jours_caracteristique_appel_offre, 0, ',', ' ') }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">jours après réception</p>
                                    @else
                                        <p class="text-sm text-gray-400">Non défini</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modalités et Documents -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-file-contract text-purple-500 mr-2"></i>
                        Modalités et Documents
                    </h2>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Conditions de paiement -->
                    <div>
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-purple-600 text-sm"></i>
                            </div>
                            <h3 class="text-sm font-bold text-gray-800">Conditions de paiement</h3>
                        </div>
                        @if($caracteristique->conditions_paiement_caracteristique_appel_offre)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $caracteristique->conditions_paiement_caracteristique_appel_offre }}</p>
                            </div>
                        @else
                            <p class="text-sm text-gray-400 italic">Non spécifiées</p>
                        @endif
                    </div>

                    <!-- Modalités d'exécution -->
                    <div>
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tasks text-blue-600 text-sm"></i>
                            </div>
                            <h3 class="text-sm font-bold text-gray-800">Modalités d'exécution</h3>
                        </div>
                        @if($caracteristique->modalites_execution_caracteristique_appel_offre)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $caracteristique->modalites_execution_caracteristique_appel_offre }}</p>
                            </div>
                        @else
                            <p class="text-sm text-gray-400 italic">Non spécifiées</p>
                        @endif
                    </div>

                    <!-- Documents requis -->
                    <div>
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-alt text-orange-600 text-sm"></i>
                            </div>
                            <h3 class="text-sm font-bold text-gray-800">Documents requis</h3>
                        </div>
                        @if($caracteristique->documents_requis_caracteristique_appel_offre)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $caracteristique->documents_requis_caracteristique_appel_offre }}</p>
                            </div>
                        @else
                            <p class="text-sm text-gray-400 italic">Non spécifiés</p>
                        @endif
                    </div>

                    <!-- Autres informations -->
                    @if($caracteristique->autres_informations_caracteristique_appel_offre)
                        <div>
                            <div class="flex items-center space-x-2 mb-3">
                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-info-circle text-gray-600 text-sm"></i>
                                </div>
                                <h3 class="text-sm font-bold text-gray-800">Autres informations</h3>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $caracteristique->autres_informations_caracteristique_appel_offre }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Métadonnées -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle text-gray-500 mr-2"></i>
                        Informations de suivi
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-user text-blue-500"></i>
                            <div>
                                <p class="text-xs text-gray-500">Créé par</p>
                                <p class="font-semibold text-gray-900">{{ $caracteristique->creator->nom_complet ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-calendar text-green-500"></i>
                            <div>
                                <p class="text-xs text-gray-500">Créé le</p>
                                <p class="font-semibold text-gray-900">{{ $caracteristique->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>

                        @if($caracteristique->updated_at != $caracteristique->created_at)
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <i class="fas fa-user-edit text-orange-500"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Modifié par</p>
                                    <p class="font-semibold text-gray-900">{{ $caracteristique->updater->nom_complet ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <i class="fas fa-clock text-purple-500"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Modifié le</p>
                                    <p class="font-semibold text-gray-900">{{ $caracteristique->updated_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
                        @endif

                        @if($caracteristique->parent_id)
                            <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <i class="fas fa-code-branch text-blue-500"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Version parente</p>
                                    <p class="font-semibold text-blue-900">V{{ $caracteristique->parent->version_caracteristique_appel_offre ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
        <script>
            // Afficher historique
            function showHistorique() {
                fetch(`/appels-offres/{{ $appelOffre->id_appel_offre }}/caracteristiques/{{ $caracteristique->id_caracteristique_appel_offre }}/historique`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.length > 0) {
                            let message = `Historique des versions:\n\n`;
                            data.data.forEach(v => {
                                message += `Version ${v.version_caracteristique_appel_offre} - ${new Date(v.created_at).toLocaleString('fr-FR')}\n`;
                                if (v.motif_modification_caracteristique_appel_offre) {
                                    message += `Motif: ${v.motif_modification_caracteristique_appel_offre}\n`;
                                }
                                message += `Créé par: ${v.creator?.nom_complet || 'N/A'}\n\n`;
                            });
                            alert(message);
                        } else {
                            alert('Aucun historique disponible');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors de la récupération de l\'historique');
                    });
            }
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
@endsection
