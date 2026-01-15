@extends('layouts.auth')

@section('title', 'Réinitialisation du mot de passe')

@section('content')

    <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">
        Réinitialiser votre mot de passe
        <span style="background: linear-gradient(to right, green, orange); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            {{ env('APP_NAME') }}
        </span>
    </h2>

    <!-- Messages d'erreur/succès globaux -->
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg animate-slideIn">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg animate-slideIn">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Informations de sécurité -->
    <div class="mb-6 p-4 bg-blue-50 bg-opacity-60 rounded-lg border border-blue-200">
        <p class="text-xs text-gray-700 flex items-start">
            <i class="fas fa-info-circle mr-2 text-blue-600 mt-0.5"></i>
            <span>Créez un mot de passe sécurisé d'au moins 8 caractères, incluant des lettres majuscules, minuscules, chiffres et caractères spéciaux.</span>
        </p>
    </div>

    <!-- Form -->
    <form class="space-y-6" action="{{ route('auth.password.update') }}" method="POST" id="resetForm">

        @csrf

        <!-- Token et Email cachés -->
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- Affichage de l'email (lecture seule) -->
        <div>
            <label for="email_display" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-envelope mr-1 text-green-600"></i>
                Adresse email
            </label>
            <div class="relative">
                <input
                    type="text"
                    id="email_display"
                    value="{{ $email }}"
                    disabled
                    class="w-full pl-4 pr-10 py-3 bg-gray-100 bg-opacity-70 border-2 border-gray-300 rounded-lg text-gray-600 cursor-not-allowed">

                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-lock"></i>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-600 flex items-center">
                <i class="fas fa-shield-alt mr-1"></i>
                <span>Votre adresse email ne peut pas être modifiée</span>
            </p>
        </div>

        <!-- Nouveau mot de passe -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-lock mr-1 text-green-600"></i>
                Nouveau mot de passe
            </label>
            <div class="relative">
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="off"
                    class="w-full pl-4 pr-12 py-3 bg-white bg-opacity-70 border-2 @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 placeholder-gray-500"
                    placeholder="Entrez votre nouveau mot de passe"
                    required>

                <!-- Toggle password visibility -->
                <button
                    type="button"
                    onclick="togglePassword('password', 'toggleIcon')"
                    title="Afficher/Masquer le mot de passe"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-green-600 focus:outline-none transition-colors duration-200 group"
                    aria-label="Afficher ou masquer le mot de passe">
                    <i id="toggleIcon" class="fas fa-eye text-lg"></i>

                    <!-- Tooltip -->
                    <span class="tooltip-custom absolute -top-10 right-0 bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none z-10">
                        Afficher
                    </span>
                </button>
            </div>

            @error('password')
                <p class="mt-2 text-sm text-red-600 flex items-center animate-slideDown">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror

            <!-- Indicateur de force du mot de passe -->
            <div id="passwordStrength" class="mt-2 hidden">
                <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                    <div id="strengthBar" class="h-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p id="strengthText" class="mt-1 text-xs flex items-center"></p>
            </div>
        </div>

        <!-- Confirmation du mot de passe -->
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-lock-open mr-1 text-green-600"></i>
                Confirmer le mot de passe
            </label>
            <div class="relative">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="off"
                    class="w-full pl-4 pr-12 py-3 bg-white bg-opacity-70 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 placeholder-gray-500"
                    placeholder="Confirmez votre nouveau mot de passe"
                    required>

                <!-- Toggle password visibility -->
                <button
                    type="button"
                    onclick="togglePassword('password_confirmation', 'toggleIconConfirm')"
                    title="Afficher/Masquer le mot de passe"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-green-600 focus:outline-none transition-colors duration-200 group"
                    aria-label="Afficher ou masquer le mot de passe">
                    <i id="toggleIconConfirm" class="fas fa-eye text-lg"></i>

                    <!-- Tooltip -->
                    <span class="tooltip-custom absolute -top-10 right-0 bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none z-10">
                        Afficher
                    </span>
                </button>
            </div>

            <!-- Message de correspondance -->
            <p id="matchMessage" class="mt-2 text-xs hidden"></p>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button
                type="submit"
                id="resetBtn"
                class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:hover:shadow-lg">
                <span id="btnText" class="inline-flex items-center justify-center">
                    <i class="fas fa-key mr-2"></i>
                    Réinitialiser le mot de passe
                </span>
                <span id="btnLoader" class="hidden inline-flex items-center justify-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Réinitialisation en cours...
                </span>
            </button>
        </div>

    </form>

    <!-- Retour à la connexion -->
    <div class="mt-6 text-center">
        <a href="{{ route('auth.index') }}"
           class="text-sm text-green-600 hover:text-green-700 hover:underline transition-colors duration-200 font-medium inline-flex items-center">
            <i class="fas fa-arrow-left mr-1 text-xs"></i>
            Retour à la connexion
        </a>
    </div>

    <!-- Conseils de sécurité -->
    <div class="mt-6 p-3 bg-green-50 bg-opacity-60 rounded-lg border border-green-200">
        <p class="text-xs text-gray-700 font-semibold mb-2 flex items-center">
            <i class="fas fa-shield-alt mr-2 text-green-600"></i>
            Conseils pour un mot de passe sécurisé :
        </p>
        <ul class="text-xs text-gray-600 space-y-1 ml-6 list-disc">
            <li>Au moins 8 caractères</li>
            <li>Mélange de lettres majuscules et minuscules</li>
            <li>Inclusion de chiffres et caractères spéciaux</li>
            <li>Évitez les mots du dictionnaire</li>
        </ul>
    </div>

    <script>
        /**
         * Toggle password visibility pour un champ spécifique
         */
        function togglePassword(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            const tooltip = icon.parentElement.querySelector('.tooltip-custom');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                if (tooltip) tooltip.textContent = 'Masquer';
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                if (tooltip) tooltip.textContent = 'Afficher';
            }
        }

        /**
         * Indicateur de force du mot de passe
         */
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthContainer = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('strengthText');

            if (password.length > 0) {
                strengthContainer.classList.remove('hidden');

                let strength = 0;
                let criteria = [];

                // Calcul de la force
                if (password.length >= 8) {
                    strength += 25;
                    criteria.push('longueur OK');
                }
                if (password.length >= 12) {
                    strength += 25;
                }
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
                    strength += 20;
                    criteria.push('majuscules/minuscules');
                }
                if (/[0-9]/.test(password)) {
                    strength += 15;
                    criteria.push('chiffres');
                }
                if (/[^a-zA-Z0-9]/.test(password)) {
                    strength += 15;
                    criteria.push('caractères spéciaux');
                }

                strengthBar.style.width = strength + '%';

                // Couleurs et messages selon la force
                if (strength < 40) {
                    strengthBar.className = 'h-full bg-gradient-to-r from-red-500 to-red-600 transition-all duration-300';
                    strengthText.innerHTML = '<i class="fas fa-exclamation-triangle mr-1 text-red-600"></i> <span class="text-red-600">Faible</span>';
                } else if (strength < 70) {
                    strengthBar.className = 'h-full bg-gradient-to-r from-yellow-500 to-orange-500 transition-all duration-300';
                    strengthText.innerHTML = '<i class="fas fa-info-circle mr-1 text-yellow-600"></i> <span class="text-yellow-600">Moyen</span>';
                } else {
                    strengthBar.className = 'h-full bg-gradient-to-r from-green-500 to-green-600 transition-all duration-300';
                    strengthText.innerHTML = '<i class="fas fa-check-circle mr-1 text-green-600"></i> <span class="text-green-600">Fort</span>';
                }
            } else {
                strengthContainer.classList.add('hidden');
            }
        });

        /**
         * Vérification de la correspondance des mots de passe
         */
        document.getElementById('password_confirmation').addEventListener('input', function(e) {
            const password = document.getElementById('password').value;
            const confirmation = e.target.value;
            const matchMessage = document.getElementById('matchMessage');
            const confirmField = e.target;

            if (confirmation.length > 0) {
                matchMessage.classList.remove('hidden');

                if (password === confirmation) {
                    matchMessage.innerHTML = '<i class="fas fa-check-circle mr-1 text-green-600"></i> <span class="text-green-600">Les mots de passe correspondent ✓</span>';
                    matchMessage.className = 'mt-2 text-xs flex items-center';
                    confirmField.classList.remove('border-red-500');
                    confirmField.classList.add('border-green-500');
                } else {
                    matchMessage.innerHTML = '<i class="fas fa-exclamation-circle mr-1 text-red-600"></i> <span class="text-red-600">Les mots de passe ne correspondent pas</span>';
                    matchMessage.className = 'mt-2 text-xs flex items-center';
                    confirmField.classList.add('border-red-500');
                    confirmField.classList.remove('border-green-500');
                }
            } else {
                matchMessage.classList.add('hidden');
                confirmField.classList.remove('border-red-500', 'border-green-500');
            }
        });

        /**
         * Loading state lors de la soumission du formulaire
         */
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;

            // Vérifier que les mots de passe correspondent
            if (password !== confirmation) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas. Veuillez vérifier.');
                document.getElementById('password_confirmation').focus();
                return false;
            }

            // Vérifier la longueur minimale
            if (password.length < 8) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 8 caractères.');
                document.getElementById('password').focus();
                return false;
            }

            const btn = document.getElementById('resetBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');

            // Désactiver le bouton et afficher le loader
            btn.disabled = true;
            btnText.classList.add('hidden');
            btnLoader.classList.remove('hidden');

            console.log('Formulaire en cours de soumission...');
        });

        /**
         * Focus automatique sur le champ mot de passe au chargement
         */
        window.addEventListener('load', function() {
            const passwordInput = document.getElementById('password');
            if (passwordInput) {
                passwordInput.focus();
            }
        });

        /**
         * Raccourci clavier Ctrl+Shift+R pour focus rapide sur le champ mot de passe
         */
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'R') {
                e.preventDefault();
                document.getElementById('password').focus();
            }
        });

        // Log pour confirmer le chargement du script
        console.log('✓ Script de réinitialisation chargé avec succès');
        console.log('✓ Toggle password: Disponible');
        console.log('✓ Vérification de correspondance: Activée');
        console.log('✓ Indicateur de force: Activé');
    </script>

@endsection
