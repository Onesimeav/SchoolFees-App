<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Bienvenue</title></head>
<body style="font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 2rem;">
    <h2>Bienvenue, {{ $user->name }} {{ $user->surname }} !</h2>

    <p>Un compte a été créé pour vous. Voici vos identifiants :</p>

    <table style="border-collapse: collapse; width: 100%; margin: 1rem 0;">
        <tr>
            <td style="padding: 0.5rem 1rem; background: #f5f5f5; font-weight: bold;">Adresse email</td>
            <td style="padding: 0.5rem 1rem;">{{ $user->email }}</td>
        </tr>
        <tr>
            <td style="padding: 0.5rem 1rem; background: #f5f5f5; font-weight: bold;">Mot de passe</td>
            <td style="padding: 0.5rem 1rem;">{{ $rawPassword }}</td>
        </tr>
    </table>

    @php
        $roleName = $user->roles->first()?->name;
        $dashboardUrl = match($roleName) {
            'admin' => url('/admin'),
            'parent_student' => url('/portal'),
            default => url('/staff'),
        };
    @endphp

    <p>
        <a href="{{ $dashboardUrl }}"
           style="display: inline-block; background: #f59e0b; color: #fff; padding: 0.75rem 1.5rem; border-radius: 0.375rem; text-decoration: none; font-weight: bold;">
            Accéder à mon espace
        </a>
    </p>

    <p style="color: #666; font-size: 0.875rem;">
        Pour des raisons de sécurité, nous vous recommandons de changer votre mot de passe lors de votre première connexion.
    </p>
</body>
</html>