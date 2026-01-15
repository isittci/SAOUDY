@extends('layouts.main')

@section('title', 'Document - ' . $document->titre_document)

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
    }
    .file-icon-large {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
    }
    .file-icon-pdf { background: linear-gradient(135deg, #ff6b6b, #ee5a5a); }
    .file-icon-doc { background: linear-gradient(135deg, #4facfe, #00f2fe); }
    .file-icon-xls { background: linear-gradient(135deg, #11998e, #38ef7d); }
    .file-icon-ppt { background: linear-gradient(135deg, #f093fb, #f5576c); }
    .file-icon-img { background: linear-gradient(135deg, #667eea, #764ba2); }
    .file-icon-zip { background: linear-gradient(135deg, #ffa751, #ffe259); }
    .file-icon-default { background: linear-gradient(135deg, #a8edea, #fed6e3); }

    .gradient-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .gradient-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .gradient-orange { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .info-row:last-child {
        border-bottom: none;
    }

    .preview-container {
        background: #f8fafc;
        border-radius: 12px;
        overflow: hidden;
    }
    .preview-container img {
        max-width: 100%;
        max-height: 500px;
        object-fit: contain;
    }
    .preview-container iframe {
        width: 100%;
        height: 500px;
        border: none;
    }
</style>
@endpush

@section('breadcrumb')
    <a @can('appels_offres.read') href="{{ route('appels-offres.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Appels d'Offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('appels_offres.view-details') href="{{ route('appels-offres.show', $lot->appel_offre_id) }}" @endcan class="text-white/80 hover:text-white transition-colors">{{ $lot->appelOffre->numero_appel_offre ?? 'AO' }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('lots.view-details') href="{{ route('lots.show', $lot->id_lot) }}" @endcan class="text-white/80 hover:text-white transition-colors">Lot {{ $lot->numero }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('documents_lots.read') href="{{ route('lots.documents.index', $lot->id_lot) }}" @endcan class="text-white/80 hover:text-white transition-colors">Documents</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">{{ Str::limit($document->titre_document, 30) }}</span>
@endsection

@section('content')
    @php
        $extension = strtolower(pathinfo($document->fichier_nom_document, PATHINFO_EXTENSION));
        $iconInfo = match(true) {
            in_array($extension, ['pdf']) => ['icon' => 'fa-file-pdf', 'bg' => 'file-icon-pdf', 'color' => 'text-red-500'],
            in_array($extension, ['doc', 'docx']) => ['icon' => 'fa-file-word', 'bg' => 'file-icon-doc', 'color' => 'text-blue-500'],
            in_array($extension, ['xls', 'xlsx']) => ['icon' => 'fa-file-excel', 'bg' => 'file-icon-xls', 'color' => 'text-green-500'],
            in_array($extension, ['ppt', 'pptx']) => ['icon' => 'fa-file-powerpoint', 'bg' => 'file-icon-ppt', 'color' => 'text-orange-500'],
            in_array($extension, ['jpg', 'jpeg', 'png', 'gif']) => ['icon' => 'fa-file-image', 'bg' => 'file-icon-img', 'color' => 'text-purple-500'],
            in_array($extension, ['zip', 'rar']) => ['icon' => 'fa-file-archive', 'bg' => 'file-icon-zip', 'color' => 'text-yellow-500'],
            default => ['icon' => 'fa-file', 'bg' => 'file-icon-default', 'color' => 'text-gray-500'],
        };
        $isPreviewable = in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'gif']);
    @endphp

    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    @can('documents_lots.read')
                    <a href="{{ route('lots.documents.index', $lot->id_lot) }}"
                       class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    @endcan
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">
                            <i class="fas {{ $iconInfo['icon'] }} {{ $iconInfo['color'] }} mr-2"></i>
                            {{ $document->titre_document }}
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">Lot {{ $lot->numero }} - {{ $lot->libelle }}</p>
                    </div>
                </div>
                @canany(['documents_lots.download', 'documents_lots.update'])
                <div class="flex items-center gap-2">
                    @can('documents_lots.download')
                    <a href="{{ route('lots.documents.download', [$lot->id_lot, $document->id_document]) }}"
                       class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-download mr-2"></i>Télécharger
                    </a>
                    @endcan

                    @can('documents_lots.update')
                    <a href="{{ route('lots.documents.edit', [$lot->id_lot, $document->id_document]) }}"
                       class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </a>
                    @endcan
                </div>
                @endcanany
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages Flash -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Version</span>
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-code-branch text-purple-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-purple-600">v{{ $document->version_document ?? 1 }}</p>
            </div>

            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Taille</span>
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-weight-hanging text-blue-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($document->fichier_taille_document, 2) }} <span class="text-sm font-normal">Mo</span></p>
            </div>

            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Format</span>
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file text-orange-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-orange-600 uppercase">{{ $extension }}</p>
            </div>

            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Statut</span>
                    <div class="w-8 h-8 {{ $document->est_valide_document ? 'bg-green-100' : 'bg-yellow-100' }} rounded-lg flex items-center justify-center">
                        <i class="fas {{ $document->est_valide_document ? 'fa-check-circle text-green-600' : 'fa-clock text-yellow-600' }} text-sm"></i>
                    </div>
                </div>
                @if($document->est_valide_document)
                    <p class="text-lg font-bold text-green-600">Validé</p>
                @else
                    <p class="text-lg font-bold text-yellow-600">En attente</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne gauche - Détails -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Informations du document -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            Informations
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-center mb-6">
                            <div class="file-icon-large {{ $iconInfo['bg'] }}">
                                <i class="fas {{ $iconInfo['icon'] }} text-white text-3xl"></i>
                            </div>
                        </div>

                        <div class="space-y-0">
                            <div class="info-row">
                                <span class="text-gray-500 text-sm">Type</span>
                                <span class="font-medium text-gray-800">{{ $typesDocuments[$document->type_document] ?? $document->type_document }}</span>
                            </div>
                            <div class="info-row">
                                <span class="text-gray-500 text-sm">Nom du fichier</span>
                                <span class="font-medium text-gray-800 text-right max-w-[60%] truncate" title="{{ $document->fichier_nom_document }}">
                                    {{ $document->fichier_nom_document }}
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="text-gray-500 text-sm">Type MIME</span>
                                <span class="font-medium text-gray-600 text-xs">{{ $document->fichier_type_document }}</span>
                            </div>
                            <div class="info-row">
                                <span class="text-gray-500 text-sm">Date document</span>
                                <span class="font-medium text-gray-800">
                                    {{ $document->date_document ? $document->date_document->format('d/m/Y') : '-' }}
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="text-gray-500 text-sm">Créé le</span>
                                <span class="font-medium text-gray-800">{{ $document->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            @if($document->updated_at != $document->created_at)
                            <div class="info-row">
                                <span class="text-gray-500 text-sm">Modifié le</span>
                                <span class="font-medium text-gray-800">{{ $document->updated_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            @endif
                        </div>

                        @if($document->description_document)
                            <div class="mt-4 p-4 bg-gray-50 rounded-xl">
                                <p class="text-sm text-gray-500 mb-1">Description</p>
                                <p class="text-gray-700">{{ $document->description_document }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Validation -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-check-double text-green-500 mr-2"></i>
                            Validation
                        </h2>
                    </div>

                    <div class="p-6">

                        @if($document->est_valide_document)
                            <div class="text-center">
                                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-check text-green-600 text-2xl"></i>
                                </div>
                                <p class="font-medium text-green-600 mb-2">Document validé</p>
                                @if($document->validateur)
                                    <p class="text-sm text-gray-500">Par {{ $document->validateur->name }}</p>
                                @endif
                                @if($document->valide_at)
                                    <p class="text-sm text-gray-500">Le {{ $document->valide_at->format('d/m/Y à H:i') }}</p>
                                @endif
                                @can('documents_lots.toggle-status')
                                    <form action="{{ route('lots.documents.invalider', [$lot->id_lot, $document->id_document]) }}" method="POST" class="mt-4">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-4 py-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 text-sm font-medium rounded-lg transition-colors">
                                            <i class="fas fa-undo mr-1"></i>Annuler la validation
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        @else
                            <div class="text-center">
                                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                                </div>
                                <p class="font-medium text-yellow-600 mb-4">En attente de validation</p>

                                @can('documents_lots.toggle-status')
                                <form action="{{ route('lots.documents.valider', [$lot->id_lot, $document->id_document]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors shadow-sm">
                                        <i class="fas fa-check mr-2"></i>Valider ce document
                                    </button>
                                </form>
                                @endcan
                            </div>
                        @endif
                    </div>

                </div>

                @canany(['documents_lots.download', 'appels_offres.read', 'documents_lots.update', 'documents_lots.delete'])
                    <!-- Actions -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-cog text-orange-500 mr-2"></i>
                                Actions
                            </h2>
                        </div>
                        <div class="p-4 space-y-2">
                            @can('documents_lots.download')
                                <a href="{{ route('lots.documents.download', [$lot->id_lot, $document->id_document]) }}"
                                class="flex items-center w-full px-4 py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl transition-colors">
                                    <i class="fas fa-download mr-3"></i>
                                    <span class="font-medium">Télécharger</span>
                                </a>
                            @endcan

                            @can('appels_offres.read')
                                @if($isPreviewable)
                                    <a href="{{ route('lots.documents.preview', [$lot->id_lot, $document->id_document]) }}"
                                    target="_blank"
                                    class="flex items-center w-full px-4 py-3 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-xl transition-colors">
                                        <i class="fas fa-external-link-alt mr-3"></i>
                                        <span class="font-medium">Ouvrir dans un nouvel onglet</span>
                                    </a>
                                @endif
                            @endcan

                            @can('documents_lots.update')
                                <a href="{{ route('lots.documents.edit', [$lot->id_lot, $document->id_document]) }}"
                                class="flex items-center w-full px-4 py-3 bg-orange-50 hover:bg-orange-100 text-orange-700 rounded-xl transition-colors">
                                    <i class="fas fa-edit mr-3"></i>
                                    <span class="font-medium">Modifier</span>
                                </a>
                            @endcan

                            @can('documents_lots.delete')
                                <form action="{{ route('lots.documents.destroy', [$lot->id_lot, $document->id_document]) }}"
                                    method="POST"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center w-full px-4 py-3 bg-red-50 hover:bg-red-100 text-red-700 rounded-xl transition-colors">
                                        <i class="fas fa-trash mr-3"></i>
                                        <span class="font-medium">Supprimer</span>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                @endcan
            </div>

            <!-- Colonne droite - Prévisualisation -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-eye text-purple-500 mr-2"></i>
                            Prévisualisation
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($isPreviewable)
                            <div class="preview-container">
                                @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                    <div class="flex items-center justify-center p-4">
                                        <img src="{{ route('lots.documents.preview', [$lot->id_lot, $document->id_document]) }}"
                                             alt="{{ $document->titre_document }}"
                                             class="rounded-lg shadow-md">
                                    </div>
                                @elseif($extension === 'pdf')
                                    <iframe src="{{ route('lots.documents.preview', [$lot->id_lot, $document->id_document]) }}"
                                            class="w-full rounded-lg"></iframe>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="file-icon-large {{ $iconInfo['bg'] }} mx-auto mb-6">
                                    <i class="fas {{ $iconInfo['icon'] }} text-white text-3xl"></i>
                                </div>
                                <p class="text-gray-500 mb-2">La prévisualisation n'est pas disponible pour ce type de fichier.</p>
                                @can('documents_lots.download')
                                <p class="text-gray-400 text-sm mb-6">Téléchargez le fichier pour le consulter.</p>
                                <a href="{{ route('lots.documents.download', [$lot->id_lot, $document->id_document]) }}"
                                   class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl transition-colors shadow-sm">
                                    <i class="fas fa-download mr-2"></i>Télécharger le fichier
                                </a>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </main>
@endsection
