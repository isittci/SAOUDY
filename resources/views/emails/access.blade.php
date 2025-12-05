<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès à votre compte</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f3f4f6;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, rgb(5, 39, 89) 0%, rgb(16, 132, 237) 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .content {
            padding: 30px;
            color: #374151;
        }
        .content h2 {
            font-size: 18px;
            margin-top: 0;
        }
        .credentials {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            font-size: 15px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>Bienvenue sur {{ config('app.name') }}</h1>
        </div>

        <!-- Contenu -->
        <div class="content">
            <h2>Bonjour {{ $user->nom_complet }},</h2>
            <p>Votre compte a été créé avec succès sur <strong>{{ config('app.name') }}</strong>.</p>
            <p>Voici vos identifiants de connexion :</p>

            <div class="credentials">
                <p><strong>Email :</strong> {{ $user->email }}</p>
                <p><strong>Mot de passe :</strong> {{ $password }}</p>
            </div>

            <p>Nous vous recommandons de vous connecter et de modifier votre mot de passe dès que possible pour renforcer la sécurité de votre compte.</p>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
