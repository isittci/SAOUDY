@extends('layouts.main')

@section('title', 'Documents du Lot - ' . $lot->numero)

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
    }
    .document-card {
        transition: all 0.3s ease;
    }
    .document-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .file-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    .file-icon-pdf { background: linear-gradient(135deg, #ff6b6b, #ee5a5a); }
    .file-icon-doc { background: linear-gradient(135deg, #4facfe, #00f2fe); }
    .file-icon-xls { background: linear-gradient(135deg, #11998e, #38ef7d); }
    .file-icon-ppt { background: linear-gradient(135deg, #f093fb, #f5576c); }
    .file-icon-img { background: linear-gradient(135deg, #667eea, #764ba2); }
    .file-icon-zip { background: linear-gradient(135deg, #ffa751, #ffe259); }
    .file-icon-default { background: linear-gradient(135deg, #a8edea, #fed6e3); }

    .drop-zone {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .drop-zone:hover,
    .drop-zone.drag-over {
        border-color: #f97316;
        background-color: #fff7ed;
    }
    .drop-zone.drag-over {
        transform: scale(1.01);
    }

    .gradient-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .gradient-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .gradient-orange { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }
    .gradient-red { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); }

    .scroll-container {
        max-height: 500px;
        overflow-y: auto;
    }
</style>
@endpush

@section('breadcrumb')
    <a @can('appels_offres.read') href="{{ route('appels-offres.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Appels d'Offres</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('appels_offres.view-details') href="{{ route('appels-offres.show', $lot->appel_offre_id) }}" @endcan class="text-white/80 hover:text-white transition-colors">{{ $lot->appelOffre->numero_appel_offre ?? 'AO' }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('lots.view-details') href="{{ route('lots-appels-offres.show', [$lot->appel_offre_id, $lot->id_lot]) }}" @endcan class="text-white/80 hover:text-white transition-colors">Lot {{ $lot->numero }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Documents</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    @can('appels_offres.view-details')
                        <a href="{{ route('lots-appels-offres.show', [$lot->appel_offre_id, $lot->id_lot]) }}"
                        class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-folder-open text-orange-500 mr-2"></i>
                            Documents du Lot {{ $lot->numero }}
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">{{ $lot->libelle }}</p>
                    </div>
                </div>
                @can('documents_lots.create')
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleUploadZone()"
                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-plus mr-2"></i>Ajouter un document
                    </button>
                </div>
                @endcan
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

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Statistiques -->
        @php
            $totalDocs = $documents->total();
            $docsValides = $lot->documents()->where('est_valide_document', true)->count();
            $docsNonValides = $totalDocs - $docsValides;
            $tailleTotale = $lot->documents()->sum('fichier_taille_document');
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Total Documents</span>
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalDocs }}</p>
            </div>

            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Validés</span>
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-green-600">{{ $docsValides }}</p>
            </div>

            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">En attente</span>
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-yellow-600">{{ $docsNonValides }}</p>
            </div>

            <div class="stat-card bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-500 uppercase font-medium">Taille totale</span>
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hdd text-purple-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-purple-600">{{ number_format($tailleTotale, 2) }} <span class="text-sm font-normal">Mo</span></p>
            </div>
        </div>

        @can('documents_lots.create')
        <!-- Zone d'upload (caché par défaut) -->
        <div id="uploadZone" class="hidden mb-6">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-file-upload text-orange-500 mr-2"></i>Ajouter un document
                    </h2>
                    <button type="button" onclick="toggleUploadZone()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="p-6">
                    <form action="{{ route('lots.documents.store', $lot->id_lot) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Type de document -->
                            <div>
                                <label for="type_document" class="block text-sm font-medium text-gray-700 mb-2">
                                    Type de document <span class="text-red-500">*</span>
                                </label>
                                <select name="type_document" id="type_document" required
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($typesDocuments as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Titre -->
                            <div>
                                <label for="titre_document" class="block text-sm font-medium text-gray-700 mb-2">
                                    Titre du document <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="titre_document" id="titre_document" required
                                       maxlength="100"
                                       placeholder="Ex: Cahier des charges v1"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <!-- Date du document -->
                            <div>
                                <label for="date_document" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date du document
                                </label>
                                <input type="date" name="date_document" id="date_document"
                                       value="{{ date('Y-m-d') }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description_document" class="block text-sm font-medium text-gray-700 mb-2">
                                    Description
                                </label>
                                <input type="text" name="description_document" id="description_document"
                                       maxlength="120"
                                       placeholder="Brève description du document"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>

                        <!-- Zone de drop -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Fichier <span class="text-red-500">*</span>
                            </label>
                            <div class="drop-zone" id="dropZone" onclick="document.getElementById('fichier').click()">
                                <input type="file" name="fichier" id="fichier" required
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                                       class="hidden" onchange="handleFileSelect(this)">
                                <div id="dropZoneContent">
                                    <i class="fas fa-cloud-upload-alt text-5xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-600 mb-2">Glissez-déposez votre fichier ici</p>
                                    <p class="text-sm text-orange-500 font-medium">ou cliquez pour parcourir</p>
                                    <p class="text-xs text-gray-400 mt-3">Formats: PDF, Word, Excel, PowerPoint, Images, ZIP (Max: 10 Mo)</p>
                                </div>
                                <div id="filePreview" class="hidden">
                                    <div class="flex items-center justify-center gap-4">
                                        <div class="file-icon file-icon-default">
                                            <i class="fas fa-file text-white text-xl" id="fileIcon"></i>
                                        </div>
                                        <div class="text-left">
                                            <p class="font-medium text-gray-800" id="fileName"></p>
                                            <p class="text-sm text-gray-500" id="fileSize"></p>
                                        </div>
                                        <button type="button" onclick="clearFile(event)" class="ml-4 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="fas fa-times-circle text-xl"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="toggleUploadZone()"
                                    class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                                Annuler
                            </button>
                            <button type="submit" id="submitBtn"
                                    class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors shadow-sm">
                                <i class="fas fa-upload mr-2"></i>Uploader
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endcan


        <!-- Filtres -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                <form action="{{ route('lots.documents.index', $lot->id_lot) }}" method="GET" class="flex flex-wrap items-end gap-4">
                    <!-- Recherche -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Rechercher</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Titre, description..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="min-w-[180px]">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                            <option value="">Tous les types</option>
                            @foreach($typesDocuments as $key => $label)
                                <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Statut -->
                    <div class="min-w-[150px]">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                            <option value="">Tous</option>
                            <option value="valide" {{ request('statut') === 'valide' ? 'selected' : '' }}>Validés</option>
                            <option value="non_valide" {{ request('statut') === 'non_valide' ? 'selected' : '' }}>Non validés</option>
                        </select>
                    </div>

                    <!-- Boutons -->
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors text-sm">
                            <i class="fas fa-filter mr-1"></i>Filtrer
                        </button>
                        <a href="{{ route('lots.documents.index', $lot->id_lot) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors text-sm">
                            <i class="fas fa-times mr-1"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>


        <!-- Liste des documents -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-list text-blue-500 mr-2"></i>
                    Liste des documents
                    <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">{{ $documents->total() }}</span>
                </h2>
            </div>

            @if($documents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Document</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Taille</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Version</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Statut</th>
                                @canany(['documents_lots.download', 'documents_lots.read', 'documents_lots.toggle-status', 'documents_lots.update', 'documents_lots.delete'])
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($documents as $document)
                                @php
                                    $extension = strtolower(pathinfo($document->fichier_nom_document, PATHINFO_EXTENSION));
                                    $iconClass = match(true) {
                                        in_array($extension, ['pdf']) => 'file-icon-pdf fa-file-pdf',
                                        in_array($extension, ['doc', 'docx']) => 'file-icon-doc fa-file-word',
                                        in_array($extension, ['xls', 'xlsx']) => 'file-icon-xls fa-file-excel',
                                        in_array($extension, ['ppt', 'pptx']) => 'file-icon-ppt fa-file-powerpoint',
                                        in_array($extension, ['jpg', 'jpeg', 'png', 'gif']) => 'file-icon-img fa-file-image',
                                        in_array($extension, ['zip', 'rar']) => 'file-icon-zip fa-file-archive',
                                        default => 'file-icon-default fa-file',
                                    };
                                    $bgClass = explode(' ', $iconClass)[0];
                                    $faClass = explode(' ', $iconClass)[1];
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="file-icon {{ $bgClass }} mr-3 flex-shrink-0">
                                                <i class="fas {{ $faClass }} text-white"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-800 truncate max-w-xs" title="{{ $document->titre_document }}">
                                                    {{ $document->titre_document }}
                                                </p>
                                                <p class="text-xs text-gray-500 truncate max-w-xs" title="{{ $document->fichier_nom_document }}">
                                                    {{ $document->fichier_nom_document }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                            {{ $typesDocuments[$document->type_document] ?? $document->type_document }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">{{ number_format($document->fichier_taille_document, 2) }} Mo</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">
                                            {{ $document->date_document ? $document->date_document->format('d/m/Y') : '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs font-bold bg-purple-100 text-purple-800 rounded-full">
                                            v{{ $document->version_document ?? 1 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($document->est_valide_document)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                <i class="fas fa-check-circle mr-1"></i>Validé
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                <i class="fas fa-clock mr-1"></i>En attente
                                            </span>
                                        @endif
                                    </td>
                                    @canany(['documents_lots.download', 'documents_lots.read', 'documents_lots.toggle-status', 'documents_lots.update', 'documents_lots.delete'])
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                @can('documents_lots.download')
                                                    <!-- Télécharger -->
                                                    <a href="{{ route('lots.documents.download', [$lot->id_lot, $document->id_document]) }}"
                                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Télécharger">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endcan

                                                @can('documents_lots.read')
                                                    <!-- Prévisualiser -->
                                                    @if(in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'gif']))
                                                        <a href="{{ route('lots.documents.preview', [$lot->id_lot, $document->id_document]) }}"
                                                        target="_blank"
                                                        class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="Prévisualiser">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                @endcan

                                                @can('documents_lots.toggle-status')
                                                    <!-- Valider / Invalider -->
                                                    @if($document->est_valide_document)
                                                        <form action="{{ route('lots.documents.invalider', [$lot->id_lot, $document->id_document]) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Annuler validation">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('lots.documents.valider', [$lot->id_lot, $document->id_document]) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Valider">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan

                                                @can('documents_lots.update')
                                                    <!-- Modifier -->
                                                    <a href="{{ route('lots.documents.edit', [$lot->id_lot, $document->id_document]) }}"
                                                    class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('documents_lots.delete')
                                                    <!-- Supprimer -->
                                                    <form action="{{ route('lots.documents.destroy', [$lot->id_lot, $document->id_document]) }}"
                                                        method="POST" class="inline"
                                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($documents->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $documents->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg mb-2">Aucun document trouvé</p>
                    @can('documents_lots.create')
                        <p class="text-gray-400 text-sm mb-4">Commencez par ajouter un document à ce lot</p>
                        <button type="button" onclick="toggleUploadZone()"
                                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Ajouter un document
                        </button>
                    @endcan
                </div>
            @endif
        </div>

    </main>
@endsection

@canany(['documents_lots.download', 'documents_lots.read', 'documents_lots.toggle-status', 'documents_lots.update', 'documents_lots.delete'])
    @push('scripts')
    <script>
        // Toggle zone d'upload
        function toggleUploadZone() {
            const zone = document.getElementById('uploadZone');
            zone.classList.toggle('hidden');

            if (!zone.classList.contains('hidden')) {
                zone.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // Gestion du drag & drop
        const dropZone = document.getElementById('dropZone');

        if (dropZone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.add('drag-over');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.remove('drag-over');
                }, false);
            });

            dropZone.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    document.getElementById('fichier').files = files;
                    handleFileSelect(document.getElementById('fichier'));
                }
            }, false);
        }

        // Gestion de la sélection de fichier
        function handleFileSelect(input) {
            const file = input.files[0];
            if (file) {
                const dropZoneContent = document.getElementById('dropZoneContent');
                const filePreview = document.getElementById('filePreview');
                const fileName = document.getElementById('fileName');
                const fileSize = document.getElementById('fileSize');
                const fileIcon = document.getElementById('fileIcon');

                // Afficher l'aperçu
                dropZoneContent.classList.add('hidden');
                filePreview.classList.remove('hidden');

                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);

                // Icône selon le type
                const extension = file.name.split('.').pop().toLowerCase();
                const iconClass = getIconClass(extension);
                fileIcon.className = 'fas ' + iconClass + ' text-white text-xl';

                // Auto-remplir le titre si vide
                const titreInput = document.getElementById('titre_document');
                if (!titreInput.value) {
                    titreInput.value = file.name.replace(/\.[^/.]+$/, '');
                }
            }
        }

        // Effacer le fichier sélectionné
        function clearFile(event) {
            event.stopPropagation();

            const fileInput = document.getElementById('fichier');
            const dropZoneContent = document.getElementById('dropZoneContent');
            const filePreview = document.getElementById('filePreview');

            fileInput.value = '';
            dropZoneContent.classList.remove('hidden');
            filePreview.classList.add('hidden');
        }

        // Formater la taille du fichier
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'Ko', 'Mo', 'Go'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Obtenir la classe d'icône selon l'extension
        function getIconClass(extension) {
            const iconMap = {
                'pdf': 'fa-file-pdf',
                'doc': 'fa-file-word',
                'docx': 'fa-file-word',
                'xls': 'fa-file-excel',
                'xlsx': 'fa-file-excel',
                'ppt': 'fa-file-powerpoint',
                'pptx': 'fa-file-powerpoint',
                'jpg': 'fa-file-image',
                'jpeg': 'fa-file-image',
                'png': 'fa-file-image',
                'gif': 'fa-file-image',
                'zip': 'fa-file-archive',
                'rar': 'fa-file-archive',
            };
            return iconMap[extension] || 'fa-file';
        }

        // Validation du formulaire
        document.getElementById('uploadForm')?.addEventListener('submit', function(e) {
            const fileInput = document.getElementById('fichier');
            const submitBtn = document.getElementById('submitBtn');

            if (!fileInput.files.length) {
                e.preventDefault();
                alert('Veuillez sélectionner un fichier');
                return;
            }

            // Désactiver le bouton pendant l'upload
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Upload en cours...';
        });
    </script>
    @endpush
@endcanany
