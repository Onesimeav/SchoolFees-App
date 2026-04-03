<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de remboursement</title>
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
        .amount-value { color: #047857; font-weight: 700; font-size: 15px; }
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

<div class="receipt-title">Reçu de remboursement</div>

<div class="generated">Généré le {{ now()->format('d/m/Y à H:i') }}</div>

@php
    $tx   = $refundRequest->transaction;
    $fee  = $tx?->fee;
    $user = $refundRequest->user;
@endphp

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
        <th colspan="2">Frais remboursé</th>
    </tr>
    <tr>
        <td class="label">Intitulé</td>
        <td class="value">{{ $fee?->title ?? '—' }}</td>
    </tr>
    @if ($fee?->grade)
        <tr>
            <td class="label">Classe</td>
            <td class="value">{{ $fee->grade->name }}</td>
        </tr>
    @endif
    @if ($fee?->academic_year)
        <tr>
            <td class="label">Année scolaire</td>
            <td class="value">{{ $fee->academic_year }}</td>
        </tr>
    @endif
    <tr>
        <td class="label">Référence KKiaPay</td>
        <td class="ref-value">{{ $tx?->kkiapay_reference ?? '—' }}</td>
    </tr>
    <tr>
        <td class="label">Date du paiement initial</td>
        <td class="value">{{ $tx?->date?->format('d/m/Y') ?? '—' }}</td>
    </tr>
    <tr>
        <td class="label">Date du remboursement</td>
        <td class="value">{{ now()->format('d/m/Y') }}</td>
    </tr>
</table>

<table>
    <tr>
        <th>Description</th>
        <th>Montant</th>
    </tr>
    <tr class="total-row">
        <td class="label">Montant remboursé</td>
        <td class="amount-value">{{ number_format((int) $tx?->amount, 0, ',', ' ') }} F CFA</td>
    </tr>
</table>

<div class="footer">
    Ce document confirme le remboursement de votre paiement.<br>
    Conservez-le précieusement.
</div>

</body>
</html>