@extends('layouts.main')
@section('title', 'Attribution ' . $attribution->numero_attribution)
@section('breadcrumb')
    <a @can('attributions_lots.read') href="{{ route('attributions.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Attributions</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">{{ $attribution->numero_attribution }}</span>
@endsection

@section('content')
    <!-- Header avec actions -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et retour -->
                <div class="flex items-center space-x-4">
                    <a @can('attributions_lots.read') href="{{ route('attributions.index') }}" @endcan
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <div>
                        <div class="flex items-center space-x-3 flex-wrap gap-2">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $attribution->numero_attribution }}</h1>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $attribution->statut_badge_class }}">
                                {{ $attribution->statut_label }}
                            </span>
                            @if (!$attribution->is_active)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-600">
                                    <i class="fas fa-history mr-1"></i> Historique
                                </span>
                            @endif
                            @if ($attribution->estEnRetard() && $attribution->is_active)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $attribution->jours_retard_actuels }} jour(s) de retard
                                </span>
                            @endif
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                Version {{ $attribution->version_attribution }}
                            </span>
                        </div>
                        <p class="text-gray-600 mt-1">
                            Lot {{ $attribution->lot->numero ?? 'N/A' }} -
                            {{ Str::limit($attribution->lot->libelle ?? '', 50) }}
                        </p>
                    </div>
                </div>

                {{-- @canany(['evaluations_attributions.read', 'attributions_lots.suspend', 'attributions_lots.resume',
                    'attributions_lots.withdraw', 'attributions_lots.view-history', 'prestataires.read',
                    'attributions_lots.reassign'])
                    <!-- Actions -->
                    <div class="flex items-center space-x-2 flex-wrap gap-2">

                        @if ($attribution->is_active)
                            @canany(['evaluations_attributions.read', 'attributions_lots.suspend'])
                                @if ($attribution->peutEtreSuspendue())
                                    @can('evaluations_attributions.read')
                                        <a href="{{ route('evaluations.pour-attribution', $attribution->id_attribution) }}"
                                            class="px-4 py-2.5 bg-white border border-green-300 text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                            <i class="fas fa-check-circle text-sm"></i>
                                            <span class="text-sm font-medium">Évaluations</span>
                                        </a>
                                    @endcan



                                    @can('attributions_lots.suspend')
                                        <button onclick="openSuspendreModal()"
                                            class="px-4 py-2.5 bg-white border border-yellow-300 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                            <i class="fas fa-pause text-sm"></i>
                                            <span class="text-sm font-medium">Suspendre</span>
                                        </button>
                                    @endcan

                                    @can('attributions_lots.reassign')
                                        @if ($attribution->pourcentage_avancement == 100 && !$attribution->date_effective_fin)
                                            <button onclick="openDateEffectiveFinModal()"
                                                class="px-4 py-2.5 bg-white border border-yellow-300 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                                <i class="fas fa-calendar-alt text-sm"></i>
                                                <span class="text-sm font-medium">Date effective de fin</span>
                                            </button>
                                        @endif
                                    @endcan
                                @endif
                            @endcanany



                            @can('attributions_lots.resume')
                                @if ($attribution->peutEtreReprise())
                                    <button onclick="reprendre()"
                                        class="px-4 py-2.5 bg-white border border-green-300 text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                        <i class="fas fa-play text-sm"></i>
                                        <span class="text-sm font-medium">Reprendre</span>
                                    </button>
                                @endif
                            @endcan

                            @can('attributions_lots.withdraw')
                                @if ($attribution->peutEtreRetiree())
                                    <button onclick="openRetirerModal()"
                                        class="px-4 py-2.5 bg-white border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                        <i class="fas fa-ban text-sm"></i>
                                        <span class="text-sm font-medium">Retirer</span>
                                    </button>
                                @endif
                            @endcan

                        @elseif($attribution->childAttributions->count() == 0)
                            @can('attributions_lots.reassign')
                                <a href="{{ route('attributions.reattribuer.form', $attribution->id_attribution) }}"
                                    class="px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md">
                                    <i class="fas fa-redo text-sm"></i>
                                    <span class="text-sm font-medium">Réattribuer ce lot</span>
                                </a>
                            @endcan
                        @endif

                        @canany(['attributions_lots.view-history', 'prestataires.read', 'attributions_lots.reassign'])
                            <!-- Menu dropdown -->
                            <div class="relative">
                                <button onclick="toggleMenu()" id="menuBtn"
                                    class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-ellipsis-v text-sm"></i>
                                </button>
                                <div id="actionMenu"
                                    class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-20">
                                    <div class="py-1">
                                        @can('attributions_lots.view-history')
                                            <a href="{{ route('attributions.historique.lot', $attribution->lot_id) }}"
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <i class="fas fa-history mr-2 text-purple-500"></i>
                                                Historique du lot
                                            </a>
                                        @endcan

                                        @can('prestataires.read')
                                            <a href="{{ route('attributions.historique.prestataire', $attribution->prestataire_id) }}"
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <i class="fas fa-user-clock mr-2 text-blue-500"></i>
                                                Historique prestataire
                                            </a>
                                        @endcan

                                        @can('attributions_lots.reassign')
                                            @if ($attribution->is_active)
                                                <a href="{{ route('attributions.edit', $attribution->id_attribution) }}"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="fas fa-edit mr-2 text-orange-500"></i>
                                                    Modifier
                                                </a>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endcanany
                    </div>
                @endcanany --}}

                @canany(['evaluations_attributions.read', 'attributions_lots.suspend', 'attributions_lots.resume',
                    'attributions_lots.withdraw', 'attributions_lots.view-history', 'prestataires.read',
                    'attributions_lots.reassign'])
                    <!-- Actions -->
                    <div class="flex items-center space-x-2 flex-wrap gap-2">

                        @if ($attribution->is_active)
                            @canany(['evaluations_attributions.read', 'attributions_lots.suspend'])
                                @if ($attribution->peutEtreSuspendue())
                                    @can('evaluations_attributions.read')
                                        <a href="{{ route('evaluations.pour-attribution', $attribution->id_attribution) }}"
                                            class="px-4 py-2.5 bg-white border border-emerald-300 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                            <i class="fas fa-star text-sm"></i>
                                            <span class="text-sm font-medium">Évaluations</span>
                                        </a>
                                    @endcan

                                    @can('attributions_lots.suspend')
                                        <button onclick="openSuspendreModal()"
                                            class="px-4 py-2.5 bg-white border border-amber-300 text-amber-600 hover:bg-amber-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                            <i class="fas fa-pause-circle text-sm"></i>
                                            <span class="text-sm font-medium">Suspendre</span>
                                        </button>
                                    @endcan

                                    @can('attributions_lots.reassign')
                                        @if ($attribution->pourcentage_avancement == 100 && !$attribution->date_effective_fin)
                                            <button onclick="openDateEffectiveFinModal()"
                                                class="px-4 py-2.5 bg-white border border-blue-300 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                                <i class="fas fa-calendar-check text-sm"></i>
                                                <span class="text-sm font-medium">Date effective de fin</span>
                                            </button>
                                        @endif
                                    @endcan
                                @endif
                            @endcanany

                            @can('attributions_lots.resume')
                                @if ($attribution->peutEtreReprise())
                                    <button onclick="reprendre()"
                                        class="px-4 py-2.5 bg-white border border-green-300 text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                        <i class="fas fa-play-circle text-sm"></i>
                                        <span class="text-sm font-medium">Reprendre</span>
                                    </button>
                                @endif
                            @endcan

                            @can('attributions_lots.withdraw')
                                @if ($attribution->peutEtreRetiree())
                                    <button onclick="openRetirerModal()"
                                        class="px-4 py-2.5 bg-white border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                        <i class="fas fa-times-circle text-sm"></i>
                                        <span class="text-sm font-medium">Retirer</span>
                                    </button>
                                @endif
                            @endcan

                        @elseif($attribution->childAttributions->count() == 0)
                            @can('attributions_lots.reassign')
                                <a href="{{ route('attributions.reattribuer.form', $attribution->id_attribution) }}"
                                    class="px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md">
                                    <i class="fas fa-redo-alt text-sm"></i>
                                    <span class="text-sm font-medium">Réattribuer ce lot</span>
                                </a>
                            @endcan
                        @endif

                        @canany(['attributions_lots.view-history', 'prestataires.read', 'attributions_lots.reassign'])
                            <!-- Menu dropdown -->
                            <div class="relative">
                                <button onclick="toggleMenu()" id="menuBtn"
                                    class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-ellipsis-v text-sm"></i>
                                </button>
                                <div id="actionMenu"
                                    class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-20">
                                    <div class="py-1">
                                        @can('attributions_lots.view-history')
                                            <a href="{{ route('attributions.historique.lot', $attribution->lot_id) }}"
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 flex items-center group">
                                                <i class="fas fa-history mr-2 text-purple-500 group-hover:text-purple-600"></i>
                                                <span class="group-hover:text-purple-700">Historique du lot</span>
                                            </a>
                                        @endcan

                                        @can('prestataires.read')
                                            <a href="{{ route('attributions.historique.prestataire', $attribution->prestataire_id) }}"
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 flex items-center group">
                                                <i class="fas fa-user-clock mr-2 text-blue-500 group-hover:text-blue-600"></i>
                                                <span class="group-hover:text-blue-700">Historique prestataire</span>
                                            </a>
                                        @endcan

                                        @can('attributions_lots.reassign')
                                            @if ($attribution->is_active)
                                                <a href="{{ route('attributions.edit', $attribution->id_attribution) }}"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 flex items-center group">
                                                    <i class="fas fa-edit mr-2 text-orange-500 group-hover:text-orange-600"></i>
                                                    <span class="group-hover:text-orange-700">Modifier</span>
                                                </a>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endcanany
                    </div>
                @endcanany
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

        <!-- Avancement Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Avancement des travaux</h3>
                        <div class="flex items-center space-x-4">
                            <div class="flex-1 bg-gray-200 rounded-full h-4">
                                <div class="h-4 rounded-full transition-all duration-500 {{ $attribution->pourcentage_avancement >= 100 ? 'bg-green-500' : 'bg-gradient-to-r from-orange-400 to-orange-500' }}"
                                    style="width: {{ min($attribution->pourcentage_avancement, 100) }}%"></div>
                            </div>
                            <span
                                class="text-2xl font-bold text-gray-800">{{ number_format($attribution->pourcentage_avancement, 0) }}%</span>
                        </div>
                    </div>



                    @php
                        $facture = $attribution->proforma->facture;
                        $montant_net_paye_paiement = $facture
                            ? $facture->paiementsValides->sum('montant_net_paye_paiement')
                            : 0;
                        $montant_reste_paiement = $facture ? $facture->montant_facture - $montant_net_paye_paiement : 0;
                    @endphp

                    <div class="flex items-center space-x-6 text-sm">
                        <div class="text-center">
                            <p class="text-gray-500">Montant engagé</p>
                            <p class="text-lg font-bold text-gray-800">
                                {{ $facture ? number_format($facture->montant_facture, 0, ',', ' ') : 0 }} <span
                                    class="text-xs">FCFA</span></p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-500">Montant payé</p>
                            <p class="text-lg font-bold text-green-600">
                                {{ number_format($montant_net_paye_paiement, 0, ',', ' ') }} <span
                                    class="text-xs">FCFA</span></p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-500">Restant</p>
                            <p class="text-lg font-bold text-orange-600">
                                {{ number_format($montant_reste_paiement, 0, ',', ' ') }} <span class="text-xs">FCFA</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Informations du lot -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-box text-indigo-500 mr-2"></i>
                            Lot attribué
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Numéro du lot</label>
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">
                                    {{ $attribution->lot->numero ?? 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Appel d'offres</label>
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-orange-100 text-orange-700">
                                    {{ $attribution->lot->appelOffre->numero_appel_offre ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Libellé</label>
                            <p class="text-gray-900 font-medium">{{ $attribution->lot->libelle ?? 'N/A' }}</p>
                        </div>
                        @if ($attribution->lot->description_critere ?? false)
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Description</label>
                                <p class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-lg">
                                    {{ $attribution->lot->description_critere }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations du prestataire -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-building text-green-500 mr-2"></i>
                            Prestataire attributaire
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Nom du prestataire</label>
                                <p class="text-gray-900 font-medium">
                                    {{ $attribution->prestataire->raison_sociale_prestataire }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Ville</label>
                                <p class="text-gray-700">{{ $attribution->prestataire->ville_prestataire ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Téléphone</label>
                                <p class="text-gray-700">{{ $attribution->prestataire->telephone_principal_prestataire }}
                                </p>
                                @if ($attribution->prestataire->telephone_secondaire_prestataire)
                                    <p class="text-gray-700">
                                        {{ $attribution->prestataire->telephone_secondaire_prestataire }}</p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Email</label>
                                <p class="text-gray-700">{{ $attribution->prestataire->email_prestataire }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dates et délais -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                            Dates et Délais
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gradient-to-br from-orange-50 to-white p-5 rounded-xl border border-orange-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Attribution</span>
                                    <i class="fas fa-handshake text-orange-500"></i>
                                </div>
                                @if ($attribution->date_attribution)
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ $attribution->date_attribution->format('d/m/Y') }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $attribution->date_attribution->diffForHumans() }}</p>
                                @else
                                    <p class="text-sm text-gray-500">Non définie</p>
                                @endif
                            </div>

                            <div class="bg-gradient-to-br from-blue-50 to-white p-5 rounded-xl border border-blue-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Début prévu</span>
                                    <i class="fas fa-play text-blue-500"></i>
                                </div>
                                @if ($attribution->date_debut_prevue)
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ $attribution->date_debut_prevue->format('d/m/Y') }}</p>
                                    @if ($attribution->date_debut_reelle)
                                        <p class="text-xs text-green-600 mt-1">
                                            <i class="fas fa-check mr-1"></i>Réel:
                                            {{ $attribution->date_debut_reelle->format('d/m/Y') }}
                                        </p>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-500">Non définie</p>
                                @endif
                            </div>

                            <div
                                class="bg-gradient-to-br from-{{ $attribution->estEnRetard() ? 'red' : 'green' }}-50 to-white p-5 rounded-xl border border-{{ $attribution->estEnRetard() ? 'red' : 'green' }}-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Fin prévue</span>
                                    <i
                                        class="fas fa-flag-checkered text-{{ $attribution->estEnRetard() ? 'red' : 'green' }}-500"></i>
                                </div>
                                @if ($attribution->date_fin_prevue)
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ $attribution->date_fin_prevue->format('d/m/Y') }}</p>
                                    @if ($attribution->estEnRetard())
                                        <p class="text-xs text-red-600 font-semibold mt-1">
                                            <i
                                                class="fas fa-exclamation-triangle mr-1"></i>{{ $attribution->jours_retard_actuels }}
                                            jour(s) de retard
                                        </p>
                                    @elseif($attribution->date_fin_reelle)
                                        <p class="text-xs text-green-600 mt-1">
                                            <i class="fas fa-check mr-1"></i>Terminé le
                                            {{ $attribution->date_fin_reelle->format('d/m/Y') }}
                                        </p>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-500">Non définie</p>
                                @endif
                            </div>
                        </div>

                        @if ($attribution->duree_prevue)
                            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Durée prévue: </span>
                                <span class="font-semibold text-gray-800">{{ $attribution->duree_prevue }} jours</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Suspension / Retrait -->
                @if ($attribution->statut_attribution === 2 || $attribution->statut_attribution === 3)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div
                            class="px-6 py-4 bg-gradient-to-r from-{{ $attribution->statut_attribution === 2 ? 'yellow' : 'red' }}-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i
                                    class="fas fa-{{ $attribution->statut_attribution === 2 ? 'pause-circle' : 'ban' }} text-{{ $attribution->statut_attribution === 2 ? 'yellow' : 'red' }}-500 mr-2"></i>
                                {{ $attribution->statut_attribution === 2 ? 'Informations de suspension' : 'Informations de retrait' }}
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            @if ($attribution->statut_attribution === 2)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-600 mb-2">Date de
                                            suspension</label>
                                        <p class="text-gray-900">
                                            {{ $attribution->date_suspension ? $attribution->date_suspension->format('d/m/Y H:i') : 'N/A' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-600 mb-2">Reprise
                                            prévue</label>
                                        <p class="text-gray-900">
                                            {{ $attribution->date_reprise_prevue ? $attribution->date_reprise_prevue->format('d/m/Y') : 'Non définie' }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Motif de
                                        suspension</label>
                                    <p class="text-gray-700 bg-yellow-50 p-4 rounded-lg">
                                        {{ $attribution->motif_suspension }}</p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-600 mb-2">Date de
                                            retrait</label>
                                        <p class="text-gray-900">
                                            {{ $attribution->date_retrait ? $attribution->date_retrait->format('d/m/Y H:i') : 'N/A' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-600 mb-2">Type de
                                            retrait</label>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            {{ ucfirst($attribution->type_retrait ?? 'N/A') }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Motif de retrait</label>
                                    <p class="text-gray-700 bg-red-50 p-4 rounded-lg">{{ $attribution->motif_retrait }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Observations -->
                @if ($attribution->observations || $attribution->conditions_particulieres)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-comment-alt text-gray-500 mr-2"></i>
                                Notes et observations
                            </h2>
                        </div>
                        <div class="p-6 space-y-5">
                            @if ($attribution->observations)
                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Observations</label>
                                    <p class="text-gray-700 bg-gray-50 p-4 rounded-lg whitespace-pre-wrap">
                                        {{ $attribution->observations }}</p>
                                </div>
                            @endif
                            @if ($attribution->conditions_particulieres)
                                <div>
                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Conditions
                                        particulières</label>
                                    <p class="text-gray-700 bg-gray-50 p-4 rounded-lg whitespace-pre-wrap">
                                        {{ $attribution->conditions_particulieres }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">

                <!-- Proforma -->
                {{-- <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-invoice text-purple-500 mr-2"></i>
                            Proforma
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Numéro</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-purple-100 text-purple-700">
                                {{ $attribution->proforma->numero_proforma ?? 'N/A' }}
                            </span>
                        </div>
                        @if ($attribution->proforma)
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Montant TTC</label>
                                <p class="text-xl font-bold text-gray-900">
                                    {{ number_format($attribution->proforma->montant_ttc ?? 0, 0, ',', ' ') }}
                                    <span class="text-sm text-gray-500">FCFA</span>
                                </p>
                            </div>
                        @endif
                    </div>
                </div> --}}

                {{-- Section Proforma complète pour show.blade.php --}}
                {{-- Remplacer la section proforma existante par celle-ci --}}

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-file-invoice text-purple-500 mr-2"></i>
                                Proforma
                            </h2>
                            @if ($attribution->proforma)
                                <div class="flex items-center space-x-2">
                                    @if ($attribution->proforma->version_proforma > 1)
                                        <span
                                            class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">
                                            <i
                                                class="fas fa-code-branch mr-1"></i>V{{ $attribution->proforma->version_proforma }}
                                        </span>
                                    @endif
                                    @if ($attribution->proforma->actif_proforma)
                                        <span
                                            class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                            <i class="fas fa-check-circle mr-1"></i>Active
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">
                                            <i class="fas fa-times-circle mr-1"></i>Inactive
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($attribution->proforma)
                        <div class="p-6 space-y-5">
                            {{-- Numéro et Date --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Numéro</label>
                                    <span
                                        class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-bold bg-purple-100 text-purple-700">
                                        <i class="fas fa-hashtag mr-2 text-purple-400"></i>
                                        {{ $attribution->proforma->numero_proforma }}
                                    </span>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Date
                                        proforma</label>
                                    <p class="text-sm font-medium text-gray-800">
                                        <i class="fas fa-calendar text-purple-400 mr-2"></i>
                                        {{ $attribution->proforma->date_proforma ? $attribution->proforma->date_proforma->format('d/m/Y') : 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Dates et délais importantes --}}
                            <div
                                class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-4 border border-purple-100">
                                <h4 class="text-sm font-semibold text-purple-800 mb-3">
                                    <i class="fas fa-calendar-alt mr-2"></i>Dates clés
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="bg-white/70 rounded-lg p-3">
                                        <label class="block text-xs text-gray-500 mb-1">Date début validée</label>
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $attribution->proforma->date_debut_validee_proforma ? $attribution->proforma->date_debut_validee_proforma->format('d/m/Y') : '-' }}
                                        </p>
                                    </div>

                                    <div class="bg-white/70 rounded-lg p-3">
                                        <label class="block text-xs text-gray-500 mb-1">Date fin validée</label>
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $attribution->proforma->date_fin_validee_proforma ? $attribution->proforma->date_fin_validee_proforma->format('d/m/Y') : '-' }}
                                        </p>
                                    </div>
                                    {{-- <div class="bg-white/70 rounded-lg p-3">
                                        <label class="block text-xs text-gray-500 mb-1">En retard de</label>
                                        <p class="text-sm font-semibold {{ $attribution->date_debut_prevue ? 'text-green-700' : 'text-gray-800' }}">
                                            @if ($attribution->date_debut_prevue && $attribution->date_effective_fin)
                                                <i class="fas fa-play-circle mr-1"></i>
                                            @endif
                                            {{ $attribution->date_debut_prevue && $attribution->date_effective_fin  ? $attribution->date_effective_fin->format('d/m/Y') : '-' }}
                                        </p>
                                    </div> --}}



                                    <div class="bg-white/70 rounded-lg p-3">
                                        @php
                                            $dateFin =
                                                $attribution->proforma->date_fin_validee_proforma ??
                                                $attribution->date_fin_prevue;
                                            $dateEffective = $attribution->date_effective_fin;
                                            $aujourdhui = now();

                                            if ($dateEffective) {
                                                // Si les travaux sont terminés, comparer date effective avec date fin prévue
                                                $difference = $dateFin
                                                    ? $dateFin->diffInDays($dateEffective, false)
                                                    : null;
                                                $estEnRetard = $difference > 0;
                                                $joursRetard = abs($difference);
                                                $estTermine = true;
                                            } else {
                                                // Si les travaux sont en cours, comparer aujourd'hui avec date fin prévue
                                                $difference = $dateFin
                                                    ? $dateFin->diffInDays($aujourdhui, false)
                                                    : null;
                                                $estEnRetard = $difference > 0;
                                                $joursRetard = abs($difference);
                                                $estTermine = false;
                                            }
                                        @endphp

                                        @if ($dateFin)
                                            @if ($estTermine)
                                                {{-- Travaux terminés --}}
                                                @if ($estEnRetard)
                                                    <label class="block text-xs text-red-500 mb-1">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>Terminé en retard
                                                    </label>
                                                    <p class="text-sm font-semibold text-red-700">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        {{ $joursRetard }} jour{{ $joursRetard > 1 ? 's' : '' }} de
                                                        retard
                                                    </p>
                                                @else
                                                    <label class="block text-xs text-green-500 mb-1">
                                                        <i class="fas fa-check-circle mr-1"></i>Terminé dans les délais
                                                    </label>
                                                    <p class="text-sm font-semibold text-green-700">
                                                        <i class="fas fa-thumbs-up mr-1"></i>
                                                        {{ $joursRetard > 0 ? $joursRetard . ' jour' . ($joursRetard > 1 ? 's' : '') . ' d\'avance' : 'À temps' }}
                                                    </p>
                                                @endif
                                            @else
                                                {{-- Travaux en cours --}}
                                                @if ($estEnRetard)
                                                    <label class="block text-xs text-red-500 mb-1">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>Hors délai
                                                    </label>
                                                    <p class="text-sm font-semibold text-red-700">
                                                        <i class="fas fa-hourglass-end mr-1"></i>
                                                        {{ $joursRetard }} jour{{ $joursRetard > 1 ? 's' : '' }} de
                                                        retard
                                                    </p>
                                                @else
                                                    <label class="block text-xs text-blue-500 mb-1">
                                                        <i class="fas fa-running mr-1"></i>Dans les délais
                                                    </label>
                                                    <p class="text-sm font-semibold text-blue-700">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        {{ $joursRetard }} jour{{ $joursRetard > 1 ? 's' : '' }}
                                                        restant{{ $joursRetard > 1 ? 's' : '' }}
                                                    </p>
                                                @endif
                                            @endif
                                        @else
                                            <label class="block text-xs text-gray-500 mb-1">Statut</label>
                                            <p class="text-sm font-semibold text-gray-800">
                                                <i class="fas fa-question-circle mr-1"></i>
                                                Non défini
                                            </p>
                                        @endif
                                    </div>




                                </div>
                            </div>

                            {{-- Détails financiers --}}
                            <div
                                class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100">
                                <h4 class="text-sm font-semibold text-green-800 mb-3">
                                    <i class="fas fa-coins mr-2"></i>Détails financiers
                                </h4>
                                <div class="space-y-3">
                                    {{-- Montant HT --}}
                                    <div class="flex justify-between items-center py-2 border-b border-green-100">
                                        <span class="text-sm text-gray-600">Montant hors taxe (HT)</span>
                                        <span class="text-sm font-semibold text-gray-800">
                                            {{ number_format($attribution->proforma->montant_retenu_proforma ?? 0, 2, ',', ' ') }}
                                            FCFA
                                        </span>
                                    </div>

                                    {{-- Remise --}}
                                    @if ($attribution->proforma->remise_montant_proforma > 0)
                                        <div class="flex justify-between items-center py-2 border-b border-green-100">
                                            <span class="text-sm text-gray-600">
                                                <i class="fas fa-tags text-orange-500 mr-1"></i>
                                                Remise ({{ $attribution->proforma->pourcentage_remise }}%)
                                            </span>
                                            <span class="text-sm font-semibold text-orange-600">
                                                -
                                                {{ number_format($attribution->proforma->remise_montant_proforma ?? 0, 2, ',', ' ') }}
                                                FCFA
                                            </span>
                                        </div>
                                        <div
                                            class="flex justify-between items-center py-2 border-b border-green-100 bg-gray-50 -mx-4 px-4">
                                            <span class="text-sm text-gray-600">Montant HT après remise</span>
                                            <span class="text-sm font-semibold text-gray-800">
                                                {{ number_format($attribution->proforma->montant_ht_apres_remise ?? 0, 2, ',', ' ') }}
                                                FCFA
                                            </span>
                                        </div>
                                    @endif

                                    {{-- TVA --}}
                                    <div class="flex justify-between items-center py-2 border-b border-green-100">
                                        <span class="text-sm text-gray-600">
                                            <i class="fas fa-percentage text-blue-500 mr-1"></i>
                                            TVA ({{ $attribution->proforma->taux_taxe }}%)
                                        </span>
                                        <span class="text-sm font-semibold text-blue-600">
                                            + {{ number_format($attribution->proforma->taxe_montant ?? 0, 2, ',', ' ') }}
                                            FCFA
                                        </span>
                                    </div>

                                    {{-- Total TTC --}}
                                    <div
                                        class="flex justify-between items-center py-3 bg-gradient-to-r from-green-600 to-emerald-600 -mx-4 px-4 -mb-4 rounded-b-xl">
                                        <span class="text-sm font-semibold text-white">
                                            <i class="fas fa-calculator mr-2"></i>Total TTC
                                        </span>
                                        <span class="text-xl font-bold text-white">
                                            {{ number_format($attribution->proforma->montant_ttc ?? 0, 2, ',', ' ') }} FCFA
                                        </span>
                                    </div>
                                </div>
                            </div>



                            {{-- Modalités de paiement --}}
                            @if ($attribution->proforma->modalite_proforma)
                                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                    <h4 class="text-sm font-semibold text-blue-800 mb-2">
                                        <i class="fas fa-credit-card mr-2"></i>Modalités de paiement
                                    </h4>
                                    <p class="text-sm text-gray-700">
                                        {{ $attribution->proforma->modalite_proforma }}
                                    </p>
                                </div>
                            @endif

                            {{-- Motif de modification si version > 1 --}}
                            @if ($attribution->proforma->version_proforma > 1 && $attribution->proforma->motif_modification_proforma)
                                <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                                    <h4 class="text-sm font-semibold text-amber-800 mb-2">
                                        <i class="fas fa-edit mr-2"></i>Motif de la modification
                                        (V{{ $attribution->proforma->version_proforma }})
                                    </h4>
                                    <p class="text-sm text-gray-700 italic">
                                        "{{ $attribution->proforma->motif_modification_proforma }}"
                                    </p>
                                </div>
                            @endif

                            {{-- Lien vers historique si versions multiples --}}
                            @if ($attribution->proforma->parent_id || $attribution->proforma->versions()->count() > 0)
                                <div class="pt-3 border-t border-gray-100">
                                    <a href="#"
                                        class="text-sm text-purple-600 hover:text-purple-800 font-medium inline-flex items-center">
                                        <i class="fas fa-history mr-2"></i>
                                        Voir l'historique des versions
                                        <i class="fas fa-chevron-right ml-2 text-xs"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-6">
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-file-invoice text-4xl text-gray-300 mb-3"></i>
                                <p>Aucune proforma associée</p>
                            </div>
                        </div>
                    @endif
                </div>



                <!-- Traçabilité -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-fingerprint text-gray-500 mr-2"></i>
                            Traçabilité
                        </h2>
                    </div>
                    <div class="p-6 space-y-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Créé par</span>
                            <span
                                class="font-medium text-gray-800">{{ $attribution->createdBy->nom_complet ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Créé le</span>
                            <span
                                class="font-medium text-gray-800">{{ $attribution->created_at ? $attribution->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>

                        @can('attributions_lots.view-details')
                            @if ($attribution->parentAttribution)
                                <hr class="border-gray-200">
                                <div>
                                    <span class="text-gray-600">Réattribution de</span>
                                    <a href="{{ route('attributions.show', $attribution->parent_attribution_id) }}"
                                        class="block mt-1 text-orange-600 hover:text-orange-800 font-medium">
                                        <i
                                            class="fas fa-link mr-1"></i>{{ $attribution->parentAttribution->numero_attribution }}
                                    </a>
                                </div>
                            @endif
                        @endcan
                    </div>
                </div>

                @can('attributions_lots.view-history')
                    <!-- Historique du lot -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-history text-purple-500 mr-2"></i>
                                    Historique
                                </h2>
                                <span class="px-2.5 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                                    {{ $historiqueLot->count() }} version(s)
                                </span>
                            </div>
                        </div>
                        <div class="p-4 max-h-80 overflow-y-auto custom-scrollbar">
                            @foreach ($historiqueLot as $historique)
                                <div
                                    class="flex items-start space-x-3 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                    <div class="flex-shrink-0">
                                        <span
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $historique->id_attribution === $attribution->id_attribution ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-500' }} text-xs font-bold">
                                            v{{ $historique->version_attribution }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('attributions.show', $historique->id_attribution) }}"
                                            class="text-sm font-medium {{ $historique->id_attribution === $attribution->id_attribution ? 'text-orange-600' : 'text-gray-900 hover:text-orange-600' }}">
                                            {{ $historique->numero_attribution }}
                                        </a>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ Str::limit($historique->prestataire->raison_sociale_prestataire ?? '', 25) }}
                                        </p>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $historique->statut_badge_class }}">
                                            {{ $historique->statut_label }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endcan

            </div>
        </div>
    </main>


    <!-- Modal Suspendre -->
    <div id="suspendreModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeSuspendreModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800"><i
                            class="fas fa-pause-circle text-yellow-500 mr-2"></i>Suspendre</h3>
                </div>
                @can('attributions_lots.suspend')
                    <form action="{{ route('attributions.suspendre', $attribution->id_attribution) }}" method="POST">
                        @csrf
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Motif <span
                                        class="text-red-500">*</span></label>
                                <textarea name="motif_suspension" rows="3" required minlength="10"
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-yellow-400"
                                    placeholder="Raison de la suspension..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date reprise prévue <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="date_reprise_prevue" required
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                            <button type="button" onclick="closeSuspendreModal()"
                                class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">Annuler</button>
                            <button type="submit"
                                class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg">Suspendre</button>
                        </div>
                    </form>
                @endcan
            </div>
        </div>
    </div>


    <!-- Modal Date effective de fin -->
    <div id="dateEffectiveFinModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeDateEffectiveFinModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800"><i
                            class="fas fa-calendar-alt text-yellow-500 mr-2"></i>Date effective de fin des travaux</h3>
                </div>
                @can('attributions_lots.reassign')
                    <form action="{{ route('attributions.ajout_date_effective_fin', $attribution->id_attribution) }}"
                        method="POST">
                        @csrf
                        <div class="p-6 space-y-4">

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date reprise prévue <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="date_effective_fin" id="date_effective_fin" required
                                    min="{{ date('Y-m-d', strtotime($attribution->date_debut_prevue)) }}"
                                    max="{{ date('Y-m-d', strtotime('0 day')) }}"
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-yellow-400">
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                            <button type="button" onclick="closeDateEffectiveFinModal()"
                                class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">Annuler</button>
                            <button type="submit"
                                class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg">Enregistrer</button>
                        </div>
                    </form>
                @endcan
            </div>
        </div>
    </div>

    <!-- Modal Retirer -->
    <div id="retirerModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeRetirerModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-ban text-red-500 mr-2"></i>Retirer</h3>
                </div>
                @can('attributions_lots.withdraw')
                    <form action="{{ route('attributions.retirer', $attribution->id_attribution) }}" method="POST">
                        @csrf
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Type *</label>
                                <select name="type_retrait" required
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-red-400">
                                    <option value="">Sélectionnez...</option>
                                    <option value="volontaire">Volontaire</option>
                                    <option value="force">Forcé</option>
                                    <option value="resiliation">Résiliation</option>
                                    <option value="abandon">Abandon</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Motif *</label>
                                <textarea name="motif_retrait" rows="3" required minlength="10"
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-red-400" placeholder="Raison du retrait..."></textarea>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                            <button type="button" onclick="closeRetirerModal()"
                                class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">Annuler</button>
                            <button type="submit"
                                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">Retirer</button>
                        </div>
                    </form>
                @endcan
            </div>
        </div>
    </div>

    <!-- Modal Terminer -->
    <div id="terminerModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeTerminerModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800"><i
                            class="fas fa-check-double text-blue-500 mr-2"></i>Terminer</h3>
                </div>
                <form action="{{ route('attributions.terminer', $attribution->id_attribution) }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>L'avancement sera marqué à 100%.
                            @if ($attribution->estEnRetard())
                                <br><span class="text-red-600 font-semibold mt-2 block"><i
                                        class="fas fa-exclamation-triangle mr-1"></i>{{ $attribution->jours_retard_actuels }}
                                    jour(s) de retard seront enregistrés.</span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Observations</label>
                            <textarea name="observations" rows="3"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400" placeholder="Notes de clôture..."></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeTerminerModal()"
                            class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">Annuler</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">Terminer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Avancement -->
    <div id="avancementModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeAvancementModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800"><i
                            class="fas fa-tasks text-orange-500 mr-2"></i>Avancement</h3>
                </div>
                <form action="{{ route('attributions.avancement', $attribution->id_attribution) }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pourcentage *</label>
                            <div class="flex items-center space-x-4">
                                <input type="range" name="pourcentage_avancement" id="avancementRange" min="0"
                                    max="100" value="{{ $attribution->pourcentage_avancement }}"
                                    class="flex-1 h-2 bg-gray-200 rounded-lg cursor-pointer"
                                    oninput="document.getElementById('avancementValue').value = this.value">
                                <input type="number" id="avancementValue" min="0" max="100"
                                    value="{{ $attribution->pourcentage_avancement }}"
                                    class="w-20 px-3 py-2 border rounded-lg text-center font-semibold"
                                    oninput="document.getElementById('avancementRange').value = this.value">
                                <span class="text-gray-500">%</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Observations</label>
                            <textarea name="observations" rows="3"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-400" placeholder="Notes..."></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeAvancementModal()"
                            class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">Annuler</button>
                        <button type="submit"
                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@can('attributions_lots.view-details')
    @push('scripts')
        <script>
            function toggleMenu() {
                document.getElementById('actionMenu').classList.toggle('hidden');
            }

            function openSuspendreModal() {
                document.getElementById('suspendreModal').classList.remove('hidden');
            }



            function closeSuspendreModal() {
                document.getElementById('suspendreModal').classList.add('hidden');
            }

            function openDateEffectiveFinModal() {
                document.getElementById('dateEffectiveFinModal').classList.remove('hidden');
            }

            function closeDateEffectiveFinModal() {
                document.getElementById('dateEffectiveFinModal').classList.add('hidden');
            }

            function openRetirerModal() {
                document.getElementById('retirerModal').classList.remove('hidden');
            }

            function closeRetirerModal() {
                document.getElementById('retirerModal').classList.add('hidden');
            }

            function openTerminerModal() {
                document.getElementById('terminerModal').classList.remove('hidden');
            }

            function closeTerminerModal() {
                document.getElementById('terminerModal').classList.add('hidden');
            }

            function openAvancementModal() {
                document.getElementById('avancementModal').classList.remove('hidden');
            }

            function closeAvancementModal() {
                document.getElementById('avancementModal').classList.add('hidden');
            }

            function reprendre() {
                if (confirm('Confirmer la reprise ?')) {
                    fetch(`/attributions/{{ $attribution->id_attribution }}/reprendre`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(r => r.json()).then(d => {
                        if (d.success) location.reload();
                        else alert(d.message);
                    });
                }
            }

            document.addEventListener('click', e => {
                if (!e.target.closest('#actionMenu') && !e.target.closest('#menuBtn')) document.getElementById(
                    'actionMenu').classList.add('hidden');
            });
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    closeSuspendreModal();
                    closeRetirerModal();
                    closeTerminerModal();
                    closeAvancementModal();
                    document.getElementById('actionMenu').classList.add('hidden');
                }
            });
        </script>
        <style>
            .animate-fadeIn {
                animation: fadeIn 0.3s ease-out;
            }

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

            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f5f9;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 3px;
            }

            input[type="range"]::-webkit-slider-thumb {
                -webkit-appearance: none;
                width: 20px;
                height: 20px;
                border-radius: 50%;
                background: #f97316;
                cursor: pointer;
                border: 2px solid white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }
        </style>
    @endpush
@endcan
