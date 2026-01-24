@extends('layouts.main')

@section('title', 'Évaluation ' . $evaluation->numero_evaluation)

@section('breadcrumb')



    <a @can('attributions_lots.read') href="{{ route('attributions.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Attributions</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white/80 hover:text-white transition-colors">{{ $evaluation->attribution->numero_attribution }}</span>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>

    <a @can('evaluations_attributions.read') href="{{ route('evaluations.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Évaluations</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">{{ $evaluation->numero_evaluation }}</span>
@endsection




@section('content')
    <!-- Header avec actions -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    @can("evaluations_attributions.evaluate")
                        <a href="{{ route('evaluations.pour-attribution', $evaluation->attribution_id) }}"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan
                    <div>
                        <div class="flex items-center space-x-3 flex-wrap gap-2">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $evaluation->numero_evaluation }}</h1>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $evaluation->statut_badge_class }}">
                                <i class="fas fa-{{ $evaluation->statut_icon }} mr-1"></i>
                                {{ $evaluation->statut_label }}
                            </span>
                        </div>
                        <p class="text-gray-600 mt-1">
                            Critère: {{ $evaluation->numero_critere }} - {{ Str::limit($evaluation->libelle_critere, 50) }}
                        </p>
                    </div>
                </div>

                @canany(['evaluations_attributions.evaluate', 'evaluations_attributions.validate', 'evaluations_attributions.reject'])
                    <!-- Actions -->
                    <div class="flex items-center space-x-2 flex-wrap gap-2">
                        @can('evaluations_attributions.evaluate')
                            @if($evaluation->peutEtreModifiee())
                                <a href="{{ route('evaluations.edit', $evaluation->id_evaluation) }}"
                                    class="px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md">
                                    <i class="fas fa-edit text-sm"></i>
                                    <span class="text-sm font-medium">Modifier</span>
                                </a>
                            @endif
                        @endcan

                        @can('evaluations_attributions.validate')
                            @if($evaluation->peutEtreTerminee())

                                <form action="{{ route('evaluations.terminer', $evaluation->id_evaluation) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                        <i class="fas fa-check text-sm"></i>
                                        <span class="text-sm font-medium">Terminer</span>
                                    </button>
                                </form>
                            @elseif($evaluation->etat_appel_offre == 1 && !empty($raisonsNonTerminable))
                                <button type="button" onclick="openRaisonsModal()"
                                    class="px-4 py-2.5 bg-gray-300 text-gray-600 rounded-lg flex items-center space-x-2"
                                    title="Conditions non remplies">
                                    <i class="fas fa-check text-sm"></i>
                                    <span class="text-sm font-medium">Terminer</span>
                                    <i class="fas fa-exclamation-circle text-yellow-600 ml-1"></i>
                                </button>
                            @endif
                        @endcan

                        @canany(['evaluations_attributions.validate', 'evaluations_attributions.reject'])
                            @if($evaluation->peutEtreValidee())
                                @can('evaluations_attributions.validate')
                                <button onclick="openValiderModal()"
                                    class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-check-double text-sm"></i>
                                    <span class="text-sm font-medium">Valider</span>
                                </button>
                                @endcan

                                @can('evaluations_attributions.reject')
                                <button onclick="openRejeterModal()"
                                    class="px-4 py-2.5 bg-white border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-times text-sm"></i>
                                    <span class="text-sm font-medium">Rejeter</span>
                                </button>
                                @endcan
                            @endif
                        @endcanany

                        @can('evaluations_attributions.evaluate')
                            @if($evaluation->isRejetee())
                                <form action="{{ route('evaluations.reprendre', $evaluation->id_evaluation) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                        <i class="fas fa-redo text-sm"></i>
                                        <span class="text-sm font-medium">Reprendre</span>
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
                @endcanany
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        @include('partials.alerts')

        <!-- Alerte si conditions non remplies -->
        @if($evaluation->etat_appel_offre == 1 && !empty($raisonsNonTerminable))
            <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-sm">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-yellow-800 font-semibold mb-2">Conditions non remplies pour terminer</h3>
                        <ul class="list-disc list-inside text-yellow-700 text-sm space-y-1">
                            @foreach($raisonsNonTerminable as $raison)
                                <li>{{ $raison }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Critère et progression -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-list-check text-indigo-500 mr-2"></i>
                            Critère d'évaluation
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <span class="px-3 py-1 text-sm font-bold bg-indigo-100 text-indigo-700 rounded-lg">
                                    {{ $evaluation->critereEvaluation->numero_critere_evaluation }}
                                </span>
                                <h3 class="mt-2 text-xl font-semibold text-gray-900">
                                    {{ $evaluation->critereEvaluation->libelle_critere_evaluation }}
                                </h3>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Note de référence</p>
                                <p class="text-3xl font-bold text-indigo-600">{{ number_format($noteReferenceCritere, 2) }}</p>
                            </div>
                        </div>

                        <!-- Progression -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700">Progression totale</span>
                                <span class="text-lg font-bold {{ $totalEvalueCritere >= $noteReferenceCritere ? 'text-green-600' : 'text-orange-600' }}">
                                    {{ number_format($totalEvalueCritere, 2) }} / {{ number_format($noteReferenceCritere, 2) }} pts
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                                @php $pct = $noteReferenceCritere > 0 ? min(($totalEvalueCritere / $noteReferenceCritere * 100), 100) : 0; @endphp
                                <div class="h-4 rounded-full transition-all {{ $pct >= 100 ? 'bg-green-500' : 'bg-orange-500' }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">{{ number_format($pct, 1) }}% complété</span>
                                @if($resteAEvaluer > 0)
                                    <span class="text-orange-600 font-medium">Reste: {{ number_format($resteAEvaluer, 2) }} pts</span>
                                @else
                                    <span class="text-green-600 font-medium"><i class="fas fa-check-circle mr-1"></i>Complet</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Résultat de cette évaluation -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-star text-green-500 mr-2"></i>
                            Résultat de cette évaluation
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-around">
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Note attribuée</p>
                                <p class="text-4xl font-bold {{ $evaluation->pourcentage_final >= 70 ? 'text-green-600' : ($evaluation->pourcentage_final >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($evaluation->resultat_evaluation, 2) }}
                                </p>
                                <p class="text-sm text-gray-400">points</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Pourcentage</p>
                                <p class="text-4xl font-bold {{ $evaluation->pourcentage_final >= 70 ? 'text-green-600' : ($evaluation->pourcentage_final >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($evaluation->pourcentage_final, 1) }}%
                                </p>
                                <p class="text-sm text-gray-400">du critère</p>
                            </div>
                        </div>
                    </div>
                </div>

                  <!-- Commentaires -->
                @if($evaluation->commentaire_general)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-comment-alt text-gray-500 mr-2"></i>
                                Commentaire
                            </h2>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $evaluation->commentaire_general }}</p>
                        </div>
                    </div>
                @endif

                <!-- Autres évaluations du même critère -->
                @if($autresEvaluationsCritere->count() > 0)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-layer-group text-purple-500 mr-2"></i>
                                Autres évaluations pour ce critère
                                <span class="ml-2 px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">
                                    {{ $autresEvaluationsCritere->count() }}
                                </span>
                            </h2>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @foreach($autresEvaluationsCritere as $autreEval)
                                <div class="p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <a @can('evaluations_attributions.view-details') href="{{ route('evaluations.show', $autreEval->id_evaluation) }}" @endcan
                                                class="font-semibold text-indigo-600 hover:text-indigo-800">
                                                {{ $autreEval->numero_evaluation }}
                                            </a>
                                            <p class="text-sm text-gray-500">
                                                {{ $autreEval->date_evaluation ? $autreEval->date_evaluation->format('d/m/Y H:i') : '-' }}
                                            </p>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $autreEval->statut_badge_class }}">
                                                {{ $autreEval->statut_label }}
                                            </span>
                                            <span class="text-lg font-bold text-gray-700">
                                                {{ number_format($autreEval->resultat_evaluation, 2) }} pts
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif



                <!-- Motif de rejet -->
                @if($evaluation->isRejetee() && $evaluation->motif_rejet)
                    <div class="bg-red-50 rounded-2xl shadow-lg overflow-hidden border border-red-200">
                        <div class="px-6 py-4 bg-gradient-to-r from-red-100 to-red-50 border-b border-red-200">
                            <h2 class="text-lg font-bold text-red-800 flex items-center">
                                <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                Motif de rejet
                            </h2>
                        </div>
                        <div class="p-6">
                            <p class="text-red-700">{{ $evaluation->motif_rejet }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">

                <!-- Responsables -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-users text-blue-500 mr-2"></i>
                            Responsables
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Responsable technique -->
                        <div class="bg-gray-50 p-4 rounded-lg {{ !$evaluation->hasRespoTechnique() ? 'border-2 border-dashed border-red-300' : '' }}">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-user-cog text-blue-500 mr-2"></i>
                                <span class="text-sm font-semibold text-gray-700">Responsable technique</span>
                            </div>
                            @if($evaluation->hasRespoTechnique())
                                <p class="font-medium text-gray-900">{{ $evaluation->respo_technique_evaluation['nom_complet'] ?? '' }}</p>
                            @else
                                <p class="text-red-500 text-sm">Non renseigné</p>
                            @endif
                        </div>

                        <!-- Superviseur -->
                        <div class="bg-gray-50 p-4 rounded-lg {{ !$evaluation->hasSuperviseur() ? 'border-2 border-dashed border-red-300' : '' }}">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-user-shield text-purple-500 mr-2"></i>
                                <span class="text-sm font-semibold text-gray-700">Superviseur</span>
                            </div>
                            @if($evaluation->hasSuperviseur())
                                <p class="font-medium text-gray-900">{{ $evaluation->superviseur_evaluation['nom_complet'] ?? '' }}</p>
                            @else
                                <p class="text-red-500 text-sm">Non renseigné</p>
                            @endif
                        </div>

                        <!-- Évaluateur -->
                        <div class="bg-gray-50 p-4 rounded-lg {{ !$evaluation->hasEvaluePar() ? 'border-2 border-dashed border-red-300' : '' }}">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-user-check text-green-500 mr-2"></i>
                                <span class="text-sm font-semibold text-gray-700">Évaluateur</span>
                            </div>
                            @if($evaluation->hasEvaluePar())
                                <p class="font-medium text-gray-900">{{ $evaluation->evalue_par['nom_complet'] ?? '' }}</p>
                            @else
                                <p class="text-red-500 text-sm">Non renseigné</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Attribution -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-contract text-orange-500 mr-2"></i>
                            Attribution
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Lot</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700">
                                {{ $evaluation->attribution->lot->numero ?? 'N/A' }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Prestataire</label>
                            <p class="text-gray-900 font-medium">{{ $evaluation->attribution->prestataire->raison_sociale_prestataire ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Audit -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-fingerprint text-gray-500 mr-2"></i>
                            Audit
                        </h2>
                    </div>
                    <div class="p-6 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Créé le</span>
                            <span class="text-gray-900">{{ $evaluation->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($evaluation->date_evaluation)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Date d'évaluation</span>
                                <span class="text-gray-900">{{ $evaluation->date_evaluation->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                        @if($evaluation->date_validation)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Validé le</span>
                                <span class="text-gray-900">{{ $evaluation->date_validation->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Valider -->
    <div id="validerModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeValiderModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-check-double text-emerald-500 mr-2"></i>Valider l'évaluation
                    </h3>
                </div>
                <form @can('evaluations_attributions.validate') action="{{ route('evaluations.valider', $evaluation->id_evaluation) }}" @endcan method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="bg-emerald-50 p-4 rounded-lg text-sm text-emerald-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            Cette action validera définitivement l'évaluation.
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Motif (optionnel)</label>
                            <textarea name="motif_validation" rows="3"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-emerald-400"></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeValiderModal()" class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">Annuler</button>
                        @can('evaluations_attributions.validate')
                        <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg">Valider</button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Rejeter -->
    <div id="rejeterModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeRejeterModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-times-circle text-red-500 mr-2"></i>Rejeter l'évaluation
                    </h3>
                </div>
                <form @can('evaluations_attributions.reject') action="{{ route('evaluations.rejeter', $evaluation->id_evaluation) }}" @endcan method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Motif du rejet *</label>
                            <textarea name="motif_rejet" rows="3" required minlength="10"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-red-400"></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeRejeterModal()" class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">Annuler</button>
                        @can('evaluations_attributions.reject')
                        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">Rejeter</button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Raisons -->
    <div id="raisonsModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeRaisonsModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-white border-b">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>Conditions non remplies
                    </h3>
                </div>
                <div class="p-6">
                    <ul class="list-disc list-inside text-gray-600 space-y-2">
                        @foreach($raisonsNonTerminable ?? [] as $raison)
                            <li>{{ $raison }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end">
                    <button type="button" onclick="closeRaisonsModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Compris</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openValiderModal() { document.getElementById('validerModal').classList.remove('hidden'); }
    function closeValiderModal() { document.getElementById('validerModal').classList.add('hidden'); }
    function openRejeterModal() { document.getElementById('rejeterModal').classList.remove('hidden'); }
    function closeRejeterModal() { document.getElementById('rejeterModal').classList.add('hidden'); }
    function openRaisonsModal() { document.getElementById('raisonsModal').classList.remove('hidden'); }
    function closeRaisonsModal() { document.getElementById('raisonsModal').classList.add('hidden'); }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeValiderModal();
            closeRejeterModal();
            closeRaisonsModal();
        }
    });
</script>
@endpush
