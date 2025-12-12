@extends('layouts.main')
@section('title', 'Proforma - ' . $proforma->numero_proforma)
@section('breadcrumb')
    <a href="{{ route('proformas.index') }}" class="text-white/80 hover:text-white transition-colors">Proformas</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">{{ $proforma->numero_proforma }}</span>
@endsection

@section('content')
    <!-- Header avec actions -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et retour -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('proformas.index') }}"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <div>
                        <div class="flex items-center space-x-3 flex-wrap">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $proforma->numero_proforma }}</h1>
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                v{{ $proforma->version_proforma }}
                            </span>
                            @if ($proforma->actif_proforma)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Active
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <i class="fas fa-times-circle mr-1"></i> Inactive
                                </span>
                            @endif
                            @if ($proforma->parent_id)
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                    <i class="fas fa-code-branch mr-1"></i> Version modifiée
                                </span>
                            @endif
                        </div>
                        <p class="text-gray-600 mt-1">
                            @if ($proforma->date_proforma)
                                <i class="fas fa-calendar mr-1"></i>{{ $proforma->date_proforma->format('d/m/Y') }}
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2 flex-wrap">
                    <button onclick="window.location.href='{{ route('proformas.edit', $proforma->id_proforma) }}'"
                        class="px-4 py-2.5 bg-white border border-orange-300 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                        <i class="fas fa-edit text-sm"></i>
                        <span class="text-sm font-medium">Modifier</span>
                    </button>

                    <button onclick="toggleStatus({{ $proforma->actif_proforma ? 'true' : 'false' }})"
                        class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                        <i class="fas fa-power-off text-sm"></i>
                        <span class="text-sm font-medium">{{ $proforma->actif_proforma ? 'Désactiver' : 'Activer' }}</span>
                    </button>

                    <!-- Menu dropdown -->
                    <div class="relative">
                        <button onclick="toggleMenu()" id="menuBtn"
                            class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-ellipsis-v text-sm"></i>
                        </button>
                        <div id="actionMenu"
                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-20">
                            <div class="py-1">
                                <button onclick="creerVersion()"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-code-branch mr-2 text-indigo-500"></i>
                                    Nouvelle version
                                </button>
                                <button onclick="duplicate()"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-copy mr-2 text-purple-500"></i>
                                    Dupliquer
                                </button>
                                <button onclick="printProforma()"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-print mr-2 text-gray-500"></i>
                                    Imprimer
                                </button>
                                <button onclick="confirmDelete()"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                    <i class="fas fa-trash mr-2"></i>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Résumé financier -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-calculator text-green-500 mr-2"></i>
                            Résumé financier
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Montant HT -->
                            <div class="bg-gradient-to-br from-blue-50 to-white p-5 rounded-xl border border-blue-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Montant HT</span>
                                    <i class="fas fa-file-invoice text-blue-500"></i>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ number_format($proforma->montant_retenu_proforma, 0, ',', ' ') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">FCFA</p>
                            </div>

                            <!-- Remise -->
                            <div class="bg-gradient-to-br from-red-50 to-white p-5 rounded-xl border border-red-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Remise</span>
                                    <i class="fas fa-percentage text-red-500"></i>
                                </div>
                                <p class="text-2xl font-bold text-red-600">
                                    -{{ number_format($proforma->remise_montant_proforma, 0, ',', ' ') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $proforma->pourcentage_remise }}% du montant HT
                                </p>
                            </div>

                            <!-- Montant HT après remise -->
                            <div class="bg-gradient-to-br from-gray-50 to-white p-5 rounded-xl border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Sous-total HT</span>
                                    <i class="fas fa-equals text-gray-500"></i>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ number_format($proforma->montant_ht_apres_remise, 0, ',', ' ') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">FCFA</p>
                            </div>

                            <!-- Taxes -->
                            <div class="bg-gradient-to-br from-yellow-50 to-white p-5 rounded-xl border border-yellow-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-600">Taxes</span>
                                    <i class="fas fa-receipt text-yellow-500"></i>
                                </div>
                                <p class="text-2xl font-bold text-yellow-600">
                                    +{{ number_format($proforma->taxe_montant, 0, ',', ' ') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $proforma->taux_taxe }}% du sous-total
                                </p>
                            </div>
                        </div>

                        <!-- Montant TTC -->
                        <div class="mt-6 p-6 bg-gradient-to-r from-green-500 to-green-600 rounded-xl text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100 text-sm font-medium">Montant Total TTC</p>
                                    <p class="text-4xl font-bold mt-1">
                                        {{ number_format($proforma->montant_ttc, 0, ',', ' ') }}
                                        <span class="text-lg font-normal">FCFA</span>
                                    </p>
                                </div>
                                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-coins text-3xl"></i>
                                </div>
                            </div>
                        </div>

                        @if ($proforma->penalites_proforma > 0)
                            <!-- Pénalités -->
                            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                        <span class="text-sm font-semibold text-red-700">Pénalités applicables</span>
                                    </div>
                                    <span class="text-lg font-bold text-red-600">
                                        {{ number_format($proforma->penalites_proforma, 0, ',', ' ') }} FCFA
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations détaillées -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                            Informations détaillées
                        </h2>
                    </div>

                    <div class="p-6 space-y-5">
                        <!-- Numéro et Version -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Numéro</label>
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-orange-100 text-orange-700">
                                    {{ $proforma->numero_proforma }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Version</label>
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-blue-100 text-blue-700">
                                    Version {{ $proforma->version_proforma }}
                                </span>
                            </div>
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Date de la proforma</label>
                            <p class="text-gray-900 font-medium">
                                {{ $proforma->date_proforma ? $proforma->date_proforma->format('d/m/Y') : 'Non définie' }}
                            </p>
                        </div>

                        <!-- Modalités de paiement -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Modalités de paiement</label>
                            <p class="text-gray-900 bg-gray-50 p-4 rounded-lg">
                                {{ $proforma->modalite_proforma ?? 'Non spécifiées' }}
                            </p>
                        </div>

                        @if ($proforma->motif_modification_proforma)
                            <!-- Motif de modification -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">
                                    <i class="fas fa-edit text-gray-400 mr-1"></i>
                                    Motif de la dernière modification
                                </label>
                                <p class="text-gray-900 bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                                    {{ $proforma->motif_modification_proforma }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Attributions liées -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div
                        class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-link text-indigo-500 mr-2"></i>
                            Attributions liées
                        </h2>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-700">
                            {{ $proforma->prestataireLotsAttributions->count() }} attribution(s)
                        </span>
                    </div>

                    <div class="p-6">
                        @if ($proforma->prestataireLotsAttributions->count() > 0)
                            <div class="space-y-3">
                                @foreach ($proforma->prestataireLotsAttributions as $attribution)
                                    <div
                                        class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-4">
                                            <div
                                                class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-building text-indigo-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">
                                                    {{ $attribution->prestataire->raison_sociale_prestataire ?? 'Prestataire inconnu' }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    Lot:
                                                    {{ $attribution->lot->libelle ?? ($attribution->lot->numero ?? 'Lot inconnu') }}
                                                </p>
                                            </div>
                                        </div>
                                        {{-- <div class="text-right">
                                            @php
                                                $statutClass = match($attribution->statut_attribution) {
                                                    \App\Models\PrestataireLot::STATUT_ATTRIBUE => 'bg-blue-100 text-blue-800',
                                                    \App\Models\PrestataireLot::STATUT_TERMINE => 'bg-green-100 text-green-800',
                                                    \App\Models\PrestataireLot::STATUT_SUSPENDU => 'bg-yellow-100 text-yellow-800',
                                                    \App\Models\PrestataireLot::STATUT_RETIRE => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                                $statutLabel = \App\Models\PrestataireLot::STATUT_LABELS[$attribution->statut_attribution] ?? 'Inconnu';
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statutClass }}">
                                                {{ $statutLabel }}
                                            </span>
                                        </div> --}}


                                        <div class="text-right">
                                            @php
                                                $statutClass = match ($attribution->statut_attribution) {
                                                    \App\Models\PrestataireLot::STATUT_ATTRIBUE
                                                        => 'bg-blue-100 text-blue-800',
                                                    \App\Models\PrestataireLot::STATUT_TERMINE
                                                        => 'bg-green-100 text-green-800',
                                                    \App\Models\PrestataireLot::STATUT_SUSPENDU
                                                        => 'bg-yellow-100 text-yellow-800',
                                                    \App\Models\PrestataireLot::STATUT_RETIRE
                                                        => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp

                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statutClass }}">
                                                {{ $attribution->getLibelleStatut() }}
                                            </span>

                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $attribution->created_at->format('d/m/Y') }}
                                            </p>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-unlink text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Aucune attribution liée à cette proforma</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">

                <!-- Historique des versions -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-history text-purple-500 mr-2"></i>
                            Historique des versions
                        </h2>
                    </div>

                    <div class="p-4">
                        @if ($historique && count($historique) > 0)
                            <div class="space-y-3">
                                @foreach ($historique as $version)
                                    <div
                                        class="flex items-start p-3 rounded-lg {{ $version->id_proforma === $proforma->id_proforma ? 'bg-purple-50 border border-purple-200' : 'bg-gray-50' }}">
                                        <div
                                            class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                            <span
                                                class="text-sm font-bold text-purple-600">v{{ $version->version_proforma }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $version->numero_proforma }}
                                                </p>
                                                @if ($version->actif_proforma)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                                        Active
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $version->created_at->format('d/m/Y H:i') }}
                                            </p>
                                            @if ($version->motif_modification_proforma && $version->id_proforma !== $proforma->id_proforma)
                                                <p class="text-xs text-gray-600 mt-1 truncate"
                                                    title="{{ $version->motif_modification_proforma }}">
                                                    {{ Str::limit($version->motif_modification_proforma, 50) }}
                                                </p>
                                            @endif
                                            @if ($version->id_proforma !== $proforma->id_proforma)
                                                <a href="{{ route('proformas.show', $version->id_proforma) }}"
                                                    class="text-xs text-purple-600 hover:text-purple-800 mt-1 inline-block">
                                                    Voir cette version →
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <i class="fas fa-file text-gray-300 text-3xl mb-2"></i>
                                <p class="text-sm text-gray-500">Version originale</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations système -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-cog text-gray-500 mr-2"></i>
                            Informations système
                        </h2>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Date de création -->
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Créée le</span>
                            <span
                                class="text-sm font-medium text-gray-900">{{ $proforma->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <!-- Créateur -->
                        @if ($proforma->creator)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-500">Créée par</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $proforma->creator->nom_complet ?? 'N/A' }}</span>
                            </div>
                        @endif

                        <!-- Date de modification -->
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Modifiée le</span>
                            <span
                                class="text-sm font-medium text-gray-900">{{ $proforma->updated_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <!-- Modificateur -->
                        @if ($proforma->updater)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-500">Modifiée par</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $proforma->updater->nom_complet ?? 'N/A' }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-bolt text-blue-500 mr-2"></i>
                            Actions rapides
                        </h2>
                    </div>

                    <div class="p-4 space-y-2">
                        <button onclick="creerVersion()"
                            class="w-full flex items-center justify-between p-3 text-gray-700 hover:bg-indigo-50 rounded-lg transition-colors group">
                            <span class="flex items-center">
                                <i class="fas fa-code-branch text-indigo-500 mr-3"></i>
                                Créer une version
                            </span>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-500"></i>
                        </button>

                        <button onclick="duplicate()"
                            class="w-full flex items-center justify-between p-3 text-gray-700 hover:bg-purple-50 rounded-lg transition-colors group">
                            <span class="flex items-center">
                                <i class="fas fa-copy text-purple-500 mr-3"></i>
                                Dupliquer
                            </span>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-500"></i>
                        </button>

                        <button onclick="printProforma()"
                            class="w-full flex items-center justify-between p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors group">
                            <span class="flex items-center">
                                <i class="fas fa-print text-gray-500 mr-3"></i>
                                Imprimer
                            </span>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600"></i>
                        </button>

                        <button onclick="window.location.href='{{ route('proformas.edit', $proforma->id_proforma) }}'"
                            class="w-full flex items-center justify-between p-3 text-gray-700 hover:bg-orange-50 rounded-lg transition-colors group">
                            <span class="flex items-center">
                                <i class="fas fa-edit text-orange-500 mr-3"></i>
                                Modifier
                            </span>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-500"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Suppression -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Confirmer la suppression</h3>
                    @if ($proforma->estUtilisee())
                        <p class="text-sm text-red-600 mb-6">
                            <strong>Impossible de supprimer cette proforma car elle est utilisée dans des
                                attributions.</strong>
                        </p>
                        <button onclick="closeDeleteModal()"
                            class="px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-200 font-medium">
                            Fermer
                        </button>
                    @else
                        <p class="text-sm text-gray-600 mb-6">
                            Êtes-vous sûr de vouloir supprimer la proforma
                            <strong>{{ $proforma->numero_proforma }}</strong> ?
                        </p>
                        <div class="flex items-center justify-center space-x-3">
                            <button onclick="closeDeleteModal()"
                                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                                Annuler
                            </button>
                            <button onclick="executeDelete()"
                                class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium">
                                Supprimer
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nouvelle Version -->
    <div id="versionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-code-branch text-indigo-500 mr-2"></i>
                        Créer une nouvelle version
                    </h3>
                    <button onclick="closeVersionModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="versionForm" class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Motif de modification <span class="text-red-500">*</span>
                            </label>
                            <textarea id="motif_modification" rows="3" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent resize-none"
                                placeholder="Expliquez la raison de cette nouvelle version..."></textarea>
                        </div>

                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-yellow-500 mr-2 mt-0.5"></i>
                                <p class="text-sm text-yellow-700">
                                    La version actuelle sera désactivée et une nouvelle version sera créée.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeVersionModal()"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                            Créer la version
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Toggle menu
            window.toggleMenu = function() {
                document.getElementById('actionMenu').classList.toggle('hidden');
            }

            // Fermer menu en cliquant ailleurs
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#menuBtn') && !e.target.closest('#actionMenu')) {
                    document.getElementById('actionMenu').classList.add('hidden');
                }
            });

            // Toggle statut
            window.toggleStatus = function(isActive) {
                const action = isActive ? 'désactiver' : 'activer';
                if (confirm(`Voulez-vous vraiment ${action} cette proforma ?`)) {
                    fetch(`/proformas/{{ $proforma->id_proforma }}/toggle-status`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message || 'Une erreur est survenue');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue');
                        });
                }
            }

            // Confirmer suppression
            window.confirmDelete = function() {
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            // Fermer modal suppression
            window.closeDeleteModal = function() {
                document.getElementById('deleteModal').classList.add('hidden');
            }

            // Exécuter suppression
            window.executeDelete = function() {
                fetch(`/proformas/{{ $proforma->id_proforma }}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '{{ route('proformas.index') }}';
                        } else {
                            alert(data.message || 'Une erreur est survenue');
                            closeDeleteModal();
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue');
                        closeDeleteModal();
                    });
            }

            // Dupliquer
            window.duplicate = function() {
                if (confirm('Voulez-vous dupliquer cette proforma ?')) {
                    fetch(`/proformas/{{ $proforma->id_proforma }}/duplicate`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = `/proformas/${data.data.id_proforma}/edit`;
                            } else {
                                alert(data.message || 'Une erreur est survenue');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue');
                        });
                }
            }

            // Créer version
            window.creerVersion = function() {
                document.getElementById('motif_modification').value = '';
                document.getElementById('versionModal').classList.remove('hidden');
            }

            // Fermer modal version
            window.closeVersionModal = function() {
                document.getElementById('versionModal').classList.add('hidden');
            }

            // Soumettre nouvelle version
            document.getElementById('versionForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const motif = document.getElementById('motif_modification').value;

                if (!motif.trim()) {
                    alert('Le motif de modification est obligatoire');
                    return;
                }

                fetch(`/proformas/{{ $proforma->id_proforma }}/creer-version`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            motif_modification_proforma: motif
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = `/proformas/${data.data.id_proforma}`;
                        } else {
                            alert(data.message || 'Une erreur est survenue');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue');
                    });
            });

            // Imprimer
            window.printProforma = function() {
                window.print();
            }

            // Fermer modals avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeDeleteModal();
                    closeVersionModal();
                    document.getElementById('actionMenu').classList.add('hidden');
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

            @media print {
                .no-print {
                    display: none !important;
                }
            }
        </style>
    @endpush
@endsection
