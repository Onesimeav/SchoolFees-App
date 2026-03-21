<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de frais général</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #374151; line-height: 1.6; margin: 0; padding: 0; background: #f9fafb; }
        .container { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #ea580c, #c2410c); padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; margin: 0 0 4px; }
        .header p { color: #fed7aa; font-size: 13px; margin: 0; }
        .body { padding: 32px 40px; }
        .greeting { font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 16px; }
        .info-box { background: #fff7ed; border: 1px solid #fdba74; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #fed7aa; font-size: 13px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #9a3412; }
        .info-value { font-weight: 600; color: #7c2d12; }
        .fine-text { color: #dc2626; font-size: 12px; }
        .notice { font-size: 13px; color: #6b7280; margin-top: 20px; }
        .footer { background: #f3f4f6; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Reçu de frais général</h1>
        <p>Confirmation de votre paiement</p>
    </div>
    <div class="body">
        <p class="greeting">Bonjour {{ $user->name }} {{ $user->surname }},</p>
        <p>
            Votre paiement pour <strong>{{ $fee->title }}</strong>
            (année scolaire <strong>{{ $fee->academic_year }}</strong>) a bien été enregistré.
        </p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Intitulé</span>
                <span class="info-value">{{ $fee->title }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Classe</span>
                <span class="info-value">{{ $fee->grade?->name ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Année scolaire</span>
                <span class="info-value">{{ $fee->academic_year }}</span>
            </div>
            @php
                $base = (int) $fee->total_amount;
                $fine = (int) $transaction->amount - $base;
            @endphp
            <div class="info-row">
                <span class="info-label">Montant de base</span>
                <span class="info-value">{{ number_format($base, 0, ',', ' ') }} F CFA</span>
            </div>
            @if ($fine > 0)
                <div class="info-row">
                    <span class="info-label">Amende de retard</span>
                    <span class="info-value fine-text">+{{ number_format($fine, 0, ',', ' ') }} F CFA</span>
                </div>
            @endif
            <div class="info-row">
                <span class="info-label">Total réglé</span>
                <span class="info-value">{{ number_format((int) $transaction->amount, 0, ',', ' ') }} F CFA</span>
            </div>
            <div class="info-row">
                <span class="info-label">Référence KKiaPay</span>
                <span class="info-value">{{ $transaction->kkiapay_reference }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date de paiement</span>
                <span class="info-value">{{ $transaction->date?->format('d/m/Y') ?? '—' }}</span>
            </div>
        </div>

        <p>Votre reçu officiel est disponible en pièce jointe (PDF).</p>

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