@extends('layouts.main')
@section('title', 'Mon Profil')
@section('breadcrumb', 'Profil')

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <!-- Avatar -->
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-2xl font-bold text-white">{{ strtoupper(substr($user->nom_complet, 0, 1)) }}</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $user->nom_complet }}</h1>
                        <p class="text-gray-500 text-sm flex items-center mt-1">
                            <i class="fas fa-envelope mr-2"></i>{{ $user->email }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @if($user->role)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-orange-100 text-orange-700">
                            <i class="fas fa-user-tag mr-2"></i>{{ $user->role->name }}
                        </span>
                    @endif
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $user->isActive() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        <i class="fas {{ $user->isActive() ? 'fa-check-circle' : 'fa-times-circle' }} mr-2"></i>
                        {{ $user->isActive() ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        <!-- Messages de succès/erreur -->
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

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <ul class="text-sm text-red-600 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Informations personnelles -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-white">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-user text-orange-500 mr-3"></i>
                            Informations personnelles
                        </h2>
                    </div>
                    <form action="{{ route('profile.update') }}" method="POST" class="p-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nom complet -->
                            <div class="md:col-span-2">
                                <label for="nom_complet" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-id-card text-gray-400 mr-1"></i>
                                    Nom complet <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nom_complet" id="nom_complet"
                                    value="{{ old('nom_complet', $user->nom_complet) }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('nom_complet') border-red-500 @enderror"
                                    placeholder="Votre nom complet">
                                @error('nom_complet')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-envelope text-gray-400 mr-1"></i>
                                    Adresse email <span class="text-red-500">*</span>
                                </label>

                                <input type="email" name="email" id="email" @if(!auth()->user()->isSuperAdmin()) readonly @endif
                                    value="{{ old('email', $user->email) }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                                    placeholder="votre@email.com">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Téléphone principal -->
                            <div>
                                <label for="telephone_principal" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-phone text-gray-400 mr-1"></i>
                                    Téléphone principal
                                </label>
                                <input type="tel" name="telephone_principal" id="telephone_principal"
                                    value="{{ old('telephone_principal', $user->telephone_principal) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all"
                                    placeholder="+225 XX XX XX XX XX">
                            </div>

                            <!-- Téléphone secondaire -->
                            <div>
                                <label for="telephone_secondaire" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-mobile-alt text-gray-400 mr-1"></i>
                                    Téléphone secondaire
                                </label>
                                <input type="tel" name="telephone_secondaire" id="telephone_secondaire"
                                    value="{{ old('telephone_secondaire', $user->telephone_secondaire) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all"
                                    placeholder="+225 XX XX XX XX XX">
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center space-x-2">
                                <i class="fas fa-save"></i>
                                <span>Enregistrer les modifications</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Changement de mot de passe -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-white">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-lock text-red-500 mr-3"></i>
                            Sécurité - Mot de passe
                        </h2>
                    </div>
                    <form action="{{ route('profile.password') }}" method="POST" class="p-6">
                        @csrf
                        @method('PUT')

                        <div class="space-y-5">
                            <!-- Mot de passe actuel -->
                            <div>
                                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-key text-gray-400 mr-1"></i>
                                    Mot de passe actuel <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" name="current_password" id="current_password" required
                                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('current_password') border-red-500 @enderror"
                                        placeholder="••••••••">
                                    <button type="button" onclick="togglePassword('current_password')"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="current_password_icon"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nouveau mot de passe -->
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-lock text-gray-400 mr-1"></i>
                                    Nouveau mot de passe <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" required
                                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                                        placeholder="••••••••">
                                    <button type="button" onclick="togglePassword('password')"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="password_icon"></i>
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Minimum 8 caractères, avec majuscules, minuscules et chiffres
                                </p>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirmation du mot de passe -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-lock text-gray-400 mr-1"></i>
                                    Confirmer le nouveau mot de passe <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all"
                                        placeholder="••••••••">
                                    <button type="button" onclick="togglePassword('password_confirmation')"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="password_confirmation_icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center space-x-2">
                                <i class="fas fa-shield-alt"></i>
                                <span>Changer le mot de passe</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">

                <!-- Carte de profil -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 px-6 py-8 text-center">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto shadow-lg">
                            <span class="text-4xl font-bold text-orange-600">{{ strtoupper(substr($user->nom_complet, 0, 1)) }}</span>
                        </div>
                        <h3 class="mt-4 text-xl font-bold text-white">{{ $user->nom_complet }}</h3>
                        <p class="text-orange-100 text-sm">{{ $user->email }}</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Rôle</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $user->role?->name ?? 'Non assigné' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Statut</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $user->isActive() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $user->isActive() ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Membre depuis</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $user->created_at?->format('d/m/Y') }}</span>
                        </div>
                        @if($user->email_verified_at)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-500">Email vérifié</span>
                                <span class="inline-flex items-center text-green-600 text-sm">
                                    <i class="fas fa-check-circle mr-1"></i> Oui
                                </span>
                            </div>
                        @else
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-500">Email vérifié</span>
                                <span class="inline-flex items-center text-yellow-600 text-sm">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Non
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Permissions -->
                @if($user->role && $user->role->permissions->count() > 0)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-shield-alt text-blue-500 mr-3"></i>
                                Mes permissions
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->role->permissions as $permission)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fas fa-check mr-1 text-blue-400"></i>
                                        {{ $permission->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Informations système -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-gray-500 mr-3"></i>
                            Informations système
                        </h2>
                    </div>
                    <div class="p-6 space-y-3 text-sm">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">ID utilisateur</span>
                            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ Str::limit($user->id, 8) }}...</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Enregistré le</span>
                            <span class="text-gray-800">{{ $user->created_at?->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Dernière modification</span>
                            <span class="text-gray-800">{{ $user->updated_at?->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($user->creator)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-gray-500">Enregistré par</span>
                                <span class="text-gray-800">{{ $user->creator->nom_complet }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
        <script>
            function togglePassword(fieldId) {
                const field = document.getElementById(fieldId);
                const icon = document.getElementById(fieldId + '_icon');

                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        </script>

        <style>
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fadeIn { animation: fadeIn 0.3s ease-out; }
        </style>
    @endpush
@endsection
