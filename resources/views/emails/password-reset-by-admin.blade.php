<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            margin: -30px -30px 30px -30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .password-box {
            background-color: #fff7ed;
            border-left: 4px solid #f97316;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .password-box p {
            margin: 5px 0;
        }
        .password-value {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #ea580c;
            background-color: #ffffff;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
        }
        .warning {
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .info-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    @php
        // Récupérer la durée d'expiration depuis la config (en minutes)
        $expirationMinutes = config('password_reset.token_expiration_minutes', 120);

        // Calculer la date d'expiration
        $expirationDate = now()->addMinutes($expirationMinutes);

        // Formater la durée de manière lisible
        $expirationDuration = $expirationMinutes >= 60
            ? floor($expirationMinutes / 60) . ' heure' . (floor($expirationMinutes / 60) > 1 ? 's' : '')
            : $expirationMinutes . ' minutes';
    @endphp

    <div class="container">
        <div class="header">
            <h1>🔐 Réinitialisation de mot de passe</h1>
        </div>

        <p>Bonjour <strong>{{ $user->nom_complet }}</strong>,</p>

        <p>Un administrateur a réinitialisé votre mot de passe. Voici vos nouvelles informations de connexion :</p>

        <div class="password-box">
            <p><strong>Votre nouveau mot de passe temporaire :</strong></p>
            <div class="password-value">{{ $plainPassword }}</div>
        </div>

        <div class="warning">
            <p><strong>⚠️ Important :</strong></p>
            <ul style="margin: 10px 0;">
                <li>Ce mot de passe est temporaire</li>
                <li>Le lien de réinitialisation ci-dessous expire dans <strong>{{ $expirationDuration }}</strong></li>
                <li>Pour des raisons de sécurité, nous vous recommandons vivement de changer ce mot de passe dès votre prochaine connexion</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" class="button">
                Changer mon mot de passe maintenant
            </a>
        </div>

        <div class="info-box">
            <p><strong>ℹ️ Comment procéder :</strong></p>
            <ol style="margin: 10px 0 10px 20px;">
                <li>Connectez-vous avec le mot de passe temporaire ci-dessus</li>
                <li>Ou cliquez directement sur le bouton pour définir un nouveau mot de passe</li>
                <li>Choisissez un mot de passe fort et unique</li>
            </ol>
        </div>

        <p>Si vous n'êtes pas à l'origine de cette demande, veuillez contacter immédiatement votre administrateur système.</p>

        <div class="footer">
            <p>Ce lien expire le : <strong>{{ $expirationDate->format('d/m/Y à H:i') }}</strong></p>
            <p>Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :</p>
            <p style="word-break: break-all; color: #3b82f6;">{{ $resetUrl }}</p>
            <p style="margin-top: 20px;">© {{ date('Y') }} - Système de gestion des appels d'offres</p>
        </div>
    </div>
</body>
</html>
