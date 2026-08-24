# Déploiement

[Sommaire](README.md) · [Installation](installation.md) · [Tests et qualité](tests-and-quality.md)

## Préparation

Avant un déploiement :

```bash
composer install --no-dev --optimize-autoloader
php bin/console asset-map:compile --env=prod
php bin/console cache:clear --env=prod
php bin/console doctrine:migrations:migrate --env=prod --no-interaction
```

Les migrations doivent être vérifiées dans un environnement comparable avant leur application en production.

## Variables et secrets

Fournir au minimum :

- `APP_ENV=prod` ;
- `APP_SECRET` ;
- `DATABASE_URL` vers MySQL ;
- `MAILER_DSN` ;
- `MESSENGER_TRANSPORT_DSN` ;
- les variables Google Calendar si l’intégration est activée.

Les secrets ne doivent jamais apparaître dans `.env`, les logs ou le dépôt.

## GitHub Actions

Les workflows existants sont :

- `.github/workflows/deploydevelop.yml` pour l’environnement de développement ;
- `.github/workflows/deploymain.yml` pour la production.

Ils récupèrent notamment `APP_SECRET`, `DATABASE_URL`, `MAILER_DSN` et `MESSENGER_TRANSPORT_DSN` depuis les secrets GitHub.

## Vérifications après déploiement

```bash
php bin/console about --env=prod
php bin/console doctrine:migrations:status --env=prod
php bin/console debug:container --env=prod
```

Vérifier ensuite la page d’accueil, une page construite, le parcours de réservation, l’envoi d’un email et l’accès sécurisé au dashboard.
