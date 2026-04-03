<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation de votre mot de passe</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 500px; margin: 40px auto; background: #fff; border-radius: 8px; padding: 32px; }
        .code { font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #1d4ed8; text-align: center; margin: 24px 0; }
        .footer { font-size: 12px; color: #9ca3af; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bonjour {{ $name }},</h2>
        <p>Voici votre code de réinitialisation de mot de passe :</p>
        <div class="code">{{ $otp }}</div>
        <p>Ce code est valable pendant <strong>15 minutes</strong>.</p>
        <p>Si vous n'avez pas demandé de réinitialisation, ignorez cet email.</p>
        <div class="footer">{{ config('app.name') }}</div>
    </div>
</body>
</html>