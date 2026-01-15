@extends('layouts.main')
@section('title', 'Créer un Rôle')
@section('breadcrumb')
    <a @can('roles.read') href="{{ route('admin.roles.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Rôles</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Nouveau rôle</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    @can('roles.read')
                        <a href="{{ route('admin.roles.index') }}"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                            <i class="fas fa-plus-circle text-purple-500"></i>
                            <span>Créer un nouveau rôle</span>
                        </h1>
                        <p class="text-gray-600 mt-1">Définissez les informations et permissions du rôle</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages d'erreur -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fadeIn">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div>
                        <p class="text-red-700 font-medium">Veuillez corriger les erreurs suivantes :</p>
                        <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @can('roles.create')
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Colonne principale -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Informations générales -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-info-circle text-purple-500 mr-2"></i>
                                    Informations générales
                                </h2>
                            </div>

                            <div class="p-6 space-y-5">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nom du rôle <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent @error('name') border-red-500 @enderror"
                                        placeholder="Ex: Superviseur, Gestionnaire...">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                        Slug <span class="text-gray-400 text-xs">(généré automatiquement si vide)</span>
                                    </label>
                                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent @error('slug') border-red-500 @enderror"
                                        placeholder="Ex: superviseur">
                                    @error('slug')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Description
                                    </label>
                                    <textarea name="description" id="description" rows="4"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent @error('description') border-red-500 @enderror"
                                        placeholder="Décrivez les responsabilités et privilèges de ce rôle...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
                                        Niveau hiérarchique <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex items-center space-x-4">
                                        <input type="number" name="level" id="level" value="{{ old('level', 50) }}"
                                            required min="1" max="99"
                                            class="w-32 px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent @error('level') border-red-500 @enderror">
                                        <span class="text-sm text-gray-500">De 1 à 99 (100 réservé au Super Admin)</span>
                                    </div>
                                    @error('level')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-xs text-gray-500">
                                        <strong>Guide:</strong> 1-20 (Utilisateur), 21-40 (Opérateur), 41-60 (Manager), 61-79
                                        (Superviseur), 80-99 (Admin)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Permissions initiales (optionnel) -->
                        @if (isset($permissions) && $permissions->count() > 0)
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                            <i class="fas fa-key text-blue-500 mr-2"></i>
                                            Permissions initiales
                                            <span class="ml-2 text-xs text-gray-500 font-normal">(optionnel)</span>
                                        </h2>
                                        <div class="flex items-center space-x-2">
                                            <button type="button" onclick="selectAllPermissions()"
                                                class="px-3 py-1.5 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg text-xs font-medium transition-colors">
                                                <i class="fas fa-check-double mr-1"></i>Tout sélectionner
                                            </button>
                                            <button type="button" onclick="deselectAllPermissions()"
                                                class="px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-xs font-medium transition-colors">
                                                <i class="fas fa-times mr-1"></i>Tout désélectionner
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6">
                                    @php
                                        // GESTION UNIVERSELLE : Supporter toutes les structures de données
                                        // Si $permissions est déjà groupé (collection de collections), on l'aplatit
    $flatPermissions = $permissions;

    // Détecter si c'est déjà groupé (le premier élément est une collection)
                                        if ($permissions->first() instanceof \Illuminate\Support\Collection) {
                                            // Aplatir la collection groupée
                                            $flatPermissions = $permissions->flatten(1);
                                        }

                                        // Maintenant on groupe par catégorie sur la collection plate
                                        $groupedPermissions = $flatPermissions->groupBy('category');
                                    @endphp

                                    <div class="space-y-4 max-h-96 overflow-y-auto">
                                        @foreach ($groupedPermissions as $category => $categoryPermissions)
                                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                                <div class="px-4 py-3 bg-gray-50 flex items-center justify-between">
                                                    <h4 class="font-semibold text-gray-700 flex items-center">
                                                        <i class="fas fa-folder text-purple-500 mr-2"></i>
                                                        {{ $category ?? 'Non catégorisé' }}
                                                    </h4>
                                                    <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">
                                                        {{ $categoryPermissions->count() }} permissions
                                                    </span>
                                                </div>
                                                <div class="p-4 bg-white">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                                        @foreach ($categoryPermissions as $permission)
                                                            <label
                                                                class="flex items-start p-2 rounded hover:bg-gray-50 cursor-pointer group border border-transparent hover:border-{{ $permission->action_color ?? 'purple' }}-200">
                                                                <input type="checkbox" name="permissions[]"
                                                                    value="{{ $permission->id }}"
                                                                    class="permission-checkbox w-4 h-4 text-{{ $permission->action_color ?? 'purple' }}-500 border-gray-300 rounded focus:ring-{{ $permission->action_color ?? 'purple' }}-500 mt-0.5"
                                                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                                <div class="ml-2 flex-1">
                                                                    <span
                                                                        class="text-sm text-gray-700 group-hover:text-{{ $permission->action_color ?? 'purple' }}-700">
                                                                        {{ $permission->name }}
                                                                    </span>
                                                                    @if (isset($permission->action_label))
                                                                        <div class="mt-1">
                                                                            <span
                                                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-{{ $permission->action_color ?? 'purple' }}-100 text-{{ $permission->action_color ?? 'purple' }}-800">
                                                                                @if (isset($permission->action_icon))
                                                                                    <i
                                                                                        class="fas {{ $permission->action_icon }} text-xs mr-1"></i>
                                                                                @endif
                                                                                {{ $permission->action_label }}
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Colonne latérale -->
                    <div class="space-y-6">
                        <!-- Actions -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="p-6 space-y-3">
                                @can('roles.create')
                                    <button type="submit"
                                        class="w-full px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-lg transition-all duration-200 font-medium shadow-md flex items-center justify-center">
                                        <i class="fas fa-save mr-2"></i>
                                        Créer le rôle
                                    </button>
                                @endcan

                                @can('roles.read')
                                    <a href="{{ route('admin.roles.index') }}"
                                        class="w-full px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium flex items-center justify-center">
                                        <i class="fas fa-times mr-2"></i>
                                        Annuler
                                    </a>
                                @endcan
                            </div>
                        </div>

                        <!-- Informations -->
                        <div class="bg-blue-50 rounded-2xl p-4">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-500 text-lg mr-3 mt-0.5"></i>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium mb-1">💡 Conseil</p>
                                    <p>Les permissions peuvent être modifiées à tout moment après la création du rôle via la
                                        page de gestion des permissions.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Guide des niveaux -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-800">
                                    <i class="fas fa-layer-group text-gray-500 mr-2"></i>
                                    Guide des niveaux
                                </h3>
                            </div>
                            <div class="p-4">
                                <ul class="space-y-2 text-xs">
                                    <li class="flex items-center p-2 bg-gray-50 rounded">
                                        <span class="w-12 font-bold text-gray-600">1-20</span>
                                        <span class="text-gray-700">Utilisateur standard</span>
                                    </li>
                                    <li class="flex items-center p-2 bg-green-50 rounded">
                                        <span class="w-12 font-bold text-green-600">21-40</span>
                                        <span class="text-green-700">Opérateur / Assistant</span>
                                    </li>
                                    <li class="flex items-center p-2 bg-blue-50 rounded">
                                        <span class="w-12 font-bold text-blue-600">41-60</span>
                                        <span class="text-blue-700">Manager / Gestionnaire</span>
                                    </li>
                                    <li class="flex items-center p-2 bg-yellow-50 rounded">
                                        <span class="w-12 font-bold text-yellow-600">61-79</span>
                                        <span class="text-yellow-700">Superviseur</span>
                                    </li>
                                    <li class="flex items-center p-2 bg-orange-50 rounded">
                                        <span class="w-12 font-bold text-orange-600">80-99</span>
                                        <span class="text-orange-700">Administrateur</span>
                                    </li>
                                    <li class="flex items-center p-2 bg-red-50 rounded">
                                        <span class="w-12 font-bold text-red-600">100</span>
                                        <span class="text-red-700">Super Admin (système)</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endcan
    </main>

    @can('users.create')
        @push('scripts')
            <script>
                function selectAllPermissions() {
                    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
                }

                function deselectAllPermissions() {
                    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
                }

                // Auto-générer le slug à partir du nom
                document.getElementById('name').addEventListener('input', function() {
                    const slugField = document.getElementById('slug');
                    if (!slugField.value || slugField.dataset.autoGenerated === 'true') {
                        slugField.value = this.value
                            .toLowerCase()
                            .normalize('NFD')
                            .replace(/[\u0300-\u036f]/g, '')
                            .replace(/[^a-z0-9]+/g, '-')
                            .replace(/(^-|-$)/g, '');
                        slugField.dataset.autoGenerated = 'true';
                    }
                });

                document.getElementById('slug').addEventListener('input', function() {
                    this.dataset.autoGenerated = 'false';
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
            </style>
        @endpush
    @endcan
@endsection
