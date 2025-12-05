@extends('layouts.auth')

@section('title', 'Login')

@section('content')

    <!-- Title -->
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">
        Authentification
    </h2>

    <!-- Form -->
    <form class="space-y-6" action="{{ route('auth.login') }}" method="POST">

        @csrf

        <!-- Email / Téléphone Field -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-800 mb-2">
                Email / Téléphone
            </label>
            <input type="text" id="email" name="email" value="{{ old('email', env('SUPER_ADMIN_EMAIL')) }}"
                class="w-full px-4 py-3 bg-white bg-opacity-70 border-2 border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 placeholder-gray-500"
                placeholder="Entrez votre email ou téléphone" required>
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-800 mb-2">
                Mot de passe
            </label>
            <input type="text" id="password" name="password" value="{{ env('SUPER_ADMIN_PASSWORD') }}"
                class="w-full px-4 py-3 bg-white bg-opacity-70 border-2 border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 placeholder-gray-500"
                placeholder="Entrez votre mot de passe" required>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Se connecter
            </button>
        </div>

    </form>

    <!-- Optional: Additional Links -->
    <div class="mt-6 text-center space-y-2">
        <a href="#" class="text-sm text-gray-700 hover:text-gray-900 hover:underline">
            Mot de passe oublié ?
        </a>
    </div>

@endsection
