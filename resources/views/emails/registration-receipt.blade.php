<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu d'inscription</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #374151; line-height: 1.6; margin: 0; padding: 0; background: #f9fafb; }
        .container { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #1d4ed8, #3730a3); padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; margin: 0 0 4px; }
        .header p { color: #bfdbfe; font-size: 13px; margin: 0; }
        .body { padding: 32px 40px; }
        .greeting { font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 16px; }
        .info-box { background: #f0fdf4; border: 1px solid #86efac; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #bbf7d0; font-size: 13px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #15803d; }
        .info-value { font-weight: 600; color: #14532d; }
        .notice { font-size: 13px; color: #6b7280; margin-top: 20px; }
        .footer { background: #f3f4f6; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Reçu d'inscription</h1>
        <p>Confirmation de votre paiement</p>
    </div>
    <div class="body">
        <p class="greeting">Bonjour {{ $registration->user->name }} {{ $registration->user->surname }},</p>
        <p>
            Votre inscription en <strong>{{ $registration->grade->name }}</strong>
            pour l'année scolaire <strong>{{ $registration->transaction->fee->academic_year }}</strong>
            a bien été enregistrée et votre paiement a été confirmé.
        </p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Classe</span>
                <span class="info-value">{{ $registration->grade->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Montant payé</span>
                <span class="info-value">{{ number_format($registration->transaction->amount, 0, ',', ' ') }} F CFA</span>
            </div>
            <div class="info-row">
                <span class="info-label">Référence KKiaPay</span>
                <span class="info-value">{{ $registration->transaction->kkiapay_reference }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date de paiement</span>
                <span class="info-value">{{ $registration->transaction->date->format('d/m/Y') }}</span>
            </div>
        </div>

        <p>Votre reçu officiel est disponible en pièce jointe (PDF).</p>
        <p>Votre dossier sera examiné par l'équipe administrative. Vous serez notifié(e) dès qu'une décision sera prise.</p>

        <p class="notice">
            Si vous avez des questions, veuillez contacter l'administration de l'établissement.
        </p>
    </div>
    <div class="footer">
        Cet email a été envoyé automatiquement, merci de ne pas y répondre.
    </div>
</div>
</body>
</html>