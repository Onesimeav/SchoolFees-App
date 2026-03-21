<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remboursement confirmé</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #374151; line-height: 1.6; margin: 0; padding: 0; background: #f9fafb; }
        .container { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #059669, #047857); padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; margin: 0 0 4px; }
        .header p { color: #a7f3d0; font-size: 13px; margin: 0; }
        .body { padding: 32px 40px; }
        .greeting { font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 16px; }
        .info-box { background: #ecfdf5; border: 1px solid #6ee7b7; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #a7f3d0; font-size: 13px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #065f46; }
        .info-value { font-weight: 600; color: #064e3b; }
        .notice { font-size: 13px; color: #6b7280; margin-top: 20px; }
        .footer { background: #f3f4f6; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Remboursement confirmé</h1>
        <p>Votre demande de remboursement a été acceptée</p>
    </div>
    <div class="body">
        @php
            $tx   = $refundRequest->transaction;
            $fee  = $tx?->fee;
            $user = $refundRequest->user;
        @endphp

        <p class="greeting">Bonjour {{ $user->name }} {{ $user->surname }},</p>
        <p>
            Votre demande de remboursement pour <strong>{{ $fee?->title ?? 'ce frais' }}</strong>
            a été acceptée et le remboursement a été traité via KKiaPay.
        </p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Intitulé du frais</span>
                <span class="info-value">{{ $fee?->title ?? '—' }}</span>
            </div>
            @if ($fee?->academic_year)
                <div class="info-row">
                    <span class="info-label">Année scolaire</span>
                    <span class="info-value">{{ $fee->academic_year }}</span>
                </div>
            @endif
            <div class="info-row">
                <span class="info-label">Montant remboursé</span>
                <span class="info-value">{{ number_format((int) $tx?->amount, 0, ',', ' ') }} F CFA</span>
            </div>
            <div class="info-row">
                <span class="info-label">Référence KKiaPay</span>
                <span class="info-value">{{ $tx?->kkiapay_reference ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date</span>
                <span class="info-value">{{ now()->format('d/m/Y') }}</span>
            </div>
        </div>

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