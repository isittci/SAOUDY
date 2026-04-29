<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur {{ $appName }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            padding: 30px 40px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 10px 0 0 0;
            font-size: 14px;
        }
        .content {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .message {
            color: #4b5563;
            margin-bottom: 30px;
        }
        .credentials-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
        }
        .credentials-box h3 {
            color: #92400e;
            margin: 0 0 20px 0;
            font-size: 16px;
            display: flex;
            align-items: center;
        }
        .credentials-box h3::before {
            content: "🔐";
            margin-right: 10px;
        }
        .credential-item {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            border-left: 4px solid #f97316;
        }
        .credential-item:last-child {
            margin-bottom: 0;
        }
        .credential-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .credential-value {
            font-size: 16px;
            color: #1f2937;
            font-weight: 600;
            word-break: break-all;
        }
        .warning-box {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 15px;
            margin: 25px 0;
            display: flex;
            align-items: flex-start;
        }
        .warning-box::before {
            content: "⚠️";
            margin-right: 12px;
            font-size: 18px;
        }
        .warning-box p {
            margin: 0;
            color: #991b1b;
            font-size: 14px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 35px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(249, 115, 22, 0.3);
        }
        .button:hover {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
        }
        .info-section {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .info-section h4 {
            color: #374151;
            margin: 0 0 15px 0;
            font-size: 14px;
        }
        .info-section ul {
            margin: 0;
            padding-left: 20px;
            color: #6b7280;
            font-size: 14px;
        }
        .info-section li {
            margin-bottom: 8px;
        }
        .role-badge {
            display: inline-block;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: #ffffff;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .footer {
            background-color: #1f2937;
            padding: 30px 40px;
            text-align: center;
        }
        .footer p {
            color: #9ca3af;
            margin: 0 0 10px 0;
            font-size: 13px;
        }
        .footer .app-name {
            color: #f97316;
            font-weight: 600;
        }
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
            margin: 25px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $appName }}</h1>
            <p>District Autonome de Yamoussoukro</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">Bonjour <strong>{{ $user->nom_complet }}</strong>,</p>

            <p class="message">
                Nous avons le plaisir de vous informer qu'un compte utilisateur a été créé pour vous sur la plateforme
                <strong>{{ $appName }}</strong>. Vous pouvez désormais accéder à l'application et commencer à utiliser
                les fonctionnalités qui vous sont attribuées.
            </p>

            <!-- Credentials Box -->
            <div class="credentials-box">
                <h3>Vos identifiants de connexion</h3>

                <div class="credential-item">
                    <div class="credential-label">Adresse email</div>
                    <div class="credential-value">{{ $user->email }}</div>
                </div>

                <div class="credential-item">
                    <div class="credential-label">Mot de passe temporaire</div>
                    <div class="credential-value">{{ $plainPassword }}</div>
                </div>

                @if($user->role)
                <div class="credential-item">
                    <div class="credential-label">Rôle attribué</div>
                    <div class="credential-value">
                        <span class="role-badge">{{ $user->role->name }}</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Warning -->
            <div class="warning-box">
                <p>
                    <strong>Important :</strong> Pour des raisons de sécurité, nous vous recommandons fortement de
                    modifier votre mot de passe dès votre première connexion.
                </p>
            </div>

            <!-- Login Button -->
            <div class="button-container">
                <a href="{{ $loginUrl }}" class="button">
                    Se connecter maintenant
                </a>
            </div>

            <div class="divider"></div>

            <!-- Info Section -->
            <div class="info-section">
                <h4>🚀 Premiers pas sur {{ $appName }}</h4>
                <ul>
                    <li>Connectez-vous avec les identifiants ci-dessus</li>
                    <li>Changez votre mot de passe depuis votre profil</li>
                    <li>Explorez les fonctionnalités disponibles selon votre rôle</li>
                    <li>Contactez l'administrateur en cas de problème</li>
                </ul>
            </div>

            @if($createdBy)
            <p style="color: #6b7280; font-size: 14px; margin-top: 25px;">
                <em>Compte Enregistré par : {{ $createdBy->nom_complet }}</em>
            </p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Cet email a été envoyé automatiquement par <span class="app-name">{{ $appName }}</span></p>
            <p>© {{ date('Y') }} District Autonome de Yamoussoukro. Tous droits réservés.</p>
            <p style="margin-top: 15px; font-size: 11px;">
                Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet email ou contacter l'administrateur.
            </p>
        </div>
    </div>
</body>
</html>
