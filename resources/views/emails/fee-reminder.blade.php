<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $type === 'near_due' ? 'Rappel de paiement' : 'Frais en retard' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #374151; line-height: 1.6; margin: 0; padding: 0; background: #f9fafb; }
        .container { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header-near  { background: linear-gradient(135deg, #d97706, #b45309); padding: 32px 40px; text-align: center; }
        .header-past  { background: linear-gradient(135deg, #dc2626, #991b1b); padding: 32px 40px; text-align: center; }
        .header-near h1, .header-past h1 { color: #ffffff; font-size: 22px; margin: 0 0 4px; }
        .header-near p  { color: #fde68a; font-size: 13px; margin: 0; }
        .header-past p  { color: #fecaca; font-size: 13px; margin: 0; }
        .body    { padding: 32px 40px; }
        .greeting { font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 16px; }
        .info-box-near { background: #fffbeb; border: 1px solid #fcd34d; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .info-box-past { background: #fef2f2; border: 1px solid #fca5a5; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid rgba(0,0,0,.06); font-size: 13px; }
        .info-row:last-child { border-bottom: none; }
        .info-label-near { color: #92400e; }
        .info-label-past { color: #991b1b; }
        .info-value-near { font-weight: 600; color: #78350f; }
        .info-value-past { font-weight: 600; color: #7f1d1d; }
        .urgency-near { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 10px 14px; border-radius: 4px; font-size: 13px; color: #92400e; margin: 16px 0; }
        .urgency-past { background: #fee2e2; border-left: 4px solid #ef4444; padding: 10px 14px; border-radius: 4px; font-size: 13px; color: #991b1b; margin: 16px 0; }
        .btn-near { display: inline-block; background: #d97706; color: #ffffff; font-size: 14px; font-weight: 600; padding: 12px 28px; border-radius: 6px; text-decoration: none; margin-top: 8px; }
        .btn-past { display: inline-block; background: #dc2626; color: #ffffff; font-size: 14px; font-weight: 600; padding: 12px 28px; border-radius: 6px; text-decoration: none; margin-top: 8px; }
        .notice { font-size: 13px; color: #6b7280; margin-top: 20px; }
        .footer { background: #f3f4f6; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">

    @if($type === 'near_due')
    <div class="header-near">
        <h1>Rappel de paiement</h1>
        <p>Échéance dans 7 jours</p>
    </div>
    @else
    <div class="header-past">
        <h1>Frais en retard</h1>
        <p>La date limite est dépassée</p>
    </div>
    @endif

    <div class="body">
        <p class="greeting">Bonjour {{ $user->name }} {{ $user->surname }},</p>

        @if($type === 'near_due')
            <p>Nous vous rappelons que le paiement suivant arrive à échéance <strong>dans 7 jours</strong>.</p>
            <div class="urgency-near">
                ⏰ Réglez ce frais avant le <strong>{{ $dueDate }}</strong> pour éviter tout pénalité de retard.
            </div>
        @else
            <p>La date limite de paiement pour le frais suivant est <strong>dépassée</strong>.</p>
            <div class="urgency-past">
                ⚠️ Ce frais était dû le <strong>{{ $dueDate }}</strong>. Veuillez régulariser votre situation dès que possible.
            </div>
        @endif

        <div class="{{ $type === 'near_due' ? 'info-box-near' : 'info-box-past' }}">
            <div class="info-row">
                <span class="info-label-{{ $type === 'near_due' ? 'near' : 'past' }}">Frais</span>
                <span class="info-value-{{ $type === 'near_due' ? 'near' : 'past' }}">
                    {{ $feeTitle }}{{ $installmentNumber ? ' — Versement n°' . $installmentNumber : '' }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label-{{ $type === 'near_due' ? 'near' : 'past' }}">Classe</span>
                <span class="info-value-{{ $type === 'near_due' ? 'near' : 'past' }}">{{ $gradeName }}</span>
            </div>
            <div class="info-row">
                <span class="info-label-{{ $type === 'near_due' ? 'near' : 'past' }}">Année scolaire</span>
                <span class="info-value-{{ $type === 'near_due' ? 'near' : 'past' }}">{{ $academicYear }}</span>
            </div>
            <div class="info-row">
                <span class="info-label-{{ $type === 'near_due' ? 'near' : 'past' }}">Montant</span>
                <span class="info-value-{{ $type === 'near_due' ? 'near' : 'past' }}">{{ number_format($amount, 0, ',', ' ') }} F CFA</span>
            </div>
            <div class="info-row">
                <span class="info-label-{{ $type === 'near_due' ? 'near' : 'past' }}">Date limite</span>
                <span class="info-value-{{ $type === 'near_due' ? 'near' : 'past' }}">{{ $dueDate }}</span>
            </div>
        </div>

        @if($portalUrl)
        <p style="text-align: center;">
            <a href="{{ $portalUrl }}" class="btn-{{ $type === 'near_due' ? 'near' : 'past' }}">
                Procéder au paiement →
            </a>
        </p>
        @endif

        <p class="notice">
            Si vous avez déjà effectué ce paiement, veuillez ignorer ce message.
            Pour toute question, contactez l'administration de l'établissement.
        </p>
    </div>

    <div class="footer">
        Cet email a été envoyé automatiquement, merci de ne pas y répondre.
    </div>
</div>
</body>
</html>