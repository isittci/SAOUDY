<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    {{-- Encodage et Viewport --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Titre SEO optimisé --}}
    <title>@yield('title', config('app.name')) | @yield('page_title', 'Plateforme de gestion')</title>

    {{-- Meta descriptions SEO --}}
    <meta name="description" content="@yield('meta_description', config('app.description', 'Plateforme de gestion sécurisée - Connectez-vous pour accéder à votre espace personnel et gérer vos services.'))">
    <meta name="keywords" content="@yield('meta_keywords', config('app.keywords', 'gestion, plateforme, services, espace client, connexion, authentification'))">
    <meta name="author" content="{{ config('app.author', config('app.name')) }}">

    {{-- Robots et indexation --}}
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <meta name="googlebot" content="@yield('googlebot', 'index, follow')">

    {{-- Canonical URL pour éviter le contenu dupliqué --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph (Facebook, LinkedIn) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', config('app.description', 'Plateforme de gestion sécurisée'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.png'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="@yield('og_image_alt', config('app.name'))">
    <meta property="og:locale" content="fr_FR">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@yield('twitter_site', config('app.twitter_handle', ''))">
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name'))">
    <meta name="twitter:description" content="@yield('twitter_description', config('app.description', 'Plateforme de gestion sécurisée'))">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/twitter-card.png'))">

    {{-- Favicon et icônes --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    {{-- Theme color pour mobile --}}
    <meta name="theme-color" content="#22c55e">
    <meta name="msapplication-TileColor" content="#22c55e">
    <meta name="msapplication-navbutton-color" content="#22c55e">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">

    {{-- Preconnect pour performances --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    {{-- DNS Prefetch --}}
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">

    {{-- Structured Data (Schema.org) --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "{{ config('app.name') }}",
        "description": "@yield('meta_description', config('app.description', 'Plateforme de gestion sécurisée'))",
        "url": "{{ config('app.url') }}",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "XOF"
        },
        "author": {
            "@type": "Organization",
            "name": "{{ config('app.name') }}",
            "url": "{{ config('app.url') }}"
        }
    }
    </script>

    {{-- Breadcrumb Schema (si disponible) --}}
    @hasSection('breadcrumb_schema')
        @yield('breadcrumb_schema')
    @endif

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-gradient-to-br from-yellow-300 via-green-400 to-green-500 flex items-center justify-center p-4">

    {{-- Skip to content pour accessibilité --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-white px-4 py-2 rounded-lg shadow-lg z-50">
        Aller au contenu principal
    </a>

    <main id="main-content" class="w-full max-w-md bg-gradient-to-br from-yellow-200 to-green-300 rounded-3xl shadow-2xl p-8" role="main">
        {{-- Header avec logo accessible --}}
        <header class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-blue-400 rounded-full flex items-center justify-center shadow-lg" role="img" aria-label="Logo {{ config('app.name') }}">
                <svg class="w-12 h-12 text-blue-900" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                </svg>
            </div>
        </header>

        {{-- Contenu principal --}}
        <article>
            @yield('content')
        </article>
    </main>

    {{-- Noscript fallback --}}
    <noscript>
        <div class="fixed inset-0 bg-yellow-100 flex items-center justify-center p-4 z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md text-center">
                <h2 class="text-xl font-bold text-gray-800 mb-2">JavaScript requis</h2>
                <p class="text-gray-600">Veuillez activer JavaScript dans votre navigateur pour utiliser cette application.</p>
            </div>
        </div>
    </noscript>

    @stack('scripts')
</body>
</html>
