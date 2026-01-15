@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')


    <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">
        Connecte
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

    <!-- Form -->
    <form class="space-y-6" action="{{ route('auth.login') }}" method="POST" id="loginForm">

        @csrf

        <!-- Email / Téléphone Field -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-user mr-1 text-green-600"></i>
                Email / Téléphone
            </label>
            <div class="relative">
                <input
                    type="text"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="off"
                    class="w-full pl-4 pr-10 py-3 bg-white bg-opacity-70 border-2 @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 placeholder-gray-500"
                    placeholder="Entrez votre email ou téléphone"
                    required>

                <!-- Icône indicateur du type de connexion -->
                <div id="loginTypeIcon" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 transition-all duration-300">
                    <i class="fas fa-at"></i>
                </div>
            </div>

            @error('email')
                <p class="mt-2 text-sm text-red-600 flex items-center animate-slideDown">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror

            <!-- Aide contextuelle dynamique -->
            <p id="emailHelp" class="mt-2 text-xs text-gray-600 flex items-center transition-all duration-200">
                <i class="fas fa-info-circle mr-1"></i>
                <span>Vous pouvez vous connecter avec votre email ou votre numéro de téléphone</span>
            </p>
        </div>

        <!-- Password Field avec Toggle Premium -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-lock mr-1 text-green-600"></i>
                Mot de passe
            </label>
            <div class="relative">
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="off"
                    class="w-full pl-4 pr-12 py-3 bg-white bg-opacity-70 border-2 @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 placeholder-gray-500"
                    placeholder="Entrez votre mot de passe"
                    required>

                <!-- Toggle password visibility avec tooltip -->
                <button
                    type="button"
                    onclick="togglePassword()"
                    title="Afficher/Masquer le mot de passe"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-green-600 focus:outline-none transition-colors duration-200 group"
                    aria-label="Afficher ou masquer le mot de passe">
                    <i id="toggleIcon" class="fas fa-eye text-lg"></i>

                    <!-- Tooltip amélioré -->
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

            <!-- Indicateur de force du mot de passe (optionnel) -->
            <div id="passwordStrength" class="mt-2 hidden">
                <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                    <div id="strengthBar" class="h-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p id="strengthText" class="mt-1 text-xs flex items-center"></p>
            </div>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label class="flex items-center cursor-pointer group">
                <input
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 focus:ring-2 cursor-pointer transition-all duration-200">
                <span class="ml-2 text-sm text-gray-700 group-hover:text-gray-900 transition-colors duration-200">
                    Se souvenir de moi
                </span>
            </label>

            <a href="{{ route('auth.password.request') }}"
               class="text-sm text-green-600 hover:text-green-700 hover:underline transition-colors duration-200 font-medium">
                <i class="fas fa-key mr-1 text-xs"></i>
                Mot de passe oublié ?
            </a>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button
                type="submit"
                id="loginBtn"
                class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:hover:shadow-lg">
                <span id="btnText" class="inline-flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Se connecter
                </span>
                <span id="btnLoader" class="hidden inline-flex items-center justify-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Connexion en cours...
                </span>
            </button>
        </div>

    </form>

    <!-- Raccourci clavier info (optionnel) -->
    <div class="mt-6 p-3 bg-green-50 bg-opacity-60 rounded-lg border border-green-200">
        <p class="text-xs text-gray-700 text-center flex items-center justify-center">
            <i class="fas fa-keyboard mr-2 text-green-600"></i>
            <span>Astuce : Appuyez sur
                <kbd class="px-1 py-0.5 bg-white border border-gray-300 rounded text-xs font-mono mx-1">Ctrl</kbd>+
                <kbd class="px-1 py-0.5 bg-white border border-gray-300 rounded text-xs font-mono mx-1">Shift</kbd>+
                <kbd class="px-1 py-0.5 bg-white border border-gray-300 rounded text-xs font-mono mx-1">P</kbd>
                pour afficher/masquer le mot de passe
            </span>
        </p>
    </div>

    <!-- Styles CSS personnalisés -->
    <style>
        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
            }
            to {
                opacity: 1;
                max-height: 100px;
            }
        }

        .animate-slideIn {
            animation: slideIn 0.3s ease-out;
        }

        .animate-slideDown {
            animation: slideDown 0.3s ease-out;
        }

        /* Animation de l'icône toggle */
        #toggleIcon {
            transition: transform 0.3s ease, color 0.2s ease;
        }

        #toggleIcon.active {
            transform: scale(1.2) rotate(10deg);
        }

        /* Tooltip personnalisé */
        .tooltip-custom::after {
            content: '';
            position: absolute;
            bottom: -4px;
            right: 10px;
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid #1f2937;
        }

        /* Animation du champ en focus */
        input:focus {
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }

        /* Animation du bouton */
        #loginBtn:active {
            transform: scale(0.98);
        }

        /* Style des kbd tags */
        kbd {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
    </style>

    <!-- Scripts JavaScript -->
    <script>
        // Variables globales
        let toggleCount = 0;

        /**
         * Toggle password visibility avec animations et feedback
         */
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            const tooltipText = document.querySelector('.tooltip-custom');

            // Compteur de toggles (pour analytics si besoin)
            toggleCount++;

            // Animation de l'icône
            toggleIcon.classList.add('active');
            setTimeout(() => toggleIcon.classList.remove('active'), 300);

            if (passwordInput.type === 'password') {
                // Afficher le mot de passe
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
                toggleIcon.style.color = '#16a34a'; // Green-600
                if (tooltipText) {
                    tooltipText.textContent = 'Masquer';
                }

                // Log pour debug (à retirer en production)
                console.log('Mot de passe affiché');
            } else {
                // Masquer le mot de passe
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
                toggleIcon.style.color = '';
                if (tooltipText) {
                    tooltipText.textContent = 'Afficher';
                }

                // Log pour debug (à retirer en production)
                console.log('Mot de passe masqué');
            }
        }

        /**
         * Raccourci clavier : Ctrl + Shift + P pour toggle password
         */
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'P') {
                e.preventDefault();
                togglePassword();

                // Feedback visuel rapide
                const passwordInput = document.getElementById('password');
                passwordInput.classList.add('ring-2', 'ring-green-400');
                setTimeout(() => {
                    passwordInput.classList.remove('ring-2', 'ring-green-400');
                }, 300);
            }
        });

        /**
         * Détection en temps réel du type d'identifiant (email/téléphone)
         */
        document.getElementById('email').addEventListener('input', function(e) {
            const value = e.target.value.trim();
            const helpText = document.getElementById('emailHelp');
            const iconContainer = document.getElementById('loginTypeIcon');

            if (value.length > 0) {
                // Détecter le format
                const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                const isPhone = /^[\d\s\+\-\(\)]+$/.test(value);

                if (isEmail) {
                    // Format email détecté
                    helpText.innerHTML = '<i class="fas fa-envelope mr-1"></i> <span class="font-medium">✓ Connexion par email</span>';
                    helpText.className = 'mt-2 text-xs text-green-600 flex items-center transition-all duration-200';
                    iconContainer.innerHTML = '<i class="fas fa-envelope text-green-600 animate-pulse"></i>';
                } else if (isPhone) {
                    // Format téléphone détecté
                    helpText.innerHTML = '<i class="fas fa-phone mr-1"></i> <span class="font-medium">✓ Connexion par téléphone</span>';
                    helpText.className = 'mt-2 text-xs text-blue-600 flex items-center transition-all duration-200';
                    iconContainer.innerHTML = '<i class="fas fa-phone text-blue-600 animate-pulse"></i>';
                } else {
                    // Format non reconnu
                    helpText.innerHTML = '<i class="fas fa-info-circle mr-1"></i> <span>Continuez à saisir...</span>';
                    helpText.className = 'mt-2 text-xs text-gray-600 flex items-center transition-all duration-200';
                    iconContainer.innerHTML = '<i class="fas fa-at text-gray-400"></i>';
                }
            } else {
                // Champ vide - message par défaut
                helpText.innerHTML = '<i class="fas fa-info-circle mr-1"></i> <span>Vous pouvez vous connecter avec votre email ou votre numéro de téléphone</span>';
                helpText.className = 'mt-2 text-xs text-gray-600 flex items-center transition-all duration-200';
                iconContainer.innerHTML = '<i class="fas fa-at text-gray-400"></i>';
            }
        });

        /**
         * Indicateur de force du mot de passe (optionnel)
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
         * Loading state lors de la soumission du formulaire
         */
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');

            // Désactiver le bouton et afficher le loader
            btn.disabled = true;
            btnText.classList.add('hidden');
            btnLoader.classList.remove('hidden');

            // Log pour debug (à retirer en production)
            console.log('Formulaire en cours de soumission...');
        });

        /**
         * Focus automatique sur le champ email au chargement
         */
        window.addEventListener('load', function() {
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.focus();
            }
        });

        /**
         * Validation côté client avant soumission (optionnel)
         */
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            // Validation minimale
            if (email.length === 0) {
                e.preventDefault();
                alert('Veuillez entrer votre email ou téléphone');
                document.getElementById('email').focus();
                return false;
            }

            if (password.length === 0) {
                e.preventDefault();
                alert('Veuillez entrer votre mot de passe');
                document.getElementById('password').focus();
                return false;
            }

            // Validation du format email si c'est un email
            const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            const isPhone = /^[\d\s\+\-\(\)]+$/.test(email);

            if (!isEmail && !isPhone) {
                e.preventDefault();
                alert('Format invalide. Veuillez entrer un email valide ou un numéro de téléphone');
                document.getElementById('email').focus();

                // Réactiver le bouton
                const btn = document.getElementById('loginBtn');
                const btnText = document.getElementById('btnText');
                const btnLoader = document.getElementById('btnLoader');
                btn.disabled = false;
                btnText.classList.remove('hidden');
                btnLoader.classList.add('hidden');

                return false;
            }
        });

        // Log pour confirmer le chargement du script
        console.log('✓ Script de login chargé avec succès');
        console.log('✓ Toggle password: Disponible');
        console.log('✓ Raccourci clavier: Ctrl+Shift+P');
    </script>

@endsection
