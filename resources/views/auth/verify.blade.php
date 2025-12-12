@extends('layouts.auth')

@section('title', 'Vérification')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Code input styling */
    .code-input {
        width: 2.75rem;
        height: 3.25rem;
        text-align: center;
        font-size: 1.25rem;
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
@endpush

@section('content')

    <!-- Title -->
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">
        Vérification
    </h2>
    <p class="text-center text-gray-600 text-sm mb-6">
        Entrez le code reçu par email
    </p>

    <!-- Info Message -->
    <div class="bg-blue-100 bg-opacity-70 text-blue-700 rounded-lg p-3 mb-6 text-sm flex items-center">
        <i class="fas fa-envelope mr-2"></i>
        <span>Un code à 6 chiffres a été envoyé à votre adresse email.</span>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="bg-green-100 bg-opacity-70 text-green-700 rounded-lg p-3 mb-4 text-sm flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 bg-opacity-70 text-red-700 rounded-lg p-3 mb-4 text-sm flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('auth.verify.post', ['token' => $token]) }}" id="verifyForm" class="space-y-6">
        @csrf

        <!-- Code Input -->
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-3 text-center">
                Code de vérification
            </label>

            <div class="flex justify-center gap-2 mb-3" id="codeInputs">
                <input type="text" maxlength="1"
                    class="code-input bg-white bg-opacity-70 border-2 border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    data-index="0" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1"
                    class="code-input bg-white bg-opacity-70 border-2 border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    data-index="1" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1"
                    class="code-input bg-white bg-opacity-70 border-2 border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    data-index="2" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1"
                    class="code-input bg-white bg-opacity-70 border-2 border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    data-index="3" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1"
                    class="code-input bg-white bg-opacity-70 border-2 border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    data-index="4" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1"
                    class="code-input bg-white bg-opacity-70 border-2 border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    data-index="5" inputmode="numeric" pattern="[0-9]">
            </div>

            <input type="hidden" name="code" id="codeValue">

            <p class="text-sm text-gray-600 text-center">
                Le code expire dans <span id="timer" class="font-semibold text-blue-600">10:00</span>
            </p>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:hover:scale-100"
                id="submitBtn"
                disabled
            >
                <span id="btnText">
                    <i class="fas fa-check-circle mr-2"></i>
                    Vérifier le code
                </span>
                <span id="btnLoader" class="hidden">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Vérification...
                </span>
            </button>
        </div>
    </form>

    <!-- Resend Code -->
    <div class="mt-6 text-center space-y-3">
        <p class="text-sm text-gray-700">Vous n'avez pas reçu le code ?</p>
        <form method="POST" action="{{ route('auth.verify.resend', ['token' => $token]) }}" id="resendForm">
            @csrf
            <button
                type="submit"
                class="text-sm text-gray-700 hover:text-gray-900 hover:underline font-medium transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                id="resendBtn"
            >
                <i class="fas fa-redo mr-1"></i>
                <span id="resendText">Renvoyer le code</span>
            </button>
        </form>
    </div>

    <!-- Back to Login -->
    <div class="mt-4 pt-4 border-t border-gray-300 border-opacity-50 text-center">
        <a href="{{ route('auth.login') }}" class="text-sm text-gray-700 hover:text-gray-900 hover:underline inline-flex items-center transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour à la connexion
        </a>
    </div>

@endsection

@push('scripts')
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
            timerElement.classList.remove('text-blue-600');
            timerElement.classList.add('text-red-500');
            timerElement.classList.add('pulse');
        }

        if (timeLeft <= 0) {
            timerElement.textContent = 'Expiré';
            submitBtn.disabled = true;
            codeInputs.forEach(input => input.disabled = true);

            // Show expired message
            const expiredDiv = document.createElement('div');
            expiredDiv.className = 'bg-red-100 bg-opacity-70 text-red-700 rounded-lg p-3 mb-4 text-sm flex items-center';
            expiredDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Code expiré. Veuillez demander un nouveau code.';
            verifyForm.before(expiredDiv);

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
@endpush
