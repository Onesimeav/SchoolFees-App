<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de scolarité</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #374151; line-height: 1.6; margin: 0; padding: 0; background: #f9fafb; }
        .container { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #059669, #047857); padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; margin: 0 0 4px; }
        .header p { color: #a7f3d0; font-size: 13px; margin: 0; }
        .body { padding: 32px 40px; }
        .greeting { font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 16px; }
        .info-box { background: #f0fdf4; border: 1px solid #86efac; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #bbf7d0; font-size: 13px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #15803d; }
        .info-value { font-weight: 600; color: #14532d; }
        .installment-table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 13px; }
        .installment-table th { background: #d1fae5; color: #065f46; padding: 8px 12px; text-align: left; }
        .installment-table td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; }
        .fine-text { color: #dc2626; font-size: 12px; }
        .total-row td { font-weight: 700; background: #f0fdf4; border-top: 2px solid #6ee7b7; }
        .notice { font-size: 13px; color: #6b7280; margin-top: 20px; }
        .footer { background: #f3f4f6; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Reçu de scolarité</h1>
        <p>Confirmation de votre paiement</p>
    </div>
    <div class="body">
        <p class="greeting">Bonjour {{ $user->name }} {{ $user->surname }},</p>
        <p>
            Votre paiement de frais de scolarité pour la classe <strong>{{ $fee->grade?->name ?? $fee->title }}</strong>
            (année scolaire <strong>{{ $fee->academic_year }}</strong>) a bien été enregistré.
        </p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Classe</span>
                <span class="info-value">{{ $fee->grade?->name ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Année scolaire</span>
                <span class="info-value">{{ $fee->academic_year }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Référence KKiaPay</span>
                <span class="info-value">{{ $transactions->first()?->kkiapay_reference ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date de paiement</span>
                <span class="info-value">{{ $transactions->first()?->date?->format('d/m/Y') ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total réglé</span>
                <span class="info-value">{{ number_format($transactions->sum('amount'), 0, ',', ' ') }} F CFA</span>
            </div>
        </div>

        <p>Versements couverts par ce paiement :</p>
        <table class="installment-table">
            <thead>
                <tr>
                    <th>N° versement</th>
                    <th>Montant</th>
                    <th>Amende</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach ($transactions as $tx)
                    @php
                        $inst      = $tx->installment ?? null;
                        $base      = $inst ? (int) $inst->amount : (int) $tx->amount;
                        $fine      = (int) $tx->amount - $base;
                        $grandTotal += (int) $tx->amount;
                    @endphp
                    <tr>
                        <td>Versement N°{{ $inst?->number ?? '—' }}</td>
                        <td>{{ number_format($base, 0, ',', ' ') }} F CFA</td>
                        <td>
                            @if ($fine > 0)
                                <span class="fine-text">+{{ number_format($fine, 0, ',', ' ') }} F CFA</span>
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ number_format((int) $tx->amount, 0, ',', ' ') }} F CFA</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3">Total</td>
                    <td>{{ number_format($grandTotal, 0, ',', ' ') }} F CFA</td>
                </tr>
            </tbody>
        </table>

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