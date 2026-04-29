{{-- resources/views/emails/sauvegardes/notification.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body      { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
        .wrapper  { max-width:600px; margin:30px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.1); }
        .header   { padding:24px 32px; color:#fff; background:{{ $estSucces ? '#16a34a' : '#dc2626' }}; }
        .header h1{ margin:0; font-size:20px; }
        .body     { padding:28px 32px; color:#374151; }
        .row      { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #e5e7eb; }
        .row:last-child { border-bottom:none; }
        .label    { font-weight:bold; color:#6b7280; font-size:13px; }
        .value    { font-size:14px; }
        .badge    { display:inline-block; padding:2px 10px; border-radius:12px; font-size:13px; font-weight:bold;
                    background:{{ $estSucces ? '#dcfce7' : '#fee2e2' }}; color:{{ $estSucces ? '#15803d' : '#b91c1c' }}; }
        .footer   { padding:16px 32px; background:#f9fafb; font-size:12px; color:#9ca3af; text-align:center; }
        .purge    { margin-top:20px; padding:12px 16px; background:#fef9c3; border-left:4px solid #ca8a04;
                    border-radius:4px; font-size:13px; color:#78350f; }
        .error    { margin-top:20px; padding:12px 16px; background:#fee2e2; border-left:4px solid #dc2626;
                    border-radius:4px; font-size:13px; color:#7f1d1d; word-break:break-word; }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- En-tête --}}
    <div class="header">
        <h1>{{ $estSucces ? '✅ Sauvegarde automatique réussie' : '❌ Échec de la sauvegarde automatique' }}</h1>
        <p style="margin:4px 0 0;font-size:14px;opacity:.9;">{{ now()->format('d/m/Y à H:i:s') }}</p>
    </div>

    {{-- Corps --}}
    <div class="body">
        <p>Bonjour,</p>
        <p>Voici le rapport de la sauvegarde automatique planifiée de la base de données <strong>{{env('APP_NAME') }}</strong>.</p>

        <div style="margin-top:20px;">
            <div class="row">
                <span class="label">Statut</span>
                <span class="value"><span class="badge">{{ ucfirst($sauvegarde->statut) }}</span></span>
            </div>
            <div class="row">
                <span class="label">Fichier</span>
                <span class="value">{{ $sauvegarde->nom_fichier }}</span>
            </div>
            <div class="row">
                <span class="label">Type</span>
                <span class="value">{{ ucfirst($sauvegarde->type) }}</span>
            </div>
            @if($tailleMo !== null)
            <div class="row">
                <span class="label">Taille</span>
                <span class="value">{{ $tailleMo }} Mo</span>
            </div>
            @endif
            @if($sauvegarde->checksum_md5)
            <div class="row">
                <span class="label">Checksum MD5</span>
                <span class="value" style="font-family:monospace;font-size:12px;">{{ $sauvegarde->checksum_md5 }}</span>
            </div>
            @endif
            <div class="row">
                <span class="label">Expiration</span>
                <span class="value">{{ $sauvegarde->expire_a?->format('d/m/Y') ?? '—' }}</span>
            </div>
            <div class="row">
                <span class="label">Horodatage</span>
                <span class="value">{{ $sauvegarde->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>

        {{-- Message d'erreur --}}
        @if(!$estSucces && $sauvegarde->message_erreur)
        <div class="error">
            <strong>Détail de l'erreur :</strong><br>
            {{ $sauvegarde->message_erreur }}
        </div>
        @endif

        {{-- Purge --}}
        @if($nombrePurgees > 0)
        <div class="purge">
            🧹 <strong>{{ $nombrePurgees }}</strong> sauvegarde(s) expirée(s) ont été purgées automatiquement.
        </div>
        @endif
    </div>

    <div class="footer">
        Cet e-mail est généré automatiquement par le système {{env('APP_NAME') }}. Ne pas répondre.
    </div>
</div>
</body>
</html>
