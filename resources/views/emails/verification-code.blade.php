<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de vérification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
        }
        .code-container {
            background-color: #fff;
            border: 2px dashed #3498db;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #3498db;
            font-family: 'Courier New', monospace;
        }
        .info {
            background-color: #e8f4f8;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Code de vérification</p>
        </div>

        <p>Bonjour <strong>{{ $user->nom_complet ?? $partenaire->nom_complet }}</strong>,</p>

        <p>Vous avez demandé à vous connecter à votre compte. Pour finaliser votre connexion, veuillez utiliser le code de vérification ci-dessous :</p>

        <div class="code-container">
            <div class="code">{{ $code }}</div>
        </div>

        <div class="info">
            <strong>ℹ️ Information importante :</strong>
            <p>Ce code est valable pendant <strong>{{ $expiresIn }} minutes</strong>.</p>
        </div>

        <div class="warning">
            <strong>⚠️ Sécurité :</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Ne partagez jamais ce code avec qui que ce soit</li>
                <li>Notre équipe ne vous demandera jamais ce code par téléphone ou email</li>
                <li>Si vous n'avez pas demandé ce code, ignorez cet email</li>
            </ul>
        </div>

        <p>Si vous avez des questions ou besoin d'aide, n'hésitez pas à nous contacter.</p>

        <div class="footer">
            <p>Cordialement,<br>L'équipe {{ config('app.name') }}</p>
            <p style="margin-top: 20px;">
                Cet email a été envoyé à {{ $user->email ?? $partenaire->email }}<br>
                © {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
            </p>
        </div>
    </div>
</body>
</html>
