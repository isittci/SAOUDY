@extends('layouts.main')
@section('title', 'Historique Prestataire')
@section('breadcrumb')
    <a href="{{ route('attributions.index') }}" class="text-white/80 hover:text-white transition-colors">Attributions</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Historique Prestataire</span>
@endsection

@section('content')
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('attributions.index') }}" class="p-2 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Historique des attributions</h1>
                    <p class="text-gray-600 mt-1">{{ $prestataire->raison_sociale_prestataire }}</p>
                </div>
            </div>
        </div>
    </div>

    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-gray-400">
                <p class="text-xs text-gray-500 uppercase font-semibold">Total</p>
                <p class="text-2xl font-bold text-gray-800">{{ $statistiques['total'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-green-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">En cours</p>
                <p class="text-2xl font-bold text-green-600">{{ $statistiques['en_cours'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Terminées</p>
                <p class="text-2xl font-bold text-blue-600">{{ $statistiques['terminees'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-yellow-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Suspendues</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $statistiques['suspendues'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-red-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Retirées</p>
                <p class="text-2xl font-bold text-red-600">{{ $statistiques['retirees'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-purple-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Montant engagé</p>
                <p class="text-lg font-bold text-purple-600">{{ number_format($statistiques['montant_total_engage'], 0, ',', ' ') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Liste attributions -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-list text-green-500 mr-2"></i>
                            Toutes les attributions
                            <span class="ml-2 px-2.5 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                {{ $historique->count() }}
                            </span>
                        </h2>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse($historique as $attr)
                            <a href="{{ route('attributions.show', $attr->id_attribution) }}"
                               class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <span class="font-semibold text-gray-800">{{ $attr->numero_attribution }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $attr->statut_badge_class }}">
                                            {{ $attr->statut_label }}
                                        </span>
                                        @if(!$attr->is_active)
                                            <span class="text-xs text-gray-400"><i class="fas fa-archive"></i></span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 text-xs font-medium mr-2">
                                            {{ $attr->lot->numero ?? '' }}
                                        </span>
                                        {{ Str::limit($attr->lot->libelle ?? '', 40) }}
                                    </p>
                                    <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                        <span><i class="fas fa-calendar mr-1"></i>{{ $attr->date_attribution ? $attr->date_attribution->format('d/m/Y') : '-' }}</span>
                                        <span><i class="fas fa-chart-line mr-1"></i>{{ number_format($attr->pourcentage_avancement, 0) }}%</span>
                                        @if($attr->estEnRetard())
                                            <span class="text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $attr->jours_retard_actuels }}j retard</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right ml-4">
                                    <p class="text-sm font-semibold text-gray-800">{{ number_format($attr->montant_engage, 0, ',', ' ') }}</p>
                                    <p class="text-xs text-gray-500">FCFA</p>
                                </div>
                            </a>
                        @empty
                            <div class="p-12 text-center">
                                <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                                <p class="text-gray-500">Aucune attribution pour ce prestataire</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Infos prestataire -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-building text-blue-500 mr-2"></i>
                            Prestataire
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Raison sociale</label>
                            <p class="text-gray-800 font-medium">{{ $prestataire->raison_sociale_prestataire }}</p>
                        </div>
                        @if($prestataire->ville_prestataire)
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Ville</label>
                                <p class="text-gray-700">{{ $prestataire->ville_prestataire }}</p>
                            </div>
                        @endif
                        @if($prestataire->telephone_prestataire)
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Téléphone</label>
                                <p class="text-gray-700">{{ $prestataire->telephone_prestataire }}</p>
                            </div>
                        @endif
                        @if($prestataire->email_prestataire)
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Email</label>
                                <p class="text-gray-700">{{ $prestataire->email_prestataire }}</p>
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Statut</label>
                            @if($prestataire->statut_prestataire)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Actif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <i class="fas fa-times-circle mr-1"></i>Inactif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Résumé financier -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
                    <h3 class="font-bold text-green-100 mb-4">Résumé financier</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-green-100">Montant engagé</span>
                            <span class="font-bold">{{ number_format($statistiques['montant_total_engage'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-100">Montant payé</span>
                            <span class="font-bold">{{ number_format($statistiques['montant_total_paye'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        <hr class="border-green-400">
                        <div class="flex justify-between">
                            <span class="text-green-100">Pénalités</span>
                            <span class="font-bold text-yellow-200">{{ number_format($statistiques['penalites_totales'], 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
