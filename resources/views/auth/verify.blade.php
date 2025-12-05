<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vérification</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="icon" href="" type="image/x-icon">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: 'rgb(5, 39, 89)',
                        'primary-dark': 'rgb(3, 25, 60)',
                        'primary-light': 'rgba(5, 39, 89, 0.8)',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        html, body {
            height: 100vh;
            overflow: hidden;
        }
        .auth-container {
            max-height: 100vh;
            overflow-y: auto;
        }

        /* Code input styling */
        .code-input {
            width: 3rem;
            height: 3.5rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        .shake {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .pulse {
            animation: pulse 2s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-primary to-primary-dark h-screen flex items-center justify-center p-3">
    <div class="w-full max-w-md auth-container">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden animate-[slideUp_0.5s_ease-out]">

            <!-- Header -->
            <div class="bg-primary px-6 py-6 text-center text-white">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                    <i class="fas fa-shield-alt text-3xl text-primary"></i>
                </div>
                <h1 class="text-2xl font-bold mb-1">Vérification</h1>
                <p class="text-xs opacity-90">Entrez le code reçu par email</p>
            </div>

            <!-- Body -->
            <div class="px-6 py-5">

                <div class="bg-blue-50 text-blue-700 rounded-lg p-2.5 mb-4 text-xs">
                    <i class="fas fa-envelope mr-1"></i>
                    Un code à 6 chiffres a été envoyé à votre adresse email.
                </div>

                <!-- Alerts -->
                @if (session('success'))
                    <div class="bg-green-50 text-green-600 rounded-lg p-2 mb-3 text-xs flex items-start">
                        <i class="fas fa-check-circle mt-0.5 mr-1.5"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-50 text-red-600 rounded-lg p-2 mb-3 text-xs flex items-start">
                        <i class="fas fa-exclamation-circle mt-0.5 mr-1.5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('auth.verify.post', ['token' => $token]) }}" id="verifyForm">
                    @csrf

                    <!-- Code Input -->
                    <div class="mb-4">
                        <label class="block font-semibold text-gray-700 mb-3 text-xs text-center">
                            Code de vérification
                        </label>

                        <div class="flex justify-center gap-2 mb-2" id="codeInputs">
                            <input type="text" maxlength="1" class="code-input border-2 border-gray-200 rounded-lg transition-all duration-300 bg-gray-50 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10" data-index="0" inputmode="numeric" pattern="[0-9]">
                            <input type="text" maxlength="1" class="code-input border-2 border-gray-200 rounded-lg transition-all duration-300 bg-gray-50 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10" data-index="1" inputmode="numeric" pattern="[0-9]">
                            <input type="text" maxlength="1" class="code-input border-2 border-gray-200 rounded-lg transition-all duration-300 bg-gray-50 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10" data-index="2" inputmode="numeric" pattern="[0-9]">
                            <input type="text" maxlength="1" class="code-input border-2 border-gray-200 rounded-lg transition-all duration-300 bg-gray-50 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10" data-index="3" inputmode="numeric" pattern="[0-9]">
                            <input type="text" maxlength="1" class="code-input border-2 border-gray-200 rounded-lg transition-all duration-300 bg-gray-50 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10" data-index="4" inputmode="numeric" pattern="[0-9]">
                            <input type="text" maxlength="1" class="code-input border-2 border-gray-200 rounded-lg transition-all duration-300 bg-gray-50 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10" data-index="5" inputmode="numeric" pattern="[0-9]">
                        </div>

                        <input type="hidden" name="code" id="codeValue">

                        <p class="text-xs text-gray-500 text-center">
                            Le code expire dans <span id="timer" class="font-semibold text-primary">10:00</span>
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full py-2.5 bg-primary text-white rounded-lg text-sm font-semibold cursor-pointer transition-all duration-300 shadow-lg shadow-primary/30 hover:bg-primary-dark hover:-translate-y-0.5 hover:shadow-xl hover:shadow-primary/40 active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed"
                        id="submitBtn"
                        disabled
                    >
                        <span id="btnText">
                            <i class="fas fa-check-circle mr-1"></i>
                            Vérifier le code
                        </span>
                        <span id="btnLoader" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-1"></i>
                            Vérification...
                        </span>
                    </button>
                </form>

                <!-- Resend Code -->
                <div class="text-center mt-4">
                    <p class="text-xs text-gray-600 mb-2">Vous n'avez pas reçu le code ?</p>
                    <form method="POST" action="{{ route('auth.verify.resend', ['token' => $token]) }}" id="resendForm">
                        @csrf
                        <button
                            type="submit"
                            class="text-primary text-xs font-medium hover:text-primary-dark hover:underline transition-colors duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                            id="resendBtn"
                        >
                            <i class="fas fa-redo mr-1"></i>
                            <span id="resendText">Renvoyer le code</span>
                        </button>
                    </form>
                </div>

                <!-- Back to Login -->
                <div class="text-center mt-3 pt-3 border-t border-gray-200">
                    <a href="{{ route('auth.login') }}" class="text-gray-600 text-xs font-medium hover:text-primary hover:underline transition-colors duration-300 inline-flex items-center">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Retour à la connexion
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center px-4 py-3 bg-gray-50 text-xs text-gray-600">
                {{-- &copy; {{ date('Y') }} {{ $globalBanniere->raison_sociale ?? config('app.name') }}. Tous droits réservés. --}}
            </div>
        </div>
    </div>

    <script>
        // Code inputs management
        const codeInputs = document.querySelectorAll('.code-input');
        const codeValue = document.getElementById('codeValue');
        const submitBtn = document.getElementById('submitBtn');
        const verifyForm = document.getElementById('verifyForm');
        const btnText = document.getElementById('btnText');
        const btnLoader = document.getElementById('btnLoader');
        const codeInputsContainer = document.getElementById('codeInputs');

        // Focus first input on load
        codeInputs[0].focus();

        // Handle input
        codeInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const value = e.target.value;

                // Only allow numbers
                if (!/^\d*$/.test(value)) {
                    e.target.value = '';
                    return;
                }

                if (value.length === 1) {
                    // Move to next input
                    if (index < codeInputs.length - 1) {
                        codeInputs[index + 1].focus();
                    }
                }

                updateCodeValue();
            });

            // Handle backspace
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    codeInputs[index - 1].focus();
                }
            });

            // Handle paste
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);

                pastedData.split('').forEach((char, i) => {
                    if (codeInputs[i]) {
                        codeInputs[i].value = char;
                    }
                });

                if (pastedData.length === 6) {
                    codeInputs[5].focus();
                } else if (pastedData.length > 0) {
                    codeInputs[Math.min(pastedData.length, 5)].focus();
                }

                updateCodeValue();
            });
        });

        function updateCodeValue() {
            const code = Array.from(codeInputs).map(input => input.value).join('');
            codeValue.value = code;

            // Enable submit button when all 6 digits are entered
            submitBtn.disabled = code.length !== 6;

            // Auto-submit when all digits entered
            if (code.length === 6) {
                setTimeout(() => {
                    verifyForm.submit();
                    submitBtn.disabled = true;
                    btnText.classList.add('hidden');
                    btnLoader.classList.remove('hidden');
                }, 300);
            }
        }

        // Form submission
        verifyForm.addEventListener('submit', function(e) {
            if (codeValue.value.length !== 6) {
                e.preventDefault();
                codeInputsContainer.classList.add('shake');
                setTimeout(() => {
                    codeInputsContainer.classList.remove('shake');
                }, 500);
                return;
            }

            submitBtn.disabled = true;
            btnText.classList.add('hidden');
            btnLoader.classList.remove('hidden');
        });

        // Timer countdown (10 minutes)
        let timeLeft = 600; // 10 minutes in seconds
        const timerElement = document.getElementById('timer');

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeft <= 60) {
                timerElement.classList.add('text-red-500');
                timerElement.classList.add('pulse');
            }

            if (timeLeft <= 0) {
                timerElement.textContent = 'Expiré';
                submitBtn.disabled = true;
                codeInputs.forEach(input => input.disabled = true);

                // Show expired message
                const expiredDiv = document.createElement('div');
                expiredDiv.className = 'bg-red-50 text-red-600 rounded-lg p-2 mb-3 text-xs text-center';
                expiredDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Code expiré. Veuillez demander un nouveau code.';
                document.querySelector('form').before(expiredDiv);

                clearInterval(timerInterval);
            } else {
                timeLeft--;
            }
        }

        const timerInterval = setInterval(updateTimer, 1000);

        // Resend code
        const resendForm = document.getElementById('resendForm');
        const resendBtn = document.getElementById('resendBtn');
        const resendText = document.getElementById('resendText');
        let resendCooldown = false;

        resendForm.addEventListener('submit', function(e) {
            if (resendCooldown) {
                e.preventDefault();
                return;
            }

            resendBtn.disabled = true;
            resendText.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Envoi...';

            // Cooldown for 60 seconds
            resendCooldown = true;
            let cooldownTime = 60;

            const cooldownInterval = setInterval(() => {
                cooldownTime--;
                if (cooldownTime <= 0) {
                    clearInterval(cooldownInterval);
                    resendBtn.disabled = false;
                    resendText.innerHTML = 'Renvoyer le code';
                    resendCooldown = false;
                } else {
                    resendText.textContent = `Renvoyer (${cooldownTime}s)`;
                }
            }, 1000);
        });
    </script>
</body>
</html>
