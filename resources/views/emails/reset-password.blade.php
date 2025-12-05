<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
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
            background: linear-gradient(135deg, rgb(5, 39, 89) 0%, rgb(3, 25, 60) 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .header-icon {
            width: 80px;
            height: 80px;
            background-color: #ffffff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .content {
            padding: 40px 30px;
            color: #374151;
        }
        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: rgb(5, 39, 89);
            margin-bottom: 20px;
        }
        .message {
            font-size: 15px;
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, rgb(5, 39, 89) 0%, rgb(3, 25, 60) 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(5, 39, 89, 0.3);
            transition: all 0.3s ease;
        }
        .button:hover {
            box-shadow: 0 6px 20px rgba(5, 39, 89, 0.4);
            transform: translateY(-2px);
        }
        .info-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin: 25px 0;
            font-size: 14px;
            color: #92400e;
        }
        .divider {
            border: 0;
            height: 1px;
            background: #e5e7eb;
            margin: 30px 0;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .footer a {
            color: rgb(5, 39, 89);
            text-decoration: none;
            font-weight: 500;
        }
        .link-box {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-top: 25px;
            word-break: break-all;
            font-size: 13px;
            color: #6b7280;
        }
        .security-notice {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            border-radius: 8px;
            margin: 25px 0;
            font-size: 14px;
            color: #991b1b;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 20px;
                border-radius: 12px;
            }
            .header {
                padding: 30px 20px;
            }
            .content {
                padding: 30px 20px;
            }
            .header h1 {
                font-size: 24px;
            }
            .button {
                padding: 14px 30px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 17H11V15H13V17ZM13 13H11V7H13V13Z" fill="rgb(5, 39, 89)"/>
                </svg>
            </div>
            <h1>Réinitialisation de mot de passe</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Bonjour {{ $userName }},
            </div>

            <div class="message">
                Vous avez demandé la réinitialisation de votre mot de passe pour votre compte <strong>{{ config('app.name') }}</strong>.
            </div>

            <div class="message">
                Pour créer un nouveau mot de passe, cliquez sur le bouton ci-dessous :
            </div>

            <div class="button-container">
                <a href="{{ $resetLink }}" class="button">
                    🔒 Réinitialiser mon mot de passe
                </a>
            </div>

            <div class="info-box">
                <strong>⏰ Attention :</strong> Ce lien expirera dans <strong>{{ $expiresIn }} minutes</strong>. Après ce délai, vous devrez faire une nouvelle demande de réinitialisation.
            </div>

            <div class="security-notice">
                <strong>🛡️ Sécurité :</strong> Si vous n'avez pas demandé cette réinitialisation, ignorez cet email. Votre mot de passe actuel reste inchangé et sécurisé.
            </div>

            <hr class="divider">

            <div class="message" style="font-size: 13px;">
                <strong>Le bouton ne fonctionne pas ?</strong><br>
                Copiez et collez ce lien dans votre navigateur :
            </div>

            <div class="link-box">
                <a href="{{ $resetLink }}" style="color: rgb(5, 39, 89); word-break: break-all;">{{ $resetLink }}</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                Cet email a été envoyé par <strong>{{ config('app.name') }}</strong>
            </p>
            <p style="margin: 0;">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
            </p>
            <p style="margin: 15px 0 0 0;">
                <a href="{{ config('app.url') }}">Visiter notre site web</a>
            </p>
        </div>
    </div>
</body>
</html>
