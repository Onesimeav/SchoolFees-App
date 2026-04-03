# Portail Étudiant — Gestion des frais scolaires

Application web permettant aux établissements scolaires de gérer les frais et aux élèves/parents de payer en ligne via mobile money. Elle comprend trois espaces distincts : un **portail étudiant** pour les paiements en libre-service, un **espace personnel** pour le personnel administratif, et un **panneau administrateur** pour la gestion complète avec tableaux de bord analytiques.

![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-5-FDB900?logo=laravel&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15-4169E1?logo=postgresql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-ready-2496ED?logo=docker&logoColor=white)

---

## Fonctionnalités

### Portail étudiant — `/portal`
- Inscription avec **vérification par code OTP par email**
- **Inscription en classe** — soumettre une demande d'inscription et payer les frais via KKiaPay mobile money
- **Paiement de la scolarité** — régler les versements individuellement avec calcul des pénalités de retard
- **Paiement des frais généraux** — régler les frais obligatoires et optionnels
- Reçu PDF envoyé par email après chaque paiement réussi
- **Demandes de remboursement** — soumettre une demande de remboursement pour toute transaction
- Réinitialisation du mot de passe par code OTP

### Espace personnel — `/staff`
- Créer et gérer les frais (frais d'inscription, scolarité avec versements, frais généraux)
- Examiner et accepter/refuser les demandes d'inscription en classe
- Traiter et confirmer les demandes de remboursement — déclenche l'envoi d'un reçu PDF par email
- Consulter toutes les transactions et télécharger les reçus
- Accès basé sur les rôles : comptable, secrétaire, employé

### Panneau administrateur — `/`
- Toutes les fonctionnalités de l'espace personnel
- **Gestion des utilisateurs** — créer des comptes, assigner des rôles, filtrer par rôle
- **Tableaux de bord analytiques** — KPIs de revenus, graphique des revenus par type de frais, graphique des utilisateurs par rôle, widgets transactions/inscriptions/remboursements récents
- Réinitialisation du mot de passe et gestion du profil

---

## Stack technique

| Couche | Technologie |
|--------|-------------|
| Backend | Laravel 12, PHP 8.4 |
| Interface admin | Filament 5 |
| Frontend | Tailwind CSS 4, Vite 7 |
| Base de données | PostgreSQL 15 |
| Stockage de fichiers | Supabase (compatible S3) |
| Paiements | KKiaPay (mobile money — bac à sable) |
| Génération PDF | DomPDF |
| Email | Envoi asynchrone via Gmail SMTP ou Brevo |
| File d'attente | Laravel database queue + worker Supervisord |
| Déploiement | Docker sur Render |

---

## Installation locale

### Prérequis

- PHP 8.4+ avec les extensions : `pdo_pgsql`, `gd`, `zip`, `intl`, `mbstring`, `bcmath`, `pcntl`, `exif`
- Composer 2
- Node.js 22+ et npm
- PostgreSQL 15+

### Étapes

```bash
# 1. Cloner le dépôt
git clone <repo-url>
cd SchoolFeesApp

# 2. Copier le fichier d'environnement local et renseigner les valeurs requises
#    (voir la section Variables d'environnement ci-dessous)
cp .env.local.example .env.local

# 3. Installer les dépendances, générer la clé, exécuter les migrations, compiler le frontend
composer run setup

# 4. Alimenter la base de données avec les données de démonstration
php artisan db:seed

# 5. Démarrer tous les processus de développement en parallèle
#    (serveur web · worker de file · visionneur de logs · Vite)
composer run dev
```

L'application sera accessible sur **http://localhost:8000**.

> La commande `composer run dev` démarre quatre processus simultanément : `php artisan serve`, `php artisan queue:work`, `php artisan pail` (visionneur de logs) et `npm run dev` (Vite HMR).

---

## Comptes de démonstration

Tous les comptes de démonstration utilisent le mot de passe : **`password`**

| Rôle | Email | Espace |
|------|-------|--------|
| Administrateur | admin@schoolfees.com | http://localhost:8000/ |
| Comptable | accountant@schoolfees.com | http://localhost:8000/staff |
| Secrétaire | secretary@schoolfees.com | http://localhost:8000/staff |
| Employé | employee@schoolfees.com | http://localhost:8000/staff |
| Étudiant | alice.student@schoolfees.com | http://localhost:8000/portal |
| Étudiant | bob.scholar@schoolfees.com | http://localhost:8000/portal |

**Numéro de test KKiaPay (bac à sable) :** `97000000`
Utiliser ce numéro dans n'importe quelle fenêtre de paiement pour simuler un paiement mobile money réussi.

---

## Variables d'environnement

Copier `.env.local.example` vers `.env.local` pour le développement local, ou `.env.prod.example` vers `.env` pour la production. Renseigner les valeurs adaptées à votre environnement.

### Application
```env
APP_NAME="Portail Etudiant"
APP_ENV=local            # local | production
APP_KEY=                 # généré par : php artisan key:generate
APP_DEBUG=true           # false en production
APP_URL=http://localhost
APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
```

### Base de données (PostgreSQL)
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nom_de_la_base
DB_USERNAME=utilisateur
DB_PASSWORD=mot_de_passe
DB_SCHEMA=public
```

### Email
```env
MAIL_MAILER=smtp          # utiliser "brevo" en production sur Render (voir note ci-dessous)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre@gmail.com
MAIL_PASSWORD=votre_mot_de_passe_application_gmail
MAIL_FROM_ADDRESS=votre@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

> **Note production :** Le forfait gratuit de Render bloque les connexions SMTP sortantes sur le port 587. Utiliser [Brevo](https://brevo.com) à la place — offre gratuite (300 emails/jour), aucun nom de domaine requis, vérification par adresse email uniquement. Définir `MAIL_MAILER=brevo` et `BREVO_KEY=votre-cle-api-brevo`.

### KKiaPay — paiements mobile money
Obtenir les clés sur [kkiapay.me](https://kkiapay.me).
```env
KKIAPAY_PUBLIC_KEY=votre_cle_publique
KKIAPAY_PRIVATE_KEY=votre_cle_privee
KKIAPAY_SECRET_KEY=votre_cle_secrete
KKIAPAY_SANDBOX=true     # false en production
```

### Supabase — stockage de fichiers (reçus PDF)
Obtenir les identifiants S3 depuis votre projet Supabase → **Storage → Identifiants S3**.
```env
SUPABASE_S3_KEY=votre_cle_acces_s3
SUPABASE_S3_SECRET=votre_secret_s3
SUPABASE_S3_BUCKET=nom_du_bucket
SUPABASE_S3_ENDPOINT=https://votre-ref-projet.supabase.co/storage/v1/s3
```

### File d'attente et cron
```env
QUEUE_CONNECTION=database
CRON_SECRET=une_chaine_aleatoire_longue   # authentifie l'endpoint HTTP du cron
```

---

## Déploiement en production (Render)

Le projet inclut un fichier `render.yaml` et un `Dockerfile` multi-étapes prêts pour le déploiement.

### Étapes

1. Pousser le dépôt sur GitHub ou GitLab.
2. Créer un nouveau **Web Service** sur [Render](https://render.com), connecter le dépôt et sélectionner **Docker** comme runtime.
3. Dans le tableau de bord Render, ajouter les variables d'environnement secrètes suivantes (absentes volontairement de `render.yaml`) :
   - `APP_KEY`
   - `DATABASE_URL` (chaîne de connexion PostgreSQL)
   - `MAIL_MAILER`, `MAIL_*` ou `BREVO_KEY`
   - `KKIAPAY_PUBLIC_KEY`, `KKIAPAY_PRIVATE_KEY`, `KKIAPAY_SECRET_KEY`
   - `SUPABASE_S3_KEY`, `SUPABASE_S3_SECRET`, `SUPABASE_S3_BUCKET`, `SUPABASE_S3_ENDPOINT`
   - `CRON_SECRET`
4. Déployer. Le script de démarrage (`docker/start.sh`) s'exécute automatiquement :
   - Installation des dépendances Composer
   - Mise en cache de la configuration, des routes et des vues
   - Exécution des migrations
   - Démarrage de **Supervisord**, qui gère trois processus :
     - `nginx` — serveur web
     - `php-fpm` — PHP FastCGI
     - `queue-worker` — `php artisan queue:work` (traitement des emails en file d'attente)

### Cron — rappels d'échéances

La commande `php artisan fees:send-reminders` envoie des emails de rappel 7 jours avant et 1 jour après chaque échéance de frais impayés. Configurer un service cron externe (ex. [cron-job.org](https://cron-job.org)) pour appeler l'URL suivante **une fois par jour** :

```
GET https://votre-app.onrender.com/cron/send-fee-reminders?token=VOTRE_CRON_SECRET
```

---

## Lancer les tests

```bash
composer run test
# ou
php artisan test
```

---

## Structure du projet

```
app/
├── Console/Commands/       # Commande artisan fees:send-reminders
├── Filament/               # Panneau admin (ressources, widgets, pages d'auth)
├── Filament/Portal/        # Portail étudiant (pages, ressources, auth)
├── Filament/Staff/         # Espace personnel (ressources, pages d'auth)
├── Mail/                   # 8 classes Mailable
├── Models/                 # Modèles Eloquent
│   └── Fee, TuitionFee, RegistrationFee, GeneralFee
│   └── Transaction, Installment, ClassRegistration
│   └── RefundRequest, VerificationCode, Grade, User
└── Providers/Filament/     # Configuration des panneaux

database/
├── migrations/             # 16 migrations
└── seeders/                # Données de démonstration avec reçus PDF sur Supabase

docker/
├── nginx.conf.template     # Configuration Nginx avec templating du port
├── start.sh                # Script de démarrage du conteneur
└── supervisord.conf        # Supervisord : nginx + php-fpm + queue-worker

resources/views/
├── emails/                 # Templates d'emails (8 vues Blade)
└── pdf/                    # Templates de reçus PDF (4 vues Blade)

tests/
└── Feature/Notifications/  # FeeReminderTest (14 tests)
```