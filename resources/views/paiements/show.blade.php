@extends('layouts.main')
@section('title', 'Détails Paiement - ' . $paiement->reference_paiement)

@push('styles')
    <style>
        .info-card {
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .detail-value {
            color: #1f2937;
            font-weight: 500;
            text-align: right;
        }

        .timeline-step {
            position: relative;
        }

        .timeline-step:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 1.25rem;
            top: 2.5rem;
            width: 2px;
            height: calc(100% - 1rem);
            background: #e5e7eb;
        }

        .timeline-step.completed:not(:last-child)::after {
            background: #10b981;
        }

        .progress-bar-animated {
            animation: progressAnimation 1.5s ease-in-out;
        }

        @keyframes progressAnimation {
            from {
                width: 0%;
            }
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

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }

            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        .pulse-ring::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid currentColor;
            animation: pulse-ring 1.5s ease-out infinite;
        }
    </style>
@endpush

@section('breadcrumb')
    <a @can('factures.read') href="{{ route('factures.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Factures</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('factures.view-details') href="{{ route('factures.show', $factureId) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">{{ $paiement->facture->numero_facture }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('paiements.read') href="{{ route('paiements.index', ['factureId' => $factureId]) }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Paiements</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">{{ $paiement->reference_paiement }}</span>
@endsection

@section('content')
    @php
        // Configuration des statuts
        $couleurs = [0 => 'yellow', 1 => 'blue', 2 => 'indigo', 3 => 'green', 4 => 'red', 5 => 'gray'];
        $couleur = $couleurs[$paiement->statut_paiement] ?? 'gray';

        $icones = [
            0 => 'fas fa-clock',
            1 => 'fas fa-check-circle',
            2 => 'fas fa-spinner fa-spin',
            3 => 'fas fa-check-double',
            4 => 'fas fa-times-circle',
            5 => 'fas fa-ban',
        ];
        $icone = $icones[$paiement->statut_paiement] ?? 'fas fa-question-circle';

        // Données contextuelles
        $facture = $paiement->facture;
        $proforma = $facture->proforma ?? null;
        $attribution = $proforma?->prestatairePrincipal;
        $lot = $attribution?->lot;
        $appelOffre = $lot?->appelOffre;
        $prestataire = $proforma?->getPrestataire();

        // Calculs financiers
        $montantFacture = $facture->montant_facture ?? 0;
        $montantPayeTotal = $facture->montant_paye ?? 0;
        $montantRestant = $facture->montant_restant ?? 0;
        $pourcentagePaye = $montantFacture > 0 ? min(100, ($montantPayeTotal / $montantFacture) * 100) : 0;
    @endphp

    <!-- Header avec actions -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Titre et retour -->
                <div class="flex items-center space-x-4">
                    @can('paiements.read')
                        <a href="{{ route('paiements.index', ['factureId' => $factureId]) }}"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan

                    <div>
                        <div class="flex items-center space-x-3 flex-wrap gap-2">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $paiement->reference_paiement }}</h1>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-{{ $couleur }}-100 text-{{ $couleur }}-800">
                                <i class="{{ $icone }} mr-1"></i>
                                {{ $paiement->statut_libelle }}
                            </span>
                        </div>
                        <p class="text-gray-600 mt-1">
                            <span
                                class="font-semibold text-green-600">{{ number_format(floor($paiement->montant_net_paye_paiement), 0, ',', ' ') }}
                                FCFA</span>
                            • Facture {{ $facture->numero_facture }}
                        </p>
                    </div>
                </div>

                @canany(['paiements.reject', 'paiements.update', 'paiements.validate', 'paiements.confirm',
                    'paiements.cancel', 'paiements.process', 'paiements.pending', 'paiements.view-details', 'paiements.delete'])
                    <!-- Actions -->
                    <div class="flex items-center space-x-2 flex-wrap gap-2">
                        @can('paiements.validate')
                            @if ($paiement->peutEtreValide())
                                <button onclick="valider()"
                                    class="px-4 py-2.5 bg-white border border-blue-300 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-check text-sm"></i>
                                    <span class="text-sm font-medium">Valider</span>
                                </button>
                            @endif
                        @endcan

                        @can('paiements.process')
                            @if ($paiement->statut_paiement == 1)
                                <button onclick="mettreEnTraitement()"
                                    class="px-4 py-2.5 bg-white border border-indigo-300 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-spinner text-sm"></i>
                                    <span class="text-sm font-medium">Traitement</span>
                                </button>
                            @endif
                        @endcan

                        @can('paiements.confirm')
                            @if (in_array($paiement->statut_paiement, [1, 2]))
                                <button onclick="confirmer()"
                                    class="px-4 py-2.5 bg-white border border-green-300 text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-check-double text-sm"></i>
                                    <span class="text-sm font-medium">Confirmer</span>
                                </button>
                            @endif
                        @endcan

                        @can('paiements.update')
                            @if ($paiement->peutEtreModifie())
                                <a href="{{ route('paiements.edit', ['factureId' => $factureId, 'paiement' => $paiement->id_paiement]) }}"
                                    class="px-4 py-2.5 bg-white border border-orange-300 text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-edit text-sm"></i>
                                    <span class="text-sm font-medium">Modifier</span>
                                </a>
                            @endif
                        @endcan

                        @canany(['paiements.reject', 'paiements.cancel', 'paiements.pending', 'paiements.view-details',
                            'paiements.delete'])
                            <!-- Menu dropdown -->
                            <div class="relative">
                                <button onclick="toggleMenu()" id="menuBtn"
                                    class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-ellipsis-v text-sm"></i>
                                </button>
                                <div id="actionMenu"
                                    class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-20">
                                    <div class="py-1">
                                        @can('paiements.reject')
                                            @if ($paiement->peutEtreRejete())
                                                <button onclick="showRejectModal()"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="fas fa-times-circle mr-2 text-red-500"></i>
                                                    Rejeter
                                                </button>
                                            @endif
                                        @endcan

                                        @can('paiements.cancel')
                                            @if ($paiement->peutEtreAnnule())
                                                <button onclick="showCancelModal()"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="fas fa-ban mr-2 text-gray-500"></i>
                                                    Annuler
                                                </button>
                                            @endif
                                        @endcan

                                        @can('paiements.pending')
                                            @if ($paiement->statut_paiement == 4)
                                                <button onclick="remettreEnAttente()"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="fas fa-undo mr-2 text-yellow-500"></i>
                                                    Remettre en attente
                                                </button>
                                            @endif
                                        @endcan

                                        @can('paiements.view-details')
                                            <a href="{{ route('factures.show', $paiement->facture_id) }}"
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <i class="fas fa-file-invoice mr-2 text-purple-500"></i>
                                                Voir la facture
                                            </a>
                                        @endcan

                                        @can('paiements.delete')
                                            @if ($paiement->statut_paiement != 3)
                                                <button onclick="confirmDelete()"
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                                    <i class="fas fa-trash mr-2"></i>
                                                    Supprimer
                                                </button>
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

        <!-- Fil d'Ariane Contextuel -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-sitemap text-indigo-500 mr-2"></i>
                    Contexte du paiement
                </h2>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <!-- Appel d'Offre -->
                    @if ($appelOffre)
                        <a @can('appels_offres.view-details') href="{{ route('appels-offres.show', $appelOffre->id_appel_offre) }}" @endcan
                            class="flex items-center bg-purple-50 px-3 py-2 rounded-lg hover:bg-purple-100 transition-all">
                            <i class="fas fa-bullhorn text-purple-500 mr-2"></i>
                            <div>
                                <span class="text-xs text-purple-600 block">Appel d'Offre</span>
                                <span
                                    class="font-semibold text-purple-800">{{ $appelOffre->numero_appel_offre ?? 'N/A' }}</span>
                            </div>
                        </a>
                        <i class="fas fa-chevron-right text-gray-300"></i>
                    @endif

                    <!-- Lot -->
                    @if ($lot)
                        <a @can('lots.view-details') href="{{ route('lots.show', $lot->id_lot) }}" @endcan
                            class="flex items-center bg-blue-50 px-3 py-2 rounded-lg hover:bg-blue-100 transition-all">
                            <i class="fas fa-cubes text-blue-500 mr-2"></i>
                            <div>
                                <span class="text-xs text-blue-600 block">Lot</span>
                                <span class="font-semibold text-blue-800">{{ $lot->numero ?? 'N/A' }}</span>
                            </div>
                        </a>
                        <i class="fas fa-chevron-right text-gray-300"></i>
                    @endif

                    <!-- Proforma -->
                    @if ($proforma)
                        <div class="flex items-center bg-amber-50 px-3 py-2 rounded-lg">
                            <i class="fas fa-file-alt text-amber-500 mr-2"></i>
                            <div>
                                <span class="text-xs text-amber-600 block">Proforma</span>
                                <span class="font-semibold text-amber-800">{{ $proforma->numero_proforma ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-300"></i>
                    @endif

                    <!-- Facture -->
                    <a @can('factures.view-details') href="{{ route('factures.show', $facture->id_facture) }}" @endcan
                        class="flex items-center bg-teal-50 px-3 py-2 rounded-lg hover:bg-teal-100 transition-all">
                        <i class="fas fa-file-invoice-dollar text-teal-500 mr-2"></i>
                        <div>
                            <span class="text-xs text-teal-600 block">Facture</span>
                            <span class="font-semibold text-teal-800">{{ $facture->numero_facture }}</span>
                        </div>
                    </a>

                    <i class="fas fa-chevron-right text-gray-300"></i>

                    <!-- Paiement (actuel) -->
                    <div class="flex items-center bg-green-50 px-3 py-2 rounded-lg border-2 border-green-300">
                        <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                        <div>
                            <span class="text-xs text-green-600 block">Paiement</span>
                            <span class="font-semibold text-green-800">{{ $paiement->reference_paiement }}</span>
                        </div>
                    </div>
                </div>

                @if ($lot)
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-info-circle text-gray-400 mr-1"></i>
                            <strong>Objet :</strong> {{ Str::limit($lot->libelle, 120) }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="xl:col-span-2 space-y-6">

                <!-- Montant et Progression -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-coins text-green-500 mr-2"></i>
                            Montant du paiement
                        </h2>
                    </div>
                    <div class="p-6">
                        <!-- Montant principal -->
                        <div class="text-center mb-6 p-6 bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl">
                            <p class="text-sm text-gray-500 uppercase tracking-wide mb-2">Montant payé</p>
                            <p class="text-4xl font-bold text-green-600">
                                {{ number_format(floor($paiement->montant_net_paye_paiement), 0, ',', ' ') }}
                                <span class="text-xl">FCFA</span>
                            </p>
                            <p class="text-sm text-gray-500 mt-2">
                                Représente <span
                                    class="font-semibold text-green-600">{{ number_format(($paiement->montant_net_paye_paiement / $montantFacture) * 100, 2) }}%</span>
                                de la facture
                            </p>
                        </div>

                        <!-- Progression globale de la facture -->
                        <div class="border-t pt-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-4">Progression du règlement de la facture
                            </h3>

                            <div class="mb-4">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-600">Total payé sur la facture</span>
                                    <span
                                        class="font-bold text-emerald-600">{{ number_format($pourcentagePaye, 2) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                    <div class="h-3 rounded-full progress-bar-animated transition-all duration-500
                                        @if ($pourcentagePaye >= 100) bg-green-500
                                        @elseif($pourcentagePaye >= 50) bg-emerald-500
                                        @elseif($pourcentagePaye > 0) bg-amber-500
                                        @else bg-gray-300 @endif"
                                        style="width: {{ $pourcentagePaye }}%">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div class="bg-gray-50 rounded-xl p-3">
                                    <p class="text-xs text-gray-500 mb-1">Facture</p>
                                    <p class="font-bold text-gray-800">{{ number_format(floor($montantFacture), 0, ',', ' ') }}
                                    </p>
                                </div>
                                <div class="bg-green-50 rounded-xl p-3">
                                    <p class="text-xs text-green-600 mb-1">Total payé</p>
                                    <p class="font-bold text-green-700">
                                        {{ number_format(floor($montantPayeTotal), 0, ',', ' ') }}</p>
                                </div>
                                <div class="bg-orange-50 rounded-xl p-3">
                                    <p class="text-xs text-orange-600 mb-1">Reste</p>
                                    <p class="font-bold text-orange-700">{{ number_format(floor($montantRestant), 0, ',', ' ') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline du Workflow -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-stream text-indigo-500 mr-2"></i>
                            Workflow du paiement
                        </h2>
                    </div>
                    <div class="p-6">
                        @php
                            $steps = [
                                ['status' => 0, 'label' => 'Créé', 'icon' => 'fa-plus-circle', 'color' => 'gray'],
                                ['status' => 0, 'label' => 'En attente', 'icon' => 'fa-clock', 'color' => 'yellow'],
                                ['status' => 1, 'label' => 'Validé', 'icon' => 'fa-check-circle', 'color' => 'blue'],
                                [
                                    'status' => 2,
                                    'label' => 'En traitement',
                                    'icon' => 'fa-spinner',
                                    'color' => 'indigo',
                                ],
                                ['status' => 3, 'label' => 'Payé', 'icon' => 'fa-check-double', 'color' => 'green'],
                            ];

                            $currentStatus = $paiement->statut_paiement;
                            $isRejected = $currentStatus == 4;
                            $isCancelled = $currentStatus == 5;
                        @endphp

                        @if ($isRejected || $isCancelled)
                            <div
                                class="mb-6 p-4 rounded-xl {{ $isRejected ? 'bg-red-50 border border-red-200' : 'bg-gray-100 border border-gray-200' }}">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-full {{ $isRejected ? 'bg-red-100' : 'bg-gray-200' }} flex items-center justify-center mr-3">
                                        <i
                                            class="fas {{ $isRejected ? 'fa-times-circle text-red-500' : 'fa-ban text-gray-500' }}"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold {{ $isRejected ? 'text-red-800' : 'text-gray-700' }}">
                                            {{ $isRejected ? 'Paiement rejeté' : 'Paiement annulé' }}
                                        </p>
                                        @if ($paiement->motif_rejet_paiement)
                                            <p class="text-sm {{ $isRejected ? 'text-red-600' : 'text-gray-600' }}">
                                                {{ $paiement->motif_rejet_paiement }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-0">
                            @foreach ($steps as $index => $step)
                                @php
                                    $isCompleted = false;
                                    $isCurrent = false;

                                    if ($index == 0) {
                                        $isCompleted = true; // Toujours créé
                                    } elseif ($step['status'] == 0 && $index == 1) {
                                        $isCompleted = $currentStatus >= 0;
                                        $isCurrent = $currentStatus == 0;
                                    } else {
                                        $isCompleted = $currentStatus >= $step['status'] && $currentStatus <= 3;
                                        $isCurrent = $currentStatus == $step['status'];
                                    }
                                @endphp

                                <div class="timeline-step {{ $isCompleted ? 'completed' : '' }} flex items-start pb-6">
                                    <div class="relative flex-shrink-0">
                                        <div
                                            class="w-10 h-10 rounded-full flex items-center justify-center
                                            @if ($isCurrent && !$isRejected && !$isCancelled) bg-{{ $step['color'] }}-500 text-white pulse-ring
                                            @elseif($isCompleted)
                                                bg-{{ $step['color'] }}-100 text-{{ $step['color'] }}-600
                                            @else
                                                bg-gray-100 text-gray-400 @endif">
                                            <i class="fas {{ $step['icon'] }} text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p
                                            class="font-medium {{ $isCompleted || $isCurrent ? 'text-gray-900' : 'text-gray-400' }}">
                                            {{ $step['label'] }}
                                        </p>
                                        @if ($index == 0 && $paiement->created_at)
                                            <p class="text-xs text-gray-500">
                                                {{ $paiement->created_at->format('d/m/Y à H:i') }}</p>
                                            @if ($paiement->createur)
                                                <p class="text-xs text-gray-400">Par
                                                    {{ $paiement->createur->nom_complet ?? 'N/A' }}</p>
                                            @endif
                                        @elseif($step['status'] == 1 && $paiement->date_validation_paiement)
                                            <p class="text-xs text-gray-500">
                                                {{ $paiement->date_validation_paiement->format('d/m/Y à H:i') }}</p>
                                            @if ($paiement->validateur)
                                                <p class="text-xs text-gray-400">Par
                                                    {{ $paiement->validateur->nom_complet ?? 'N/A' }}</p>
                                            @endif
                                        @elseif($step['status'] == 3 && $paiement->payeur)
                                            <p class="text-xs text-gray-400">Par
                                                {{ $paiement->payeur->nom_complet ?? 'N/A' }}</p>
                                        @endif
                                    </div>
                                    @if ($isCurrent && !$isRejected && !$isCancelled)
                                        <span
                                            class="px-2 py-1 text-xs font-medium bg-{{ $step['color'] }}-100 text-{{ $step['color'] }}-700 rounded-full">
                                            Actuel
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Informations bancaires détaillées -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-university text-blue-500 mr-2"></i>
                            Compte bancaire destinataire
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-6 p-4 bg-blue-50 rounded-xl">
                            <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                <i class="fas fa-building text-blue-500 text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-xl font-bold text-gray-800">{{ $paiement->banque->nom_banque }}</p>
                                <p class="text-sm text-gray-500">{{ $paiement->banque->code_banque ?? '' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Titulaire du
                                        compte</label>
                                    <p class="text-base font-medium text-gray-900 mt-1">
                                        {{ $paiement->banque->titulaire_compte_banque ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Numéro de
                                        compte</label>
                                    <p
                                        class="text-base font-mono font-medium text-gray-900 mt-1 bg-gray-50 px-3 py-2 rounded-lg">
                                        {{ $paiement->banque->numero_compte_banque }}
                                    </p>
                                </div>
                                @if ($paiement->banque->code_guichet_banque)
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Code
                                            guichet</label>
                                        <p class="text-base font-mono text-gray-900 mt-1">
                                            {{ $paiement->banque->code_guichet_banque }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-4">
                                @if ($paiement->banque->iban_banque)
                                    <div>
                                        <label
                                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide">IBAN</label>
                                        <p
                                            class="text-sm font-mono text-gray-900 mt-1 bg-gray-50 px-3 py-2 rounded-lg break-all">
                                            {{ $paiement->banque->iban_banque }}
                                        </p>
                                    </div>
                                @endif
                                @if ($paiement->banque->swift_bic_banque)
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">SWIFT /
                                            BIC</label>
                                        <p class="text-base font-mono font-medium text-gray-900 mt-1">
                                            {{ $paiement->banque->swift_bic_banque }}</p>
                                    </div>
                                @endif
                                @if ($paiement->banque->rib_complet)
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">RIB
                                            complet</label>
                                        <p class="text-sm font-mono text-gray-900 mt-1 bg-gray-50 px-3 py-2 rounded-lg">
                                            {{ $paiement->banque->rib_complet }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observations et Motifs -->
                @if ($paiement->observations_paiement || $paiement->motif_rejet_paiement)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-comment-alt text-gray-500 mr-2"></i>
                                Notes & Observations
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            @if ($paiement->observations_paiement)
                                <div>
                                    <label class="text-sm font-semibold text-gray-600 flex items-center mb-2">
                                        <i class="fas fa-sticky-note text-gray-400 mr-2"></i>
                                        Observations
                                    </label>
                                    <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-lg">
                                        {{ $paiement->observations_paiement }}
                                    </p>
                                </div>
                            @endif

                            @if ($paiement->motif_rejet_paiement)
                                <div>
                                    <label class="text-sm font-semibold text-red-600 flex items-center mb-2">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Motif de rejet / annulation
                                    </label>
                                    <p class="text-sm text-red-700 bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                                        {{ $paiement->motif_rejet_paiement }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Autres paiements de la facture -->
                @php
                    $autresPaiements = $facture->paiements->where('id_paiement', '!=', $paiement->id_paiement);
                @endphp
                @if ($autresPaiements->count() > 0)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-history text-purple-500 mr-2"></i>
                                Autres paiements de cette facture
                                <span
                                    class="ml-2 px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">
                                    {{ $autresPaiements->count() }}
                                </span>
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Référence</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                            Montant</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                            Statut</th>
                                            @can('paiements.view-details')
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                            Action</th>
                                            @endcan
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($autresPaiements->sortByDesc('created_at') as $autrePaiement)
                                        @php
                                            $autreStatutCouleur = $couleurs[$autrePaiement->statut_paiement] ?? 'gray';
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                {{ $autrePaiement->reference_paiement }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $autrePaiement->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right font-semibold text-gray-800">
                                                {{ number_format($autrePaiement->montant_net_paye_paiement, 0, ',', ' ') }}
                                                FCFA
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $autreStatutCouleur }}-100 text-{{ $autreStatutCouleur }}-800">
                                                    {{ $autrePaiement->statut_libelle }}
                                                </span>
                                            </td>
                                            @can('paiements.view-details')
                                            <td class="px-4 py-3 text-center">
                                                <a href="{{ route('paiements.show', ['factureId' => $factureId, 'paiement' => $autrePaiement->id_paiement]) }}"
                                                    class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">

                <!-- Informations Prestataire -->
                @if ($prestataire)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden info-card">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-building text-orange-500 mr-2"></i>
                                Prestataire
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                                    <i class="fas fa-user-tie text-orange-500 text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $prestataire->raison_sociale_prestataire }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $prestataire->numero_cc_prestataire ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm border-t pt-4">
                                @if ($prestataire->email_prestataire)
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-envelope w-5 text-gray-400"></i>
                                        <span class="ml-2 truncate">{{ $prestataire->email_prestataire }}</span>
                                    </div>
                                @endif
                                @if ($prestataire->telephone_principal_prestataire)
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-phone w-5 text-gray-400"></i>
                                        <span class="ml-2">{{ $prestataire->telephone_principal_prestataire }}</span>
                                    </div>
                                @endif
                                @if ($prestataire->adresse_prestataire)
                                    <div class="flex items-start text-gray-600">
                                        <i class="fas fa-map-marker-alt w-5 text-gray-400 mt-0.5"></i>
                                        <span class="ml-2">{{ $prestataire->getAdresseComplete() }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Représentant légal --}}
                            @if ($prestataire->representant_legal_prestataire)
                                @php
                                    $representant = is_array($prestataire->representant_legal_prestataire)
                                        ? $prestataire->representant_legal_prestataire
                                        : json_decode($prestataire->representant_legal_prestataire, true);
                                @endphp

                                @if ($representant && (!empty($representant['nom']) || !empty($representant['prenoms'])))
                                    <div class="border-t pt-3 mt-3">
                                        <p class="text-xs text-gray-500 uppercase font-semibold mb-2">
                                            <i class="fas fa-user-tie text-gray-400 mr-1"></i>
                                            Représentant Légal
                                        </p>
                                        <div class="flex items-center text-gray-700">
                                            <span class="font-medium">
                                                {{ $representant['prenoms'] ?? '' }} {{ $representant['nom'] ?? '' }}
                                            </span>
                                        </div>
                                        @if (!empty($representant['contact']))
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-phone text-gray-400 mr-1"></i>
                                                {{ $representant['contact'] }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Récapitulatif Facture -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden info-card">
                    <div class="px-6 py-4 bg-gradient-to-r from-teal-50 to-white border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-invoice text-teal-500 mr-2"></i>
                            Facture
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="detail-row">
                            <span class="detail-label">Numéro</span>
                            <span
                                class="detail-value font-mono bg-teal-50 px-2 py-0.5 rounded text-sm">{{ $facture->numero_facture }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Date</span>
                            <span class="detail-value">{{ $facture->date_facture?->format('d/m/Y') ?? '-' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Montant</span>
                            <span
                                class="detail-value font-bold text-teal-600">{{ number_format(floor($montantFacture), 0, ',', ' ') }}
                                FCFA</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Statut</span>
                            @php
                                $factureCouleur =
                                    [
                                        'en_attente' => 'yellow',
                                        'validee' => 'blue',
                                        'rejetee' => 'red',
                                        'payee' => 'green',
                                        'partiellement_payee' => 'orange',
                                        'annulee' => 'gray',
                                    ][$facture->statut_facture] ?? 'gray';
                            @endphp
                            <span
                                class="px-2 py-0.5 text-xs font-medium bg-{{ $factureCouleur }}-100 text-{{ $factureCouleur }}-700 rounded-full">
                                {{ $facture->statut_libelle }}
                            </span>
                        </div>
                        @can('factures.view-details')
                        <div class="pt-3 border-t">
                            <a href="{{ route('factures.show', $facture->id_facture) }}"
                                class="block w-full px-4 py-2 bg-teal-50 text-teal-600 text-center rounded-lg hover:bg-teal-100 transition-all text-sm font-medium">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                Voir la facture
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>

                <!-- Proforma -->
                @if ($proforma)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden info-card">
                        <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-white border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-file-alt text-amber-500 mr-2"></i>
                                Proforma
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="detail-row">
                                <span class="detail-label">Numéro</span>
                                <span class="detail-value font-mono text-sm">{{ $proforma->numero_proforma }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Montant HT</span>
                                <span
                                    class="detail-value">{{ number_format($proforma->montant_retenu_proforma ?? 0, 0, ',', ' ') }}</span>
                            </div>
                            @if ($proforma->taxe_montant > 0)
                                <div class="detail-row">
                                    <span class="detail-label">TVA</span>
                                    <span class="detail-value text-blue-600">+
                                        {{ number_format($proforma->taxe_montant, 0, ',', ' ') }}</span>
                                </div>
                            @endif
                            @if ($proforma->remise_montant_proforma > 0)
                                <div class="detail-row">
                                    <span class="detail-label">Remise</span>
                                    <span class="detail-value text-red-600">-
                                        {{ number_format($proforma->remise_montant_proforma, 0, ',', ' ') }}</span>
                                </div>
                            @endif
                            <div class="detail-row border-t pt-2">
                                <span class="detail-label font-semibold">Total TTC</span>
                                <span
                                    class="detail-value font-bold text-amber-600">{{ number_format(floor($proforma->montant_ttc ?? 0), 0, ',', ' ') }}
                                    FCFA</span>
                            </div>
                        </div>
                    </div>
                @endif

                @canany(['paiements.validate', 'paiements.process', 'paiements.confirm', 'paiements.create', 'paiements.read'])
                <!-- Actions rapides -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden info-card">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-bolt text-gray-500 mr-2"></i>
                            Actions rapides
                        </h3>
                    </div>
                    <div class="p-4 space-y-2">
                        @can('paiements.validate')
                        @if ($paiement->peutEtreValide())
                            <button onclick="valider()"
                                class="w-full px-4 py-2.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-all text-sm font-medium flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i>
                                Valider le paiement
                            </button>
                        @endif
                        @endcan

                        @can('paiements.process')
                        @if ($paiement->statut_paiement == 1)
                            <button onclick="mettreEnTraitement()"
                                class="w-full px-4 py-2.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-all text-sm font-medium flex items-center justify-center">
                                <i class="fas fa-spinner mr-2"></i>
                                Mettre en traitement
                            </button>
                        @endif
                        @endcan

                        @can('paiements.confirm')
                        @if (in_array($paiement->statut_paiement, [1, 2]))
                            <button onclick="confirmer()"
                                class="w-full px-4 py-2.5 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-all text-sm font-medium flex items-center justify-center">
                                <i class="fas fa-check-double mr-2"></i>
                                Confirmer paiement effectué
                            </button>
                        @endif
                        @endcan

                        @can('paiements.create')
                        {{-- @if ($montantRestant > 0) --}}
                        @if($montantRestant > 0 && $facture->peutRecevoirPaiement() && $attribution &&  (!$attribution->date_retrait || !$attribution->date_suspension) )
                            <a href="{{ route('paiements.create', $factureId) }}"
                                class="w-full px-4 py-2.5 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-all text-sm font-medium flex items-center justify-center">
                                <i class="fas fa-plus mr-2"></i>
                                Nouveau paiement
                            </a>
                        @endif
                        @endcan

                        @can('paiements.read')
                        <a href="{{ route('paiements.index', ['factureId' => $factureId]) }}"
                            class="w-full px-4 py-2.5 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition-all text-sm font-medium flex items-center justify-center">
                            <i class="fas fa-list mr-2"></i>
                            Liste des paiements
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <!-- Audit -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden info-card">
                    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-user-clock text-slate-500 mr-2"></i>
                            Audit
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @if ($paiement->createur)
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-gray-500 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Enregistré par</p>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ $paiement->createur->nom_complet ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400">{{ $paiement->created_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if ($paiement->validateur)
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-check text-blue-500 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Validé par</p>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ $paiement->validateur->nom_complet ?? 'N/A' }}</p>
                                    @if ($paiement->date_validation_paiement)
                                        <p class="text-xs text-gray-400">
                                            {{ $paiement->date_validation_paiement->format('d/m/Y à H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($paiement->payeur)
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-check-double text-green-500 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Confirmé par</p>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ $paiement->payeur->nom_complet ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($paiement->updated_at && $paiement->updated_at != $paiement->created_at)
                            <div class="pt-3 border-t text-xs text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                Dernière modification: {{ $paiement->updated_at->format('d/m/Y à H:i') }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Modal Rejet -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                    <i class="fas fa-times-circle text-red-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Rejeter le paiement</h3>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Motif du rejet <span
                        class="text-red-500">*</span></label>
                <textarea id="rejectMotif" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400 focus:border-transparent"
                    placeholder="Expliquez pourquoi ce paiement est rejeté (minimum 10 caractères)"></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button onclick="closeRejectModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                    Annuler
                </button>
                @can('paiements.reject')
                <button onclick="executeReject()"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all">
                    <i class="fas fa-times-circle mr-1"></i>
                    Confirmer le rejet
                </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- Modal Annulation -->
    <div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                    <i class="fas fa-ban text-gray-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Annuler le paiement</h3>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Motif de l'annulation <span
                        class="text-red-500">*</span></label>
                <textarea id="cancelMotif" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 focus:border-transparent"
                    placeholder="Expliquez pourquoi ce paiement est annulé (minimum 10 caractères)"></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button onclick="closeCancelModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                    Annuler
                </button>
                @can('appels_offres.cancel')
                <button onclick="executeCancel()"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all">
                    <i class="fas fa-ban mr-1"></i>
                    Confirmer l'annulation
                </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- Modal Suppression -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                    <i class="fas fa-trash text-red-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Confirmer la suppression</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700">Êtes-vous sûr de vouloir supprimer ce paiement ?</p>
                <p class="text-sm text-red-600 mt-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Cette action est irréversible.
                </p>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                    Annuler
                </button>
                @can('paiements.delete')
                <button onclick="executeDelete()"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all">
                    <i class="fas fa-trash mr-1"></i>
                    Supprimer
                </button>
                @endcan
            </div>
        </div>
    </div>

    @can('paiements.view-details')
    @push('scripts')
        <script>
            const factureId = '{{ $factureId }}';
            const paiementId = '{{ $paiement->id_paiement }}';

            function toggleMenu() {
                document.getElementById('actionMenu').classList.toggle('hidden');
            }

            function valider() {
                if (confirm('Voulez-vous valider ce paiement ?')) {
                    fetch("{{ route('paiements.valider', [':factureId', ':paiementId']) }}".replace(':factureId', factureId)
                            .replace(':paiementId', paiementId), {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        });
                }
            }

            function mettreEnTraitement() {
                if (confirm('Mettre ce paiement en traitement bancaire ?')) {
                    fetch("{{ route('paiements.traitement', [':factureId', ':paiementId']) }}".replace(':factureId',
                            factureId).replace(':paiementId', paiementId), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        });
                }
            }

            function confirmer() {
                if (confirm('Confirmer que ce paiement a été effectué ?')) {
                    fetch("{{ route('paiements.confirmer', [':factureId', ':paiementId']) }}".replace(':factureId', factureId)
                            .replace(':paiementId', paiementId), {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        });
                }
            }

            function showRejectModal() {
                document.getElementById('rejectMotif').value = '';
                document.getElementById('rejectModal').classList.remove('hidden');
                document.getElementById('actionMenu').classList.add('hidden');
            }

            function closeRejectModal() {
                document.getElementById('rejectModal').classList.add('hidden');
            }

            function executeReject() {
                const motif = document.getElementById('rejectMotif').value.trim();
                if (motif.length < 10) {
                    alert('Le motif doit contenir au moins 10 caractères');
                    return;
                }

                fetch("{{ route('paiements.rejeter', [':factureId', ':paiementId']) }}".replace(':factureId', factureId)
                        .replace(':paiementId', paiementId), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                motif_rejet: motif
                            })
                        })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    });
            }

            function showCancelModal() {
                document.getElementById('cancelMotif').value = '';
                document.getElementById('cancelModal').classList.remove('hidden');
                document.getElementById('actionMenu').classList.add('hidden');
            }

            function closeCancelModal() {
                document.getElementById('cancelModal').classList.add('hidden');
            }

            function executeCancel() {
                const motif = document.getElementById('cancelMotif').value.trim();
                if (motif.length < 10) {
                    alert('Le motif doit contenir au moins 10 caractères');
                    return;
                }

                fetch("{{ route('paiements.annuler', [':factureId', ':paiementId']) }}".replace(':factureId', factureId)
                        .replace(':paiementId', paiementId), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                motif_annulation: motif
                            })
                        })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    });
            }

            function remettreEnAttente() {
                if (confirm('Remettre ce paiement en attente ?')) {
                    fetch("{{ route('paiements.remettre-attente', [':factureId', ':paiementId']) }}".replace(':factureId',
                            factureId).replace(':paiementId', paiementId), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        });
                }
            }

            function confirmDelete() {
                document.getElementById('deleteModal').classList.remove('hidden');
                document.getElementById('actionMenu').classList.add('hidden');
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
            }

            function executeDelete() {
                fetch("{{ route('paiements.destroy', [':factureId', ':paiementId']) }}".replace(':factureId', factureId)
                        .replace(':paiementId', paiementId), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = "{{ route('paiements.index', $factureId) }}";
                        } else {
                            alert(data.message);
                            closeDeleteModal();
                        }
                    });
            }

            // Fermer les modals avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeRejectModal();
                    closeCancelModal();
                    closeDeleteModal();
                    document.getElementById('actionMenu').classList.add('hidden');
                }
            });

            // Fermer le menu dropdown en cliquant ailleurs
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#actionMenu') && !e.target.closest('#menuBtn')) {
                    document.getElementById('actionMenu').classList.add('hidden');
                }
            });
        </script>
    @endpush
    @endcan
@endsection
