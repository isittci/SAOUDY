<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Tailwind</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-blue-500">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-white">
            Si ce texte est blanc sur fond bleu, Tailwind fonctionne !
        </h1>
    </div>
</body>

</html>
