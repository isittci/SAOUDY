@extends('layouts.main')
@section('title', 'Historique - ' . $proforma->numero_proforma)
@section('breadcrumb')
    <a @can('proformas.read') href="{{ route('proformas.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Proformas</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('proformas.view-details') href="{{ route('proformas.show', $proforma->id_proforma) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">{{ $proforma->numero_proforma }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Historique</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et retour -->
                <div class="flex items-center space-x-4">
                    @can('proformas.view-details')
                        <a href="{{ route('proformas.show', $proforma->id_proforma) }}"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-history text-purple-500 mr-3"></i>
                            Historique des versions
                        </h1>
                        <p class="text-gray-600 mt-1 flex items-center flex-wrap gap-2">
                            <span>{{ $proforma->numero_proforma }}</span>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                {{ $historique->count() }} version(s)
                            </span>
                        </p>
                    </div>
                </div>

                @can('proformas.view-details')
                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('proformas.show', $proforma->id_proforma) }}"
                            class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                            <i class="fas fa-eye text-sm"></i>
                            <span class="text-sm font-medium">Voir la proforma</span>
                        </a>
                    </div>
                @endcan
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
            <!-- Colonne principale - Timeline -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-code-branch text-purple-500 mr-2"></i>
                            Timeline des versions
                        </h2>
                    </div>

                    <div class="p-6">
                        @if ($historique->count() > 0)
                            <div class="relative">
                                <!-- Ligne verticale -->
                                <div
                                    class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-purple-500 via-purple-300 to-gray-200">
                                </div>

                                <div class="space-y-6">
                                    @foreach ($historique as $index => $version)
                                        <div class="relative pl-16">
                                            <!-- Point sur la timeline -->
                                            <div
                                                class="absolute left-4 w-5 h-5 rounded-full border-4
                                                {{ $version->actif_proforma ? 'bg-green-500 border-green-200' : 'bg-gray-400 border-gray-200' }}
                                                {{ $index === 0 ? 'ring-4 ring-purple-100' : '' }}">
                                            </div>

                                            <!-- Carte version -->
                                            <div
                                                class="bg-gradient-to-br {{ $version->actif_proforma ? 'from-green-50 to-white border-green-200' : 'from-gray-50 to-white border-gray-200' }}
                                                border rounded-xl p-5 hover:shadow-md transition-all duration-200
                                                {{ $index === 0 ? 'ring-2 ring-purple-200' : '' }}">

                                                <!-- En-tête de la version -->
                                                <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                                                    <div class="flex items-center space-x-2">
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold
                                                            {{ $version->actif_proforma ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                                            <i class="fas fa-code-branch mr-1.5"></i>
                                                            v{{ $version->version_proforma }}
                                                        </span>
                                                        @if ($version->actif_proforma)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-500 text-white">
                                                                <i class="fas fa-check mr-1"></i> Active
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-400 text-white">
                                                                <i class="fas fa-archive mr-1"></i> Archivée
                                                            </span>
                                                        @endif
                                                        @if ($index === 0)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-500 text-white">
                                                                <i class="fas fa-star mr-1"></i> Dernière
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @can('proformas.view-details')
                                                        <a href="{{ route('proformas.show', $version->id_proforma) }}"
                                                            class="text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center">
                                                            <span>Voir détails</span>
                                                            <i class="fas fa-arrow-right ml-1"></i>
                                                        </a>
                                                    @endcan
                                                </div>

                                                <!-- Informations financières -->
                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                                                    <div class="bg-white/70 p-3 rounded-lg">
                                                        <p class="text-xs text-gray-500 mb-1">Montant HT</p>
                                                        <p class="font-bold text-gray-800">
                                                            {{ number_format($version->montant_retenu_proforma, 0, ',', ' ') }}
                                                        </p>
                                                        <p class="text-xs text-gray-400">FCFA</p>
                                                    </div>
                                                    <div class="bg-white/70 p-3 rounded-lg">
                                                        <p class="text-xs text-gray-500 mb-1">Remise</p>
                                                        <p class="font-bold text-red-600">
                                                            -{{ number_format($version->remise_montant_proforma, 0, ',', ' ') }}
                                                        </p>
                                                        <p class="text-xs text-gray-400">FCFA</p>
                                                    </div>
                                                    <div class="bg-white/70 p-3 rounded-lg">
                                                        <p class="text-xs text-gray-500 mb-1">TVA</p>
                                                        <p class="font-bold text-orange-600">
                                                            +{{ number_format($version->taxe_montant, 0, ',', ' ') }}</p>
                                                        <p class="text-xs text-gray-400">FCFA</p>
                                                    </div>
                                                    <div class="bg-white/70 p-3 rounded-lg">
                                                        <p class="text-xs text-gray-500 mb-1">Total TTC</p>
                                                        <p class="font-bold text-green-600">
                                                            {{ number_format($version->calculerMontantTTC(), 0, ',', ' ') }}
                                                        </p>
                                                        <p class="text-xs text-gray-400">FCFA</p>
                                                    </div>
                                                </div>

                                                <!-- Motif de modification -->
                                                @if ($version->motif_modification_proforma)
                                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                                                        <div class="flex items-start">
                                                            <i class="fas fa-comment-alt text-yellow-500 mr-2 mt-0.5"></i>
                                                            <div>
                                                                <p class="text-xs font-semibold text-yellow-700 mb-1">Motif
                                                                    de modification</p>
                                                                <p class="text-sm text-yellow-800">
                                                                    {{ $version->motif_modification_proforma }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Métadonnées -->
                                                <div
                                                    class="flex flex-wrap items-center gap-4 text-xs text-gray-500 border-t border-gray-100 pt-3">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-calendar mr-1.5"></i>
                                                        <span>Date proforma:
                                                            {{ $version->date_proforma ? $version->date_proforma->format('d/m/Y') : 'N/A' }}</span>
                                                    </div>
                                                    @if ($version->creator)
                                                        <div class="flex items-center">
                                                            <i class="fas fa-user mr-1.5"></i>
                                                            <span>Créé par: {{ $version->creator->name ?? 'N/A' }}</span>
                                                        </div>
                                                    @endif
                                                    <div class="flex items-center">
                                                        <i class="fas fa-clock mr-1.5"></i>
                                                        <span>{{ $version->created_at->format('d/m/Y à H:i') }}</span>
                                                    </div>
                                                    @if ($version->updater && $version->updated_at != $version->created_at)
                                                        <div class="flex items-center">
                                                            <i class="fas fa-edit mr-1.5"></i>
                                                            <span>Modifié par:
                                                                {{ $version->updater->name ?? 'N/A' }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div
                                    class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-history text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun historique</h3>
                                <p class="text-gray-500">Cette proforma n'a pas encore d'historique de versions.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">
                <!-- Résumé de la proforma actuelle -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-invoice text-blue-500 mr-2"></i>
                            Proforma actuelle
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Numéro</span>
                            <span class="font-semibold text-gray-900">{{ $proforma->numero_proforma }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Version</span>
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                v{{ $proforma->version_proforma }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Statut</span>
                            @if ($proforma->actif_proforma)
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Active
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    <i class="fas fa-times-circle mr-1"></i> Inactive
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Date</span>
                            <span class="font-medium text-gray-900">
                                {{ $proforma->date_proforma ? $proforma->date_proforma->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between py-4 bg-gradient-to-r from-green-500 to-green-600 -mx-6 px-6 -mb-6 rounded-b-lg">
                            <span class="text-white font-semibold">Total TTC</span>
                            <span
                                class="text-xl font-bold text-white">{{ number_format($proforma->calculerMontantTTC(), 0, ',', ' ') }}
                                FCFA</span>
                        </div>
                    </div>
                </div>

                <!-- Statistiques versions -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-chart-bar text-purple-500 mr-2"></i>
                            Statistiques
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-500">Total versions</span>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-purple-100 text-purple-800">
                                {{ $historique->count() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-500">Versions actives</span>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800">
                                {{ $historique->where('actif_proforma', true)->count() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-500">Versions archivées</span>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-gray-100 text-gray-600">
                                {{ $historique->where('actif_proforma', false)->count() }}
                            </span>
                        </div>
                        @if ($historique->count() > 1)
                            @php
                                $premiereVersion = $historique->last();
                                $derniereVersion = $historique->first();
                                $evolution =
                                    $derniereVersion->montant_retenu_proforma -
                                    $premiereVersion->montant_retenu_proforma;
                                $pourcentageEvolution =
                                    $premiereVersion->montant_retenu_proforma > 0
                                        ? round(($evolution / $premiereVersion->montant_retenu_proforma) * 100, 2)
                                        : 0;
                            @endphp
                            <div class="border-t border-gray-100 pt-4 mt-2">
                                <p class="text-xs text-gray-500 mb-2">Évolution du montant HT</p>
                                <div class="flex items-center space-x-2">
                                    @if ($evolution > 0)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded text-sm font-bold bg-green-100 text-green-700">
                                            <i class="fas fa-arrow-up mr-1"></i>
                                            +{{ number_format($evolution, 0, ',', ' ') }} FCFA
                                        </span>
                                        <span class="text-xs text-green-600">(+{{ $pourcentageEvolution }}%)</span>
                                    @elseif($evolution < 0)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded text-sm font-bold bg-red-100 text-red-700">
                                            <i class="fas fa-arrow-down mr-1"></i>
                                            {{ number_format($evolution, 0, ',', ' ') }} FCFA
                                        </span>
                                        <span class="text-xs text-red-600">({{ $pourcentageEvolution }}%)</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded text-sm font-bold bg-gray-100 text-gray-600">
                                            <i class="fas fa-equals mr-1"></i>
                                            Pas de changement
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @canany(['proformas.read', 'proformas.update', 'proformas.view-details'])
                    <!-- Actions rapides -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-bolt text-orange-500 mr-2"></i>
                                Actions rapides
                            </h2>
                        </div>
                        <div class="p-6 space-y-3">
                            @can('proformas.view-details')
                                <a href="{{ route('proformas.show', $proforma->id_proforma) }}"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
                                    <i class="fas fa-eye"></i>
                                    <span class="font-medium">Voir la proforma</span>
                                </a>
                            @endcan

                            @can('proformas.update')
                                <a href="{{ route('proformas.edit', $proforma->id_proforma) }}"
                                    class="w-full px-4 py-2.5 bg-white border border-orange-300 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
                                    <i class="fas fa-edit"></i>
                                    <span class="font-medium">Modifier</span>
                                </a>
                            @endcan

                            @can('proformas.read')
                                <a href="{{ route('proformas.index') }}"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
                                    <i class="fas fa-list"></i>
                                    <span class="font-medium">Liste des proformas</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                @endcanany
            </div>
        </div>
    </main>

    @push('scripts')
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
