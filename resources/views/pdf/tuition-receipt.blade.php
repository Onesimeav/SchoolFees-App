<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de scolarité</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 13px; color: #1f2937; padding: 40px; }
        .header { text-align: center; margin-bottom: 32px; border-bottom: 3px solid #059669; padding-bottom: 20px; }
        .header h1 { font-size: 22px; color: #065f46; letter-spacing: 1px; margin-bottom: 4px; }
        .header .subtitle { font-size: 13px; color: #6b7280; }
        .receipt-title { text-align: center; font-size: 18px; font-weight: bold; color: #059669; margin: 24px 0 20px; text-transform: uppercase; letter-spacing: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { background: #059669; color: #ffffff; padding: 10px 14px; text-align: left; font-size: 12px; }
        td { padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
        tr:last-child td { border-bottom: none; }
        tr:nth-child(even) td { background: #f9fafb; }
        .label { color: #6b7280; font-weight: 600; width: 40%; }
        .value { color: #111827; font-weight: 500; }
        .amount-value { color: #15803d; font-weight: 700; font-size: 15px; }
        .fine-value { color: #dc2626; font-size: 12px; }
        .ref-value { color: #059669; font-family: monospace; font-size: 12px; }
        .total-row td { font-weight: 700; background: #d1fae5; }
        .footer { text-align: center; margin-top: 40px; padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 11px; color: #9ca3af; }
        .generated { text-align: right; font-size: 11px; color: #9ca3af; margin-bottom: 16px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Établissement Scolaire</h1>
    <div class="subtitle">Système de gestion des frais scolaires</div>
</div>

<div class="receipt-title">Reçu de scolarité</div>

<div class="generated">Généré le {{ now()->format('d/m/Y à H:i') }}</div>

<table>
    <tr>
        <th colspan="2">Informations de l'élève</th>
    </tr>
    <tr>
        <td class="label">Nom complet</td>
        <td class="value">{{ $user->name }} {{ $user->surname }}</td>
    </tr>
    <tr>
        <td class="label">Email</td>
        <td class="value">{{ $user->email }}</td>
    </tr>
</table>

<table>
    <tr>
        <th colspan="2">Détails des frais</th>
    </tr>
    <tr>
        <td class="label">Classe</td>
        <td class="value">{{ $fee->grade?->name ?? '—' }}</td>
    </tr>
    <tr>
        <td class="label">Année scolaire</td>
        <td class="value">{{ $fee->academic_year }}</td>
    </tr>
    <tr>
        <td class="label">Référence KKiaPay</td>
        <td class="ref-value">{{ $transactions->first()?->kkiapay_reference ?? '—' }}</td>
    </tr>
    <tr>
        <td class="label">N° Mobile Money</td>
        <td class="value">{{ $transactions->first()?->phone_number ?? '—' }}</td>
    </tr>
    <tr>
        <td class="label">Date de paiement</td>
        <td class="value">{{ $transactions->first()?->date?->format('d/m/Y') ?? '—' }}</td>
    </tr>
</table>

<table>
    <tr>
        <th>N° versement</th>
        <th>Montant de base</th>
        <th>Amende retard</th>
        <th>Total versement</th>
    </tr>
    @php $grandTotal = 0; @endphp
    @foreach ($transactions as $tx)
        @php
            $inst  = $installments[$tx->installment_id] ?? null;
            $base  = $inst ? (int) $inst->amount : (int) $tx->amount;
            $fine  = (int) $tx->amount - $base;
            $grandTotal += (int) $tx->amount;
        @endphp
        <tr>
            <td>Versement N°{{ $inst?->number ?? '—' }}</td>
            <td>{{ number_format($base, 0, ',', ' ') }} F CFA</td>
            <td class="fine-value">{{ $fine > 0 ? '+' . number_format($fine, 0, ',', ' ') . ' F CFA' : '—' }}</td>
            <td class="amount-value">{{ number_format((int) $tx->amount, 0, ',', ' ') }} F CFA</td>
        </tr>
    @endforeach
    <tr class="total-row">
        <td colspan="3" class="label">Total réglé</td>
        <td class="amount-value">{{ number_format($grandTotal, 0, ',', ' ') }} F CFA</td>
    </tr>
</table>

<div class="footer">
    Ce document est généré automatiquement et constitue votre preuve de paiement.<br>
    Conservez-le précieusement.
</div>

</body>
</html>