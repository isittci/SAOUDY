@extends('layouts.main')
@section('title', $user->nom_complet)
@section('breadcrumb')
    <a @can('users.read') href="{{ route('admin.users.index') }}" @endcan
        class="text-white/80 hover:text-white transition-colors">Utilisateurs</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">{{ $user->nom_complet }}</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    @can('users.read')
                        <a href="{{ route('admin.users.index') }}"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan
                    <div
                        class="flex-shrink-0 h-14 w-14 bg-{{ $user->role->level_color ?? 'gray' }}-500 rounded-xl flex items-center justify-center">
                        <span class="text-xl font-bold text-white">{{ $user->initials }}</span>
                    </div>
                    <div>
                        <div class="flex items-center space-x-3 flex-wrap gap-2">
                            <h1 class="text-2xl font-bold text-gray-800">{{ $user->nom_complet }}</h1>
                            @if ($user->statut === \App\Models\User::STATUT_ACTIF)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Actif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i> Inactif
                                </span>
                            @endif
                        </div>
                        <p class="text-gray-600 mt-1">{{ $user->email }}</p>
                    </div>
                </div>

                @canany(['users.toggle-status', 'users.update', 'users.delete'])
                    <!-- Actions -->
                    <div class="flex items-center space-x-2 flex-wrap gap-2">

                        @if (auth()->user()->canManageUser($user))
                            @can('users.update')
                                <a href="{{ route('admin.users.edit', $user) }}"
                                    class="px-4 py-2.5 bg-white border border-amber-300 text-amber-600 hover:bg-amber-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                    <i class="fas fa-edit text-sm"></i>
                                    <span class="text-sm font-medium">Modifier</span>
                                </a>
                            @endcan

                            @if ($user->id !== auth()->id())
                                @can('users.toggle-status')
                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                        @csrf

                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-4 py-2.5 bg-white border border-{{ $user->statut ? 'red' : 'green' }}-300 text-{{ $user->statut ? 'red' : 'green' }}-600 hover:bg-{{ $user->statut ? 'red' : 'green' }}-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                            <i class="fas fa-{{ $user->statut ? 'ban' : 'check' }} text-sm"></i>
                                            <span class="text-sm font-medium">{{ $user->statut ? 'Désactiver' : 'Activer' }}</span>
                                        </button>
                                    </form>
                                @endcan

                                @can('users.update')
                                    <button onclick="openResetPasswordModal()"
                                        class="px-4 py-2.5 bg-white border border-blue-300 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                        <i class="fas fa-key text-sm"></i>
                                        <span class="text-sm font-medium">Réinitialiser MDP</span>
                                    </button>
                                @endcan

                                @can('users.delete')
                                    @if ($user->canBeDeleted())
                                        <button onclick="openDeleteModal()"
                                            class="px-4 py-2.5 bg-white border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-sm">
                                            <i class="fas fa-trash text-sm"></i>
                                            <span class="text-sm font-medium">Supprimer</span>
                                        </button>
                                    @endif
                                @endcan
                            @endif
                        @endif
                    </div>
                @endcanany
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations personnelles -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-user text-orange-500 mr-2"></i>
                            Informations personnelles
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $user->nom_complet }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900 flex items-center">
                                    {{ $user->email }}
                                    @if ($user->email_verified_at)
                                        <span class="ml-2 text-green-500"
                                            title="Vérifié le {{ $user->email_verified_at->format('d/m/Y') }}">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    @else
                                        <span class="ml-2 text-amber-500" title="Non vérifié">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Téléphone principal</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->telephone_principal ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Téléphone secondaire</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->telephone_secondaire ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Rôle</dt>
                                <dd class="mt-1">
                                    @if ($user->role)
                                        <a @can('roles.view-details') href="{{ route('admin.roles.show', $user->role) }}" @endcan
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $user->role->level_color }}-100 text-{{ $user->role->level_color }}-800 hover:bg-{{ $user->role->level_color }}-200 transition-colors">
                                            <i class="fas fa-user-shield mr-1"></i>
                                            {{ $user->role->name }} (Niv. {{ $user->role->level }})
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">Aucun rôle</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Statut</dt>
                                <dd class="mt-1">
                                    @if ($user->statut === \App\Models\User::STATUT_ACTIF)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Actif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Inactif
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Permissions héritées -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-key text-purple-500 mr-2"></i>
                                Permissions (via le rôle)
                            </h3>
                            <span class="text-sm text-gray-500">{{ $user->role ? $user->role->permissions->count() : 0 }}
                                permission(s)</span>
                        </div>
                    </div>
                    <div class="p-6">
                        @if ($user->role && $user->role->permissions->count() > 0)
                            @php
                                $permissionsByCategory = $user->role->permissions->groupBy('category');
                            @endphp
                            <div class="space-y-4">
                                @foreach ($permissionsByCategory as $category => $permissions)
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-folder text-orange-400 mr-2"></i>
                                            {{ \App\Models\Permission::CATEGORIES[$category] ?? $category }}
                                            <span
                                                class="ml-2 text-xs font-normal text-gray-400">({{ count($permissions) }})</span>
                                        </h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($permissions->take(10) as $permission)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 bg-purple-50 text-purple-700 rounded text-xs">
                                                    <i class="fas fa-check text-purple-500 mr-1"></i>
                                                    {{ $permission->name }}
                                                </span>
                                            @endforeach
                                            @if ($permissions->count() > 10)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">
                                                    +{{ $permissions->count() - 10 }} autres
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-key text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500">Aucune permission attribuée</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Métadonnées -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-clock text-gray-500 mr-2"></i>
                            Métadonnées
                        </h3>
                    </div>
                    <div class="p-6 space-y-4 text-sm">
                        <div>
                            <span class="text-gray-500">Enregistré le</span>
                            <p class="text-gray-900 font-medium">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                            @if ($user->creator)
                                <p class="text-xs text-gray-500">par {{ $user->creator->nom_complet }}</p>
                            @endif
                        </div>
                        <div>
                            <span class="text-gray-500">Modifié le</span>
                            <p class="text-gray-900 font-medium">{{ $user->updated_at->format('d/m/Y à H:i') }}</p>
                            @if ($user->updater)
                                <p class="text-xs text-gray-500">par {{ $user->updater->nom_complet }}</p>
                            @endif
                        </div>
                        @if ($user->email_verified_at)
                            <div>
                                <span class="text-gray-500">Email vérifié le</span>
                                <p class="text-gray-900 font-medium">{{ $user->email_verified_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sécurité -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-shield-alt text-blue-500 mr-2"></i>
                            Sécurité
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-gray-400 mr-3"></i>
                                <span class="text-sm text-gray-700">Email vérifié</span>
                            </div>
                            @if ($user->email_verified_at)
                                <span class="text-green-500"><i class="fas fa-check-circle"></i></span>
                            @else
                                <span class="text-gray-400"><i class="fas fa-times-circle"></i></span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-user-shield text-gray-400 mr-3"></i>
                                <span class="text-sm text-gray-700">Niveau hiérarchique</span>
                            </div>
                            <span
                                class="text-sm font-bold text-{{ $user->role->level_color ?? 'gray' }}-600">{{ $user->role->level ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Reset Password -->
    @if (auth()->user()->canManageUser($user) && $user->id !== auth()->id())
        <div id="resetPasswordModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeResetPasswordModal()"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b">
                        <h3 class="text-lg font-bold text-gray-800">
                            <i class="fas fa-key text-blue-500 mr-2"></i>
                            Réinitialiser le mot de passe
                        </h3>
                    </div>
                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="p-6 space-y-4">
                            {{-- <div class="relative">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nouveau mot de passe <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="password" name="password" id="newPassword" required minlength="8"
                                        class="w-full px-4 py-3 pr-12 border rounded-lg focus:ring-2 focus:ring-blue-400"
                                        placeholder="Min. 8 caractères">
                                    <button type="button" onclick="togglePassword('newPassword', 'eyeIcon1')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                        <i id="eyeIcon1" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="confirmPassword" required
                                        class="w-full px-4 py-3 pr-12 border rounded-lg focus:ring-2 focus:ring-blue-400"
                                        placeholder="Confirmer">
                                    <button type="button" onclick="togglePassword('confirmPassword', 'eyeIcon2')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                        <i id="eyeIcon2" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div> --}}
                            <div class="relative">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nouveau mot de passe <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="password" name="password" id="newPassword" required minlength="8"
                                        class="w-full px-4 py-3 pr-12 border rounded-lg focus:ring-2 focus:ring-blue-400"
                                        placeholder="Min. 8 caractères" oninput="checkPasswordMatch()">
                                    <button type="button" onclick="togglePassword('newPassword', 'eyeIcon1')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                        <i id="eyeIcon1" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirmer le mot de passe
                                    <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="confirmPassword" required
                                        class="w-full px-4 py-3 pr-12 border rounded-lg focus:ring-2 focus:ring-blue-400"
                                        placeholder="Confirmer" oninput="checkPasswordMatch()">
                                    <button type="button" onclick="togglePassword('confirmPassword', 'eyeIcon2')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                        <i id="eyeIcon2" class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <!-- Message de validation -->
                                <p id="passwordMatchMessage" class="mt-2 text-sm hidden"></p>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                            <button type="button" onclick="closeResetPasswordModal()"
                                class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">Annuler</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">Réinitialiser</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @can('users.delete')
            <!-- Modal Delete -->
            @if ($user->canBeDeleted())
                <div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeDeleteModal()"></div>
                        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b">
                                <h3 class="text-lg font-bold text-gray-800">
                                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                    Confirmer la suppression
                                </h3>
                            </div>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="p-6">
                                    <p class="text-gray-600">Êtes-vous sûr de vouloir supprimer l'utilisateur <strong
                                            class="text-gray-900">{{ $user->nom_complet }}</strong> ?</p>
                                    <p class="mt-2 text-sm text-gray-500">L'utilisateur sera déplacé dans la corbeille.</p>
                                </div>
                                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                                    <button type="button" onclick="closeDeleteModal()"
                                        class="px-4 py-2 bg-white border text-gray-700 rounded-lg hover:bg-gray-50">Annuler</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">Supprimer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endcan
    @endif

@endsection

@can('users.view-details')
    @push('scripts')
        <script>
            function openResetPasswordModal() {
                document.getElementById('resetPasswordModal').classList.remove('hidden');
            }

            function closeResetPasswordModal() {
                document.getElementById('resetPasswordModal').classList.add('hidden');
            }

            function openDeleteModal() {
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
            }
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeResetPasswordModal();
                    closeDeleteModal();
                }
            });






            function togglePassword(inputId, iconId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }

            function checkPasswordMatch() {
                const password = document.getElementById('newPassword');
                const confirmPassword = document.getElementById('confirmPassword');
                const message = document.getElementById('passwordMatchMessage');
                const submitBtn = document.getElementById('resetPasswordBtn');

                // Si le champ de confirmation est vide, masquer le message
                if (confirmPassword.value === '') {
                    message.classList.add('hidden');
                    confirmPassword.classList.remove('border-green-500', 'border-red-500');
                    confirmPassword.classList.add('border-gray-300');
                    return;
                }

                message.classList.remove('hidden');

                if (password.value === confirmPassword.value) {
                    // Mots de passe identiques
                    message.textContent = '✓ Les mots de passe correspondent';
                    message.classList.remove('text-red-500');
                    message.classList.add('text-green-500');
                    confirmPassword.classList.remove('border-red-500');
                    confirmPassword.classList.add('border-green-500');
                    if (submitBtn) submitBtn.disabled = false;
                } else {
                    // Mots de passe différents
                    message.textContent = '✗ Les mots de passe ne correspondent pas';
                    message.classList.remove('text-green-500');
                    message.classList.add('text-red-500');
                    confirmPassword.classList.remove('border-green-500');
                    confirmPassword.classList.add('border-red-500');
                    if (submitBtn) submitBtn.disabled = true;
                }
            }

            // Validation avant soumission du formulaire
            document.querySelector('#resetPasswordModal form').addEventListener('submit', function(e) {
                const password = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Les mots de passe ne correspondent pas');
                    return false;
                }

                if (password.length < 8) {
                    e.preventDefault();
                    alert('Le mot de passe doit contenir au moins 8 caractères');
                    return false;
                }
            });
        </script>
        <style>
            .animate-fadeIn {
                animation: fadeIn 0.3s ease-out;
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
        </style>
    @endpush
@endcan
