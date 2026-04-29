@extends('layouts.main')
@section('title', 'Créer un utilisateur')
@section('breadcrumb')
    <a @can('users.read') href="{{ route('admin.users.index') }}" @endcan class="text-white/80 hover:text-white transition-colors">Utilisateurs</a>
    <i class="fas fa-chevron-right text-white/50 text-xs mx-2"></i>
    <span class="text-white font-medium">Nouveau</span>
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 shadow-sm">
        <div class="px-3 sm:px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @can('users.read')
                        <a href="{{ route('admin.users.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-600"></i>
                        </a>
                    @endcan
                    <h1 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-user-plus text-orange-500 mr-2"></i>
                        Créer un utilisateur
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-3 sm:p-4 lg:p-6">

        {{-- ============================================================ --}}
        {{-- MESSAGES FLASH (success, error, warning, info) --}}
        {{-- ============================================================ --}}

        {{-- Message de succès --}}
        @if (session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-green-500 hover:text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- Message d'erreur (flash du contrôleur) --}}
        @if (session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-red-700 font-medium">Erreur</p>
                        <p class="text-red-600 text-sm mt-1">{{ session('error') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- Message d'avertissement --}}
        @if (session('warning'))
            <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-yellow-700 font-medium">Attention</p>
                        <p class="text-yellow-600 text-sm mt-1">{{ session('warning') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-yellow-500 hover:text-yellow-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- Message d'information --}}
        @if (session('info'))
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-blue-700 font-medium">Information</p>
                        <p class="text-blue-600 text-sm mt-1">{{ session('info') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-blue-500 hover:text-blue-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- ERREURS DE VALIDATION (FormRequest) --}}
        {{-- ============================================================ --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-red-700 font-medium mb-2">Erreurs de validation :</p>
                        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @can('users.create')
            <form action="{{ route('admin.users.store') }}" method="POST" class="w-full">
                @csrf

                <!-- Grid layout pour 2 colonnes sur grand écran -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                    <!-- Colonne gauche -->
                    <div class="space-y-6">
                        <!-- Informations personnelles -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fas fa-user text-orange-500 mr-2"></i>
                                    Informations personnelles
                                </h3>
                            </div>
                            <div class="p-6 space-y-6">
                                <!-- Nom complet -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nom complet <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nom_complet" value="{{ old('nom_complet') }}" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all @error('nom_complet') border-red-500 @enderror"
                                        placeholder="Ex: Jean Kouassi">
                                    @error('nom_complet')
                                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Adresse email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="email" value="{{ old('email') }}" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all @error('email') border-red-500 @enderror"
                                        placeholder="utilisateur@example.com">
                                    <p class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Les adresses email temporaires (yopmail, guerrillamail, etc.) ne sont pas acceptées.
                                    </p>
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Téléphones -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Téléphone principal
                                        </label>
                                        <input type="text" name="telephone_principal" value="{{ old('telephone_principal') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all @error('telephone_principal') border-red-500 @enderror"
                                            placeholder="+225 XX XX XX XX XX">
                                        @error('telephone_principal')
                                            <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Téléphone secondaire
                                        </label>
                                        <input type="text" name="telephone_secondaire" value="{{ old('telephone_secondaire') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all @error('telephone_secondaire') border-red-500 @enderror"
                                            placeholder="+225 XX XX XX XX XX">
                                        @error('telephone_secondaire')
                                            <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rôle et statut -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fas fa-user-shield text-purple-500 mr-2"></i>
                                    Rôle et permissions
                                </h3>
                            </div>
                            <div class="p-6 space-y-6">
                                <!-- Rôle -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Rôle <span class="text-red-500">*</span>
                                    </label>
                                    <select name="role_id" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all @error('role_id') border-red-500 @enderror">
                                        <option value="">Sélectionner un rôle</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }} (Niveau {{ $role->level }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Statut -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Statut <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex items-center space-x-6">
                                        <label class="flex items-center cursor-pointer group">
                                            <input type="radio" name="statut" value="1" {{ old('statut', '1') == '1' ? 'checked' : '' }} required
                                                class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                                            <span class="ml-2 text-sm text-gray-700 group-hover:text-green-600 transition-colors">
                                                <i class="fas fa-check-circle text-green-500 mr-1"></i>Actif
                                            </span>
                                        </label>
                                        <label class="flex items-center cursor-pointer group">
                                            <input type="radio" name="statut" value="0" {{ old('statut') == '0' ? 'checked' : '' }}
                                                class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                                            <span class="ml-2 text-sm text-gray-700 group-hover:text-red-600 transition-colors">
                                                <i class="fas fa-times-circle text-red-500 mr-1"></i>Inactif
                                            </span>
                                        </label>
                                    </div>
                                    @error('statut')
                                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email vérifié -->
                                <div>
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="checkbox" name="email_verified" value="1" {{ old('email_verified') ? 'checked' : '' }}
                                            class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                        <span class="ml-2 text-sm text-gray-700 group-hover:text-orange-600 transition-colors">
                                            <i class="fas fa-envelope-circle-check text-orange-500 mr-1"></i>
                                            Marquer l'email comme vérifié
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne droite -->
                    <div class="space-y-6">
                        <!-- Sécurité / Mot de passe -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fas fa-lock text-blue-500 mr-2"></i>
                                    Sécurité
                                </h3>
                            </div>
                            <div class="p-6 space-y-6">
                                <!-- Mot de passe -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Mot de passe <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" name="password" id="password" required minlength="8"
                                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all @error('password') border-red-500 @enderror"
                                            placeholder="Minimum 8 caractères">
                                        <button type="button" onclick="togglePassword('password', 'togglePasswordIcon')"
                                            class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 hover:text-orange-500 focus:outline-none transition-colors"
                                            title="Afficher/Masquer le mot de passe">
                                            <i id="togglePasswordIcon" class="fas fa-eye text-lg"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirmation mot de passe -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Confirmer le mot de passe <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" name="password_confirmation" id="password_confirmation" required
                                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-transparent hover:border-orange-300 transition-all"
                                            placeholder="Confirmer le mot de passe">
                                        <button type="button" onclick="togglePassword('password_confirmation', 'togglePasswordConfirmIcon')"
                                            class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 hover:text-orange-500 focus:outline-none transition-colors"
                                            title="Afficher/Masquer le mot de passe">
                                            <i id="togglePasswordConfirmIcon" class="fas fa-eye text-lg"></i>
                                        </button>
                                    </div>
                                    <!-- Indicateur de correspondance -->
                                    <div id="passwordMatchIndicator" class="mt-2 hidden">
                                        <span id="passwordMatchText" class="text-sm flex items-center"></span>
                                    </div>
                                </div>

                                <!-- Indicateur de force du mot de passe -->
                                <div id="passwordStrengthContainer" class="hidden">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-600">Force du mot de passe</span>
                                        <span id="passwordStrengthText" class="text-sm font-bold"></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div id="passwordStrengthBar" class="h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>

                                    <!-- Critères du mot de passe -->
                                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                        <p class="text-sm font-medium text-gray-700 mb-3">Critères de sécurité :</p>
                                        <ul id="passwordRequirements" class="space-y-2">
                                            <li id="req-length" class="flex items-center text-sm text-gray-500">
                                                <i class="fas fa-circle text-[8px] mr-3"></i>
                                                Minimum 8 caractères
                                            </li>
                                            <li id="req-uppercase" class="flex items-center text-sm text-gray-500">
                                                <i class="fas fa-circle text-[8px] mr-3"></i>
                                                Au moins une majuscule (A-Z)
                                            </li>
                                            <li id="req-lowercase" class="flex items-center text-sm text-gray-500">
                                                <i class="fas fa-circle text-[8px] mr-3"></i>
                                                Au moins une minuscule (a-z)
                                            </li>
                                            <li id="req-number" class="flex items-center text-sm text-gray-500">
                                                <i class="fas fa-circle text-[8px] mr-3"></i>
                                                Au moins un chiffre (0-9)
                                            </li>
                                            <li id="req-special" class="flex items-center text-sm text-gray-500">
                                                <i class="fas fa-circle text-[8px] mr-3"></i>
                                                Au moins un caractère spécial (!@#$%^&*)
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Note informative -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <i class="fas fa-envelope text-blue-500 mt-0.5 mr-3"></i>
                                        <div class="text-sm text-blue-700">
                                            <p class="font-semibold">Email de bienvenue obligatoire</p>
                                            <p class="mt-1">Un email contenant les identifiants de connexion sera automatiquement envoyé à l'utilisateur. Si l'envoi échoue, le compte ne sera pas créé.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 flex items-center space-x-2">
                        <i class="fas fa-times text-sm"></i>
                        <span class="font-medium">Annuler</span>
                    </a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-md">
                        <i class="fas fa-save text-sm"></i>
                        <span class="font-medium">Créer l'utilisateur</span>
                    </button>
                </div>
            </form>
        @endcan
    </main>
@endsection

@push('scripts')
<script>
    /**
     * Toggle password visibility
     */
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

    /**
     * Password strength checker and match validator
     */
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirmation');
        const strengthContainer = document.getElementById('passwordStrengthContainer');
        const strengthBar = document.getElementById('passwordStrengthBar');
        const strengthText = document.getElementById('passwordStrengthText');
        const matchIndicator = document.getElementById('passwordMatchIndicator');
        const matchText = document.getElementById('passwordMatchText');

        // Password strength check on input
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;

                if (password.length > 0) {
                    strengthContainer.classList.remove('hidden');
                    const result = checkPasswordStrength(password);
                    updateStrengthUI(result);
                } else {
                    strengthContainer.classList.add('hidden');
                }

                // Also check match if confirm field has value
                if (passwordConfirmInput.value.length > 0) {
                    checkPasswordMatch();
                }
            });
        }

        // Password match check
        if (passwordConfirmInput) {
            passwordConfirmInput.addEventListener('input', checkPasswordMatch);
        }

        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = passwordConfirmInput.value;

            if (confirmPassword.length > 0) {
                matchIndicator.classList.remove('hidden');

                if (password === confirmPassword) {
                    matchText.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-2"></i><span class="text-green-600">Les mots de passe correspondent</span>';
                } else {
                    matchText.innerHTML = '<i class="fas fa-times-circle text-red-500 mr-2"></i><span class="text-red-600">Les mots de passe ne correspondent pas</span>';
                }
            } else {
                matchIndicator.classList.add('hidden');
            }
        }

        function checkPasswordStrength(password) {
            let score = 0;
            const checks = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>\-_=+\[\]\\;'/`~]/.test(password)
            };

            // Calculate score
            if (checks.length) score++;
            if (checks.uppercase) score++;
            if (checks.lowercase) score++;
            if (checks.number) score++;
            if (checks.special) score++;

            return { score, checks };
        }

        function updateStrengthUI(result) {
            const { score, checks } = result;

            // Update requirements list
            updateRequirement('req-length', checks.length);
            updateRequirement('req-uppercase', checks.uppercase);
            updateRequirement('req-lowercase', checks.lowercase);
            updateRequirement('req-number', checks.number);
            updateRequirement('req-special', checks.special);

            // Update strength bar and text
            let width, bgColor, textColor, text;

            switch (score) {
                case 0:
                case 1:
                    width = '20%';
                    bgColor = 'bg-red-500';
                    textColor = 'text-red-500';
                    text = 'Très faible';
                    break;
                case 2:
                    width = '40%';
                    bgColor = 'bg-orange-500';
                    textColor = 'text-orange-500';
                    text = 'Faible';
                    break;
                case 3:
                    width = '60%';
                    bgColor = 'bg-yellow-500';
                    textColor = 'text-yellow-600';
                    text = 'Moyen';
                    break;
                case 4:
                    width = '80%';
                    bgColor = 'bg-lime-500';
                    textColor = 'text-lime-600';
                    text = 'Fort';
                    break;
                case 5:
                    width = '100%';
                    bgColor = 'bg-green-500';
                    textColor = 'text-green-600';
                    text = 'Très fort';
                    break;
            }

            strengthBar.style.width = width;
            strengthBar.className = `h-2.5 rounded-full transition-all duration-300 ${bgColor}`;
            strengthText.textContent = text;
            strengthText.className = `text-sm font-bold ${textColor}`;
        }

        function updateRequirement(elementId, isValid) {
            const element = document.getElementById(elementId);
            const icon = element.querySelector('i');

            if (isValid) {
                element.classList.remove('text-gray-500');
                element.classList.add('text-green-600');
                icon.classList.remove('fa-circle');
                icon.classList.add('fa-check-circle');
            } else {
                element.classList.remove('text-green-600');
                element.classList.add('text-gray-500');
                icon.classList.remove('fa-check-circle');
                icon.classList.add('fa-circle');
            }
        }
    });
</script>
@endpush
