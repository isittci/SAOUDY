@extends('layouts.main')

@section('title', 'Ajouter un document - Lot ' . $lot->numero)

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
    }
    .drop-zone {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 3rem;
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
    .file-icon {
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        margin: 0 auto;
    }
    .file-icon-pdf { background: linear-gradient(135deg, #ff6b6b, #ee5a5a); }
    .file-icon-doc { background: linear-gradient(135deg, #4facfe, #00f2fe); }
    .file-icon-xls { background: linear-gradient(135deg, #11998e, #38ef7d); }
    .file-icon-ppt { background: linear-gradient(135deg, #f093fb, #f5576c); }
    .file-icon-img { background: linear-gradient(135deg, #667eea, #764ba2); }
    .file-icon-zip { background: linear-gradient(135deg, #ffa751, #ffe259); }
    .file-icon-default { background: linear-gradient(135deg, #a8edea, #fed6e3); }

    .gradient-orange { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }
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
    <span class="text-white font-medium">Ajouter</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center gap-3">
                @can('documents_lots.read')
                <a href="{{ route('lots.documents.index', $lot->id_lot) }}"
                   class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                @endcan

                <div>
                    <h1 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-file-upload text-orange-500 mr-2"></i>
                        Ajouter un document
                    </h1>
                    <p class="text-gray-600 text-sm mt-1">Lot {{ $lot->numero }} - {{ $lot->libelle }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages d'erreur -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle mr-3 mt-0.5 text-red-500"></i>
                    <div>
                        <p class="font-medium">Veuillez corriger les erreurs suivantes :</p>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formulaire -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-plus-circle text-orange-500 mr-2"></i>
                    Informations du document
                </h2>
            </div>

            @can('documents_lots.create')
            <form action="{{ route('lots.documents.store', $lot->id_lot) }}" method="POST" enctype="multipart/form-data" id="uploadForm" class="p-6">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Colonne gauche - Informations -->
                    <div class="space-y-6">
                        <!-- Type de document -->
                        <div>
                            <label for="type_document" class="block text-sm font-medium text-gray-700 mb-2">
                                Type de document <span class="text-red-500">*</span>
                            </label>
                            <select name="type_document" id="type_document" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors @error('type_document') border-red-500 @enderror">
                                <option value="">-- Sélectionner un type --</option>
                                @foreach($typesDocuments as $key => $label)
                                    <option value="{{ $key }}" {{ old('type_document') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type_document')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Titre -->
                        <div>
                            <label for="titre_document" class="block text-sm font-medium text-gray-700 mb-2">
                                Titre du document <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="titre_document" id="titre_document" required
                                   maxlength="100"
                                   value="{{ old('titre_document') }}"
                                   placeholder="Ex: Cahier des charges technique"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors @error('titre_document') border-red-500 @enderror">
                            @error('titre_document')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date du document -->
                        <div>
                            <label for="date_document" class="block text-sm font-medium text-gray-700 mb-2">
                                Date du document
                            </label>
                            <input type="date" name="date_document" id="date_document"
                                   value="{{ old('date_document', date('Y-m-d')) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description_document" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea name="description_document" id="description_document"
                                      rows="3"
                                      maxlength="120"
                                      placeholder="Brève description du contenu du document..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none">{{ old('description_document') }}</textarea>
                            <p class="mt-1 text-xs text-gray-400">Maximum 120 caractères</p>
                        </div>
                    </div>

                    <!-- Colonne droite - Upload fichier -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Fichier <span class="text-red-500">*</span>
                        </label>
                        <div class="drop-zone @error('fichier') border-red-500 @enderror" id="dropZone" onclick="document.getElementById('fichier').click()">
                            <input type="file" name="fichier" id="fichier" required
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                                   class="hidden" onchange="handleFileSelect(this)">

                            <div id="dropZoneContent">
                                <div class="file-icon file-icon-default mb-4">
                                    <i class="fas fa-cloud-upload-alt text-white text-2xl"></i>
                                </div>
                                <p class="text-gray-700 font-medium mb-2">Glissez-déposez votre fichier ici</p>
                                <p class="text-orange-500 font-medium mb-4">ou cliquez pour parcourir</p>
                                <div class="bg-gray-100 rounded-lg p-3 inline-block">
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Formats: PDF, Word, Excel, PowerPoint, Images, ZIP
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-weight-hanging mr-1"></i>
                                        Taille max: {{ $tailleMaxMo }} Mo
                                    </p>
                                </div>
                            </div>

                            <div id="filePreview" class="hidden">
                                <div class="file-icon mb-4" id="previewIcon">
                                    <i class="fas fa-file text-white text-2xl" id="fileIcon"></i>
                                </div>
                                <p class="font-medium text-gray-800 mb-1" id="fileName"></p>
                                <p class="text-sm text-gray-500 mb-4" id="fileSize"></p>
                                <button type="button" onclick="clearFile(event)"
                                        class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-600 font-medium rounded-lg transition-colors">
                                    <i class="fas fa-times mr-2"></i>Retirer le fichier
                                </button>
                            </div>
                        </div>
                        @error('fichier')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror

                        <!-- Aide -->
                        <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-200">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">
                                <i class="fas fa-lightbulb mr-1"></i>Conseil
                            </h4>
                            <p class="text-xs text-blue-700">
                                Donnez un titre explicite à votre document pour le retrouver facilement.
                                La version sera automatiquement incrémentée si un document avec le même titre existe déjà.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-end gap-3">
                    <a href="{{ route('lots.documents.index', $lot->id_lot) }}"
                       class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl transition-colors text-center">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </a>
                    <button type="submit" id="submitBtn"
                            class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-xl transition-colors shadow-sm">
                        <i class="fas fa-upload mr-2"></i>Ajouter le document
                    </button>
                </div>
            </form>
            @endcan
        </div>

    </main>
@endsection

@can('documents_lots.create')
@push('scripts')
<script>
    // Gestion du drag & drop
    const dropZone = document.getElementById('dropZone');

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

    // Gestion de la sélection de fichier
    function handleFileSelect(input) {
        const file = input.files[0];
        if (file) {
            const dropZoneContent = document.getElementById('dropZoneContent');
            const filePreview = document.getElementById('filePreview');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            const previewIcon = document.getElementById('previewIcon');
            const fileIcon = document.getElementById('fileIcon');

            // Afficher l'aperçu
            dropZoneContent.classList.add('hidden');
            filePreview.classList.remove('hidden');

            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);

            // Icône et couleur selon le type
            const extension = file.name.split('.').pop().toLowerCase();
            const { iconClass, bgClass } = getIconInfo(extension);

            fileIcon.className = 'fas ' + iconClass + ' text-white text-2xl';
            previewIcon.className = 'file-icon mb-4 ' + bgClass;

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

    // Obtenir les infos d'icône selon l'extension
    function getIconInfo(extension) {
        const iconMap = {
            'pdf': { iconClass: 'fa-file-pdf', bgClass: 'file-icon-pdf' },
            'doc': { iconClass: 'fa-file-word', bgClass: 'file-icon-doc' },
            'docx': { iconClass: 'fa-file-word', bgClass: 'file-icon-doc' },
            'xls': { iconClass: 'fa-file-excel', bgClass: 'file-icon-xls' },
            'xlsx': { iconClass: 'fa-file-excel', bgClass: 'file-icon-xls' },
            'ppt': { iconClass: 'fa-file-powerpoint', bgClass: 'file-icon-ppt' },
            'pptx': { iconClass: 'fa-file-powerpoint', bgClass: 'file-icon-ppt' },
            'jpg': { iconClass: 'fa-file-image', bgClass: 'file-icon-img' },
            'jpeg': { iconClass: 'fa-file-image', bgClass: 'file-icon-img' },
            'png': { iconClass: 'fa-file-image', bgClass: 'file-icon-img' },
            'gif': { iconClass: 'fa-file-image', bgClass: 'file-icon-img' },
            'zip': { iconClass: 'fa-file-archive', bgClass: 'file-icon-zip' },
            'rar': { iconClass: 'fa-file-archive', bgClass: 'file-icon-zip' },
        };
        return iconMap[extension] || { iconClass: 'fa-file', bgClass: 'file-icon-default' };
    }

    // Validation du formulaire
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
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
@endcan
