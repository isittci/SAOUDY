<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Code de vérification - {{ config('app.name') }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* Reset */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        /* Base */
        body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #f4f7fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #374151;
        }

        /* Container */
        .email-wrapper {
            width: 100%;
            background-color: #f4f7fa;
            padding: 40px 20px;
        }

        .email-container {
            max-width: 520px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #c2410c 100%);
            padding: 40px 30px;
            text-align: center;
        }

        .logo-circle {
            width: 70px;
            height: 70px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon {
            font-size: 32px;
            color: #ffffff;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .email-header p {
            margin: 8px 0 0;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        /* Body */
        .email-body {
            padding: 40px 36px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 16px;
        }

        .message-text {
            font-size: 15px;
            color: #4b5563;
            margin: 0 0 32px;
            line-height: 1.7;
        }

        /* Code Box */
        .code-section {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border-radius: 12px;
            padding: 28px;
            text-align: center;
            margin-bottom: 28px;
            border: 1px solid #fed7aa;
        }

        .code-label {
            font-size: 12px;
            font-weight: 600;
            color: #9a3412;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 0 0 12px;
        }

        .verification-code {
            font-family: 'SF Mono', 'Fira Code', 'Consolas', monospace;
            font-size: 38px;
            font-weight: 700;
            letter-spacing: 10px;
            color: #ea580c;
            margin: 0;
            padding: 8px 0;
            background: transparent;
        }

        .code-expiry {
            font-size: 13px;
            color: #b45309;
            margin: 12px 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .expiry-icon {
            font-size: 14px;
        }

        /* Info Box */
        .info-box {
            background-color: #f0fdf4;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 20px;
            border-left: 4px solid #22c55e;
        }

        .info-box-title {
            font-size: 13px;
            font-weight: 600;
            color: #166534;
            margin: 0 0 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box-text {
            font-size: 14px;
            color: #15803d;
            margin: 0;
            line-height: 1.5;
        }

        /* Warning Box */
        .warning-box {
            background-color: #fefce8;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 28px;
            border-left: 4px solid #eab308;
        }

        .warning-box-title {
            font-size: 13px;
            font-weight: 600;
            color: #a16207;
            margin: 0 0 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .warning-list {
            margin: 0;
            padding-left: 18px;
            font-size: 13px;
            color: #854d0e;
            line-height: 1.8;
        }

        .warning-list li {
            margin-bottom: 4px;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
            margin: 28px 0;
        }

        /* Help Section */
        .help-section {
            text-align: center;
            padding: 0 20px;
        }

        .help-text {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
        }

        .help-link {
            color: #ea580c;
            text-decoration: none;
            font-weight: 600;
        }

        .help-link:hover {
            text-decoration: underline;
        }

        /* Footer */
        .email-footer {
            background-color: #f9fafb;
            padding: 28px 36px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer-brand {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin: 0 0 4px;
        }

        .footer-tagline {
            font-size: 13px;
            color: #9ca3af;
            margin: 0 0 20px;
        }

        .footer-meta {
            font-size: 11px;
            color: #9ca3af;
            margin: 0;
            line-height: 1.8;
        }

        .footer-email {
            color: #6b7280;
            font-weight: 500;
        }

        /* Social Links */
        .social-links {
            margin: 16px 0;
        }

        .social-link {
            display: inline-block;
            width: 36px;
            height: 36px;
            background-color: #f3f4f6;
            border-radius: 50%;
            margin: 0 4px;
            line-height: 36px;
            text-align: center;
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .social-link:hover {
            background-color: #ea580c;
            color: #ffffff;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 20px 12px;
            }
            .email-container {
                border-radius: 12px;
            }
            .email-header {
                padding: 32px 24px;
            }
            .email-header h1 {
                font-size: 20px;
            }
            .email-body {
                padding: 28px 24px;
            }
            .verification-code {
                font-size: 28px;
                letter-spacing: 6px;
            }
            .code-section {
                padding: 20px;
            }
            .email-footer {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td align="center">
                    <div class="email-container">
                        <!-- Header -->
                        <div class="email-header">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <div class="logo-circle">
                                            <span class="logo-icon">🔐</span>
                                        </div>
                                        <h1>{{ config('app.name') }}</h1>
                                        <p>Vérification de votre identité</p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Body -->
                        <div class="email-body">
                            <p class="greeting">Bonjour {{ $user->nom_complet ?? $partenaire->nom_complet }},</p>

                            <p class="message-text">
                                Une tentative de connexion à votre compte a été détectée. Pour confirmer votre identité et finaliser la connexion, veuillez saisir le code de vérification ci-dessous :
                            </p>

                            <!-- Code Section -->
                            <div class="code-section">
                                <p class="code-label">Votre code de vérification</p>
                                <p class="verification-code">{{ $code }}</p>
                                <p class="code-expiry">
                                    <span class="expiry-icon">⏱️</span>
                                    Expire dans <strong>{{ $expiresIn }} minutes</strong>
                                </p>
                            </div>

                            <!-- Info Box -->
                            <div class="info-box">
                                <p class="info-box-title">
                                    <span>✓</span> Comment utiliser ce code
                                </p>
                                <p class="info-box-text">
                                    Saisissez ce code à 6 chiffres dans le champ de vérification sur la page de connexion pour accéder à votre compte.
                                </p>
                            </div>

                            <!-- Warning Box -->
                            <div class="warning-box">
                                <p class="warning-box-title">
                                    <span>⚠️</span> Conseils de sécurité
                                </p>
                                <ul class="warning-list">
                                    <li>Ne partagez <strong>jamais</strong> ce code avec qui que ce soit</li>
                                    <li>Notre équipe ne vous demandera jamais ce code</li>
                                    <li>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email et changez votre mot de passe</li>
                                </ul>
                            </div>

                            <div class="divider"></div>

                            <!-- Help Section -->
                            <div class="help-section">
                                <p class="help-text">
                                    Besoin d'aide ? <a href="mailto:{{ env('SUPPORT_MAIL') }}" class="help-link">Contactez notre support</a>
                                </p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="email-footer">
                            <p class="footer-brand">{{ config('app.name') }}</p>
                            <p class="footer-tagline">Sécurité • Confiance • Excellence</p>

                            <p class="footer-meta">
                                Cet email a été envoyé à <span class="footer-email">{{ $user->email ?? $partenaire->email }}</span><br>
                                © {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
                            </p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
