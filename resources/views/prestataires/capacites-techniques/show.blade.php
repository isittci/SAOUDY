@extends('layouts.main')

@section('title', 'Détails Capacité Technique - ' . $prestataire->raison_sociale_prestataire)

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
    }
    .badge-certification {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 1rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
        margin: 0.25rem;
    }
    .badge-agrement {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 1rem;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
        margin: 0.25rem;
    }
    .badge-expertise {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 1rem;
        background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
        color: white;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
        margin: 0.25rem;
    }
    .score-circle-large {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .score-excellent { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .score-bon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .score-moyen { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }
    .score-faible { background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .info-row:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('breadcrumb')
    <a href="{{ route('prestataires.index') }}" class="text-white/80 hover:text-white transition-colors">Prestataires</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('prestataires.show', $prestataire->id_prestataire) }}" class="text-white/80 hover:text-white transition-colors">{{ Str::limit($prestataire->raison_sociale_prestataire, 20) }}</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a href="{{ route('prestataires.capacites-techniques.index', $prestataire->id_prestataire) }}" class="text-white/80 hover:text-white transition-colors">Capacités Techniques</a>
<i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Détails</span>
@endsection

@section('content')
    @php
        $niveau = $capacite->getNiveau();
        $score = $capacite->calculerScore();
        $scoreClass = match(true) {
            $score >= 80 => 'score-excellent',
            $score >= 60 => 'score-bon',
            $score >= 40 => 'score-moyen',
            default => 'score-faible',
        };
    @endphp

    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    @can('capacites_techniques.read')
                    <a href="{{ route('prestataires.capacites-techniques.index', $prestataire->id_prestataire) }}"
                       class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    @endcan
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-cogs text-orange-500 mr-2"></i>
                            Détails de la Capacité Technique
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">{{ $prestataire->raison_sociale_prestataire }}</p>
                    </div>
                </div>
                @can('capacites_techniques.manage')
                <div class="flex items-center gap-2">
                    <a href="{{ route('prestataires.capacites-techniques.edit', [$prestataire->id_prestataire, $capacite->id_capacite_technique]) }}"
                       class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </a>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne gauche - Score et Résumé -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Score -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-{{ $niveau['classe'] }}-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-chart-pie text-{{ $niveau['classe'] }}-500 mr-2"></i>
                            Score Global
                        </h2>
                    </div>
                    <div class="p-6 text-center">
                        <div class="score-circle-large {{ $scoreClass }} mx-auto mb-4">
                            <span class="text-3xl font-bold">{{ $score }}</span>
                            <span class="text-sm opacity-80">/100</span>
                        </div>
                        <p class="text-lg font-semibold text-{{ $niveau['classe'] }}-600 mb-2">
                            <i class="fas fa-{{ $niveau['icon'] }} mr-1"></i>
                            {{ $niveau['niveau'] }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Évaluation basée sur les critères renseignés
                        </p>
                    </div>
                </div>

                <!-- Effectifs -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-users text-blue-500 mr-2"></i>
                            Effectifs
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <p class="text-3xl font-bold text-blue-600">{{ $capacite->effectif_permanent_capacite_technique ?? 0 }}</p>
                                <p class="text-xs text-gray-500">Permanents</p>
                            </div>
                            <div>
                                <p class="text-3xl font-bold text-orange-600">{{ $capacite->effectif_temporaire_capacite_technique ?? 0 }}</p>
                                <p class="text-xs text-gray-500">Temporaires</p>
                            </div>
                            <div>
                                <p class="text-3xl font-bold text-green-600">{{ $capacite->effectif_total }}</p>
                                <p class="text-xs text-gray-500">Total</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-gray-500 mr-2"></i>
                            Informations
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="info-row">
                            <span class="text-gray-500 text-sm">Références</span>
                            <span class="font-medium text-gray-800">{{ $capacite->references_capacite_technique ?: 'Non renseigné' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="text-gray-500 text-sm">Compétences clés</span>
                            <span class="font-medium text-gray-800">{{ $capacite->competences_cles_capacite_technique ?: 'Non renseigné' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="text-gray-500 text-sm">Créé le</span>
                            <span class="font-medium text-gray-800">{{ $capacite->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="text-gray-500 text-sm">Modifié le</span>
                            <span class="font-medium text-gray-800">{{ $capacite->updated_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne droite - Détails -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Certifications -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-certificate text-purple-500 mr-2"></i>
                            Certifications
                            @if($capacite->hasCertifications())
                                <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">
                                    {{ count($capacite->certifications_array) }}
                                </span>
                            @endif
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($capacite->hasCertifications())
                            <div class="flex flex-wrap">
                                @foreach($capacite->certifications_array as $certification)
                                    <span class="badge-certification">
                                        <i class="fas fa-check-circle mr-1.5"></i>{{ $certification }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-certificate text-4xl mb-2"></i>
                                <p>Aucune certification renseignée</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Agréments -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-award text-green-500 mr-2"></i>
                            Agréments
                            @if($capacite->hasAgrements())
                                <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                    {{ count($capacite->agrements_array) }}
                                </span>
                            @endif
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($capacite->hasAgrements())
                            <div class="flex flex-wrap">
                                @foreach($capacite->agrements_array as $agrement)
                                    <span class="badge-agrement">
                                        <i class="fas fa-check-circle mr-1.5"></i>{{ $agrement }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-award text-4xl mb-2"></i>
                                <p>Aucun agrément renseigné</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Domaines d'expertise -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-briefcase text-orange-500 mr-2"></i>
                            Domaines d'Expertise
                            @if(count($capacite->domaines_expertise_array) > 0)
                                <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">
                                    {{ count($capacite->domaines_expertise_array) }}
                                </span>
                            @endif
                        </h2>
                    </div>
                    <div class="p-6">
                        @if(count($capacite->domaines_expertise_array) > 0)
                            <div class="flex flex-wrap">
                                @foreach($capacite->domaines_expertise_array as $domaine)
                                    <span class="badge-expertise">
                                        <i class="fas fa-tag mr-1.5"></i>{{ $domaine }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-briefcase text-4xl mb-2"></i>
                                <p>Aucun domaine d'expertise renseigné</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Moyens Matériels -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-tools text-gray-500 mr-2"></i>
                            Moyens Matériels
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($capacite->moyens_materiels_capacite_technique)
                            <div class="prose prose-sm max-w-none text-gray-700">
                                {!! nl2br(e($capacite->moyens_materiels_capacite_technique)) !!}
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-tools text-4xl mb-2"></i>
                                <p>Aucun moyen matériel renseigné</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </main>
@endsection
