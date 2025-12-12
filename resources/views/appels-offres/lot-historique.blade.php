@extends('layouts.main')
@section('title', 'Historique du Lot - ' . $lot->numero)
@section('breadcrumb')
    <a href="{{ route('appels-offres.index') }}" class="text-white/80 hover:text-white transition-colors">Appels d'offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('appels-offres.show', $lot->appelOffre->id_appel_offre) }}" class="text-white/80 hover:text-white transition-colors">{{ $lot->appelOffre->numero_appel_offre }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('lots-appels-offres.index', $lot->appelOffre->id_appel_offre) }}" class="text-white/80 hover:text-white transition-colors">Lots</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('lots-appels-offres.show', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}" class="text-white/80 hover:text-white transition-colors">{{ $lot->numero }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Historique</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('lots-appels-offres.show', [$lot->appelOffre->id_appel_offre, $lot->id_lot]) }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                            <i class="fas fa-history text-purple-500"></i>
                            <span>Historique des Versions</span>
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">Lot {{ $lot->numero }} - {{ $lot->libelle }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-sm font-semibold">
                        {{ $historique->count() }} version(s)
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <div class="max-w-6xl mx-auto">

            <!-- Info AO -->
            <div class="mb-6 bg-white rounded-2xl shadow-lg overflow-hidden border-l-4 border-purple-500">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">
                                    {{ $lot->appelOffre->numero_appel_offre }}
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-700">
                                    {{ $lot->appelOffre->typeAppelOffre->code_type_appel_offre }}
                                </span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                {{ $lot->appelOffre->libelle_critere_appel_offre }}
                            </h3>
                        </div>
                        <a href="{{ route('appels-offres.show', $lot->appelOffre->id_appel_offre) }}"
                            target="_blank"
                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                            title="Voir l'appel d'offres">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Timeline des versions -->
            <div class="relative">
                <!-- Ligne verticale de timeline -->
                <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gradient-to-b from-purple-300 via-purple-200 to-transparent hidden md:block"></div>

                <div class="space-y-6">
                    @foreach($historique as $index => $version)
                        <div class="relative">
                            <!-- Point sur la timeline -->
                            <div class="absolute left-8 top-8 w-4 h-4 bg-purple-500 rounded-full border-4 border-white shadow-lg transform -translate-x-1/2 hidden md:block z-10"></div>

                            <!-- Carte de version -->
                            <div class="md:ml-16 bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl {{ $version->id_lot === $lot->id_lot ? 'border-2 border-purple-500' : '' }}">
                                <!-- En-tête -->
                                <div class="px-6 py-4 {{ $version->id_lot === $lot->id_lot ? 'bg-gradient-to-r from-purple-50 to-white' : 'bg-gradient-to-r from-gray-50 to-white' }} border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold {{ $version->id_lot === $lot->id_lot ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                                                Version {{ $version->version_lot }}
                                            </span>
                                            @if($version->id_lot === $lot->id_lot)
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Actuelle
                                                </span>
                                            @endif
                                            @if($index === $historique->count() - 1)
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-700">
                                                    <i class="fas fa-star mr-1"></i>
                                                    Initiale
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center space-x-2">
                                            <button onclick="toggleDetails('version-{{ $version->id_lot }}')"
                                                class="px-3 py-1 text-sm text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-all">
                                                <i class="fas fa-chevron-down transform transition-transform" id="icon-version-{{ $version->id_lot }}"></i>
                                                <span class="ml-1">Détails</span>
                                            </button>
                                            <a href="{{ route('lots-appels-offres.show', [$lot->appelOffre->id_appel_offre, $version->id_lot]) }}"
                                                class="px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                                <i class="fas fa-eye mr-1"></i>
                                                Voir
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Résumé de la version -->
                                <div class="p-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                        <!-- Date de création -->
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-calendar text-blue-600"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Créé le</p>
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $version->created_at->format('d/m/Y à H:i') }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Créé par -->
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-user text-green-600"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Créé par</p>
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $version->creator->nom_complet ?? '-' }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Statut -->
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 {{ $version->statut_lot ? 'bg-green-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                                                <i class="fas {{ $version->statut_lot ? 'fa-check-circle text-green-600' : 'fa-times-circle text-gray-600' }}"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Statut</p>
                                                <p class="text-sm font-semibold {{ $version->statut_lot ? 'text-green-600' : 'text-gray-600' }}">
                                                    {{ $version->statut_lot ? 'Actif' : 'Inactif' }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Attribution -->
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 {{ $version->attribution_lot ? 'bg-orange-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                                                <i class="fas {{ $version->attribution_lot ? 'fa-user-check text-orange-600' : 'fa-user-slash text-gray-600' }}"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Attribution</p>
                                                <p class="text-sm font-semibold {{ $version->attribution_lot ? 'text-orange-600' : 'text-gray-600' }}">
                                                    {{ $version->attribution_lot ? 'Attribué' : 'Non attribué' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Motif de modification -->
                                    @if($version->motif_retrait)
                                        <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                                            <div class="flex items-start">
                                                <i class="fas fa-exclamation-triangle text-red-500 mt-1 mr-3"></i>
                                                <div class="flex-1">
                                                    <h4 class="text-sm font-semibold text-red-800 mb-1">Motif de retrait</h4>
                                                    <p class="text-sm text-red-700">{{ $version->motif_retrait }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Détails détaillés (repliable) -->
                                <div id="version-{{ $version->id_lot }}" class="hidden border-t border-gray-200">
                                    <div class="p-6 bg-gray-50">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Informations générales -->
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                                    Informations générales
                                                </h4>
                                                <dl class="space-y-2 text-sm">
                                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                                        <dt class="text-gray-600">Numéro:</dt>
                                                        <dd class="font-semibold text-gray-900">{{ $version->numero }}</dd>
                                                    </div>
                                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                                        <dt class="text-gray-600">Libellé:</dt>
                                                        <dd class="font-semibold text-gray-900">{{ $version->libelle ?? 'N/A' }}</dd>
                                                    </div>
                                                    <div class="py-2 border-b border-gray-200">
                                                        <dt class="text-gray-600 mb-1">Description:</dt>
                                                        <dd class="text-gray-900">{{ $version->description_critere ?? 'N/A' }}</dd>
                                                    </div>
                                                </dl>
                                            </div>

                                            <!-- Dates et planning -->
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                                                    <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                                                    Planning
                                                </h4>
                                                <dl class="space-y-2 text-sm">
                                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                                        <dt class="text-gray-600">Début prévu:</dt>
                                                        <dd class="font-semibold text-gray-900">
                                                            {{ $version->date_debut_prevue ? \Carbon\Carbon::parse($version->date_debut_prevue)->format('d/m/Y') : 'N/A' }}
                                                        </dd>
                                                    </div>
                                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                                        <dt class="text-gray-600">Fin prévue:</dt>
                                                        <dd class="font-semibold text-gray-900">
                                                            {{ $version->date_fin_prevue ? \Carbon\Carbon::parse($version->date_fin_prevue)->format('d/m/Y') : 'N/A' }}
                                                        </dd>
                                                    </div>
                                                    @if($version->date_attribution)
                                                        <div class="flex justify-between py-2 border-b border-gray-200">
                                                            <dt class="text-gray-600">Date attribution:</dt>
                                                            <dd class="font-semibold text-orange-600">
                                                                {{ \Carbon\Carbon::parse($version->date_attribution)->format('d/m/Y') }}
                                                            </dd>
                                                        </div>
                                                    @endif
                                                </dl>
                                            </div>

                                            <!-- Spécifications techniques -->
                                            @if($version->specifications_techniques)
                                                <div class="md:col-span-2">
                                                    <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                                                        <i class="fas fa-file-alt text-purple-500 mr-2"></i>
                                                        Spécifications techniques
                                                    </h4>
                                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $version->specifications_techniques }}</p>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Pénalités -->
                                            @if($version->taux_penalites)
                                                <div>
                                                    <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                                                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                                        Pénalités
                                                    </h4>
                                                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                                        <p class="text-2xl font-bold text-red-600">{{ $version->taux_penalites }}%</p>
                                                        <p class="text-xs text-red-600 mt-1">par jour de retard</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($historique->isEmpty())
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-history text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Aucun historique disponible</h3>
                    <p class="text-gray-600">Aucune version antérieure n'a été trouvée pour ce lot.</p>
                </div>
            @endif

        </div>
    </main>

    @push('scripts')
        <script>
            function toggleDetails(id) {
                const element = document.getElementById(id);
                const icon = document.getElementById('icon-' + id);

                if (element.classList.contains('hidden')) {
                    element.classList.remove('hidden');
                    icon.classList.add('rotate-180');
                } else {
                    element.classList.add('hidden');
                    icon.classList.remove('rotate-180');
                }
            }

            // Ouvrir automatiquement la version actuelle
            document.addEventListener('DOMContentLoaded', function() {
                const currentVersionElement = document.querySelector('[id^="version-"]');
                if (currentVersionElement) {
                    const currentVersionId = currentVersionElement.id;
                    toggleDetails(currentVersionId);
                }
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

            .rotate-180 {
                transform: rotate(180deg);
            }
        </style>
    @endpush
@endsection
