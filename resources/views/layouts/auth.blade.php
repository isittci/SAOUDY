<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-gradient-to-br from-yellow-300 via-green-400 to-green-500 flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-gradient-to-br from-yellow-200 to-green-300 rounded-3xl shadow-2xl p-8">

        <!-- Icon -->
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-blue-400 rounded-full flex items-center justify-center shadow-lg">
                <svg class="w-12 h-12 text-blue-900" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                </svg>
            </div>
        </div>





        @yield('content')

    </div>


    @stack('scripts')

</body>
</html>
