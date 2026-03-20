<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu d'inscription</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 13px; color: #1f2937; padding: 40px; }
        .header { text-align: center; margin-bottom: 32px; border-bottom: 3px solid #1d4ed8; padding-bottom: 20px; }
        .header h1 { font-size: 22px; color: #1e3a8a; letter-spacing: 1px; margin-bottom: 4px; }
        .header .subtitle { font-size: 13px; color: #6b7280; }
        .receipt-title { text-align: center; font-size: 18px; font-weight: bold; color: #1d4ed8; margin: 24px 0 20px; text-transform: uppercase; letter-spacing: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { background: #1d4ed8; color: #ffffff; padding: 10px 14px; text-align: left; font-size: 12px; }
        td { padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
        tr:last-child td { border-bottom: none; }
        tr:nth-child(even) td { background: #f9fafb; }
        .label { color: #6b7280; font-weight: 600; width: 40%; }
        .value { color: #111827; font-weight: 500; }
        .amount-value { color: #15803d; font-weight: 700; font-size: 15px; }
        .ref-value { color: #1d4ed8; font-family: monospace; font-size: 12px; }
        .status-badge { display: inline-block; background: #d1fae5; color: #065f46; padding: 3px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; border: 1px solid #6ee7b7; }
        .footer { text-align: center; margin-top: 40px; padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 11px; color: #9ca3af; }
        .generated { text-align: right; font-size: 11px; color: #9ca3af; margin-bottom: 16px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Établissement Scolaire</h1>
    <div class="subtitle">Système de gestion des frais scolaires</div>
</div>

<div class="receipt-title">Reçu d'inscription</div>

<div class="generated">Généré le {{ now()->format('d/m/Y à H:i') }}</div>

<table>
    <tr>
        <th colspan="2">Informations de l'élève</th>
    </tr>
    <tr>
        <td class="label">Nom complet</td>
        <td class="value">{{ $registration->user->name }} {{ $registration->user->surname }}</td>
    </tr>
    <tr>
        <td class="label">Email</td>
        <td class="value">{{ $registration->user->email }}</td>
    </tr>
</table>

<table>
    <tr>
        <th colspan="2">Détails de l'inscription</th>
    </tr>
    <tr>
        <td class="label">Classe</td>
        <td class="value">{{ $registration->grade->name }}</td>
    </tr>
    <tr>
        <td class="label">Année scolaire</td>
        <td class="value">{{ $registration->transaction->fee->academic_year }}</td>
    </tr>
    <tr>
        <td class="label">Statut</td>
        <td class="value"><span class="status-badge">Paiement confirmé</span></td>
    </tr>
</table>

<table>
    <tr>
        <th colspan="2">Détails du paiement</th>
    </tr>
    <tr>
        <td class="label">Montant réglé</td>
        <td class="amount-value">{{ number_format($registration->transaction->amount, 0, ',', ' ') }} F CFA</td>
    </tr>
    <tr>
        <td class="label">Référence KKiaPay</td>
        <td class="ref-value">{{ $registration->transaction->kkiapay_reference }}</td>
    </tr>
    <tr>
        <td class="label">N° Mobile Money</td>
        <td class="value">{{ $registration->transaction->phone_number }}</td>
    </tr>
    <tr>
        <td class="label">Date de paiement</td>
        <td class="value">{{ $registration->transaction->date->format('d/m/Y') }}</td>
    </tr>
</table>

<div class="footer">
    Ce document est généré automatiquement et constitue votre preuve de paiement.<br>
    Conservez-le précieusement.
</div>

</body>
</html>