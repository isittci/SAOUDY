@extends('layouts.main')
@section('title', 'Permissions - ' . $role->name)
@section('breadcrumb')
    <a @can('roles.read') href="{{ route('admin.roles.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Rôles</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <a @can('roles.view-details') href="{{ route('admin.roles.show', $role) }}" @endcan class="text-white/80 hover:text-white transition-colors">{{ $role->name }}</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Permissions</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    @can('roles.view-details')
                        <a href="{{ route('admin.roles.show', $role) }}" class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan

                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-{{ $role->level_color }}-100 rounded-full">
                            <i class="fas fa-user-shield text-{{ $role->level_color }}-600"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Permissions du rôle</h1>
                            <p class="text-sm text-gray-600">{{ $role->name }} - Niveau {{ $role->level }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button type="button" onclick="selectAll()" class="px-4 py-2.5 bg-white border border-green-300 text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 flex items-center space-x-2">
                        <i class="fas fa-check-double text-sm"></i>
                        <span class="text-sm font-medium">Tout sélectionner</span>
                    </button>
                    <button type="button" onclick="deselectAll()" class="px-4 py-2.5 bg-white border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 flex items-center space-x-2">
                        <i class="fas fa-times text-sm"></i>
                        <span class="text-sm font-medium">Tout désélectionner</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

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

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Permissions attribuées</p>
                        <p class="text-3xl font-bold text-purple-600" id="selectedCount">{{ count($rolePermissions) }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-check-circle text-purple-500 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Total permissions</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $permissionsByCategory->flatten()->count() }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-key text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Catégories</p>
                        <p class="text-3xl font-bold text-green-600">{{ $permissionsByCategory->count() }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-layer-group text-green-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        @can('roles.manage')
            <!-- Formulaire des permissions -->
            <form id="permissionsForm" action="{{ route('admin.roles.permissions.update', $role) }}" method="POST">
                @csrf
                @method('POST')

                <div class="space-y-6">
                    @foreach($permissionsByCategory as $category => $categoryPermissions)
                        @php
                            $modulesByCategory = $categoryPermissions->groupBy('module');
                        @endphp

                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <!-- Header de la catégorie -->
                            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-folder text-orange-500 text-lg"></i>
                                        <h3 class="text-lg font-semibold text-gray-800">{{ $category }}</h3>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $categoryPermissions->count() }} permissions
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button type="button" onclick="toggleCategory('{{ Str::slug($category) }}')" class="px-3 py-1.5 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-all">
                                            <i class="fas fa-check-double mr-1"></i> Tout sélectionner
                                        </button>
                                        <button type="button" onclick="collapseCategory('{{ Str::slug($category) }}')" class="p-2 hover:bg-gray-100 rounded-lg transition-all category-toggle">
                                            <i class="fas fa-chevron-down text-gray-600"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenu de la catégorie -->
                            <div id="category-{{ Str::slug($category) }}" class="category-content">
                                @foreach($modulesByCategory as $module => $modulePermissions)
                                    <div class="border-b border-gray-100 last:border-b-0">
                                        <div class="px-6 py-3 bg-gray-50">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-sm font-semibold text-gray-700">
                                                    <i class="fas fa-cube text-gray-400 mr-2"></i>
                                                    {{ $module }}
                                                </h4>
                                                <span class="text-xs text-gray-500">{{ $modulePermissions->count() }} permissions</span>
                                            </div>
                                        </div>

                                        <div class="p-6">
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                @foreach($modulePermissions as $permission)
                                                    <label class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition-all group border border-transparent hover:border-{{ $permission->action_color }}-200">
                                                        <input type="checkbox"
                                                            name="permissions[]"
                                                            value="{{ $permission->id }}"
                                                            {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                                            data-category="{{ Str::slug($category) }}"
                                                            class="w-5 h-5 text-{{ $permission->action_color }}-600 border-gray-300 rounded focus:ring-{{ $permission->action_color }}-500 mt-0.5 permission-checkbox"
                                                            onchange="updateCount()">
                                                        <div class="flex-1">
                                                            <div class="flex items-center space-x-2">
                                                                <i class="fas {{ $permission->action_icon }} text-{{ $permission->action_color }}-500 text-sm"></i>
                                                                <span class="text-sm font-medium text-gray-900 group-hover:text-{{ $permission->action_color }}-600">
                                                                    {{ $permission->name }}
                                                                </span>
                                                            </div>
                                                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ Str::limit($permission->description, 100) }}</p>
                                                            <div class="flex items-center space-x-2 mt-2">
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $permission->action_color }}-100 text-{{ $permission->action_color }}-800">
                                                                    {{ $permission->action_label }}
                                                                </span>
                                                                @if($permission->requires_confirmation)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                                                    <i class="fas fa-exclamation-triangle text-xs mr-1"></i> Confirmation
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Actions Footer -->
                <div class="sticky bottom-0 mt-6 bg-white rounded-2xl shadow-lg p-6 flex items-center justify-between border-t-4 border-purple-500">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                <span id="selectedCountText">{{ count($rolePermissions) }}</span> permission(s) sélectionnée(s)
                            </p>
                            <p class="text-xs text-gray-500">Les modifications seront appliquées immédiatement</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.roles.show', $role) }}" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 flex items-center space-x-2">
                            <i class="fas fa-times text-sm"></i>
                            <span class="font-medium">Annuler</span>
                        </a>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md">
                            <i class="fas fa-save text-sm"></i>
                            <span class="font-medium">Enregistrer les permissions</span>
                        </button>
                    </div>
                </div>
            </form>
        @endcan
    </main>
@endsection

@can('roles.manage')
    @push('scripts')
    <script>
        // Mettre à jour le compteur
        function updateCount() {
            const checkedCount = document.querySelectorAll('.permission-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = checkedCount;
            document.getElementById('selectedCountText').textContent = checkedCount;
        }

        // Tout sélectionner
        function selectAll() {
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = true;
            });
            updateCount();
        }

        // Tout désélectionner
        function deselectAll() {
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateCount();
        }

        // Toggle catégorie
        function toggleCategory(categorySlug) {
            const checkboxes = document.querySelectorAll(`[data-category="${categorySlug}"]`);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);

            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });

            updateCount();
        }

        // Collapse/Expand catégorie
        function collapseCategory(categorySlug) {
            const content = document.getElementById(`category-${categorySlug}`);
            const icon = event.currentTarget.querySelector('i');

            if (content.style.display === 'none') {
                content.style.display = 'block';
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-down');
            } else {
                content.style.display = 'none';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-right');
            }
        }

        // Confirmation avant soumission
        document.getElementById('permissionsForm').addEventListener('submit', function(e) {
            const checkedCount = document.querySelectorAll('.permission-checkbox:checked').length;

            if (checkedCount === 0) {
                e.preventDefault();
                if (!confirm('Attention : Vous êtes sur le point de retirer TOUTES les permissions de ce rôle. Êtes-vous sûr ?')) {
                    return false;
                }
            }
        });

        // Recherche de permissions (optionnel)
        function searchPermissions(query) {
            const permissions = document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                const label = checkbox.closest('label');
                const text = label.textContent.toLowerCase();

                if (text.includes(query.toLowerCase())) {
                    label.style.display = 'flex';
                } else {
                    label.style.display = 'none';
                }
            });
        }
    </script>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }

        .category-content {
            transition: all 0.3s ease;
        }

        .permission-checkbox:checked + div {
            background-color: rgba(139, 92, 246, 0.05);
        }
    </style>
    @endpush
@endcan
