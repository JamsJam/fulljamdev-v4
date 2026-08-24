# Fulljamdev Portfolio

Application Symfony du site Fulljamdev. Elle regroupe le site public, un dashboard d’administration, un constructeur de pages par blocs et un système complet de réservation.

## Fonctionnalités principales

- pages publiques composées de blocs réutilisables ;
- chemins racines ou imbriqués avec détection des conflits de routes ;
- page d’accueil configurable ;
- gestion des plannings, disponibilités et rendez-vous ;
- interfaces dynamiques avec Turbo Frames, Turbo Streams et Stimulus ;
- réglages YAML mis en cache ;
- notifications et intégration facultative à Google Calendar.

## Stack

PHP 8.3, Symfony 7.4, Doctrine ORM 3, MySQL 8, Twig, Symfony UX, Turbo 8, Stimulus 3, AssetMapper et SassBundle.

## Prérequis

- PHP 8.3 avec `pdo_mysql` ;
- Composer ;
- MySQL 8.0.32 ou Docker Compose ;
- Symfony CLI recommandé ;
- GNU Make pour les commandes automatisées.

## Installation

```bash
composer install
docker compose up -d database mailer
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
```

Les valeurs spécifiques à la machine et les secrets doivent être placés dans `.env.local` ou `.env.dev.local`.

## Configuration minimale

Configuration MySQL locale :

```dotenv
DATABASE_URL="mysql://root:root@127.0.0.1:3306/fulljamdev4?serverVersion=8.0.32&charset=utf8mb4"
```

Pour inspecter les emails dans Mailpit :

```dotenv
MAILER_DSN=smtp://127.0.0.1:1025
```

L’interface Mailpit est accessible sur `http://127.0.0.1:8025`.

## Lancer l’application

```bash
symfony server:start -d
```

Afficher l’état ou arrêter le serveur :

```bash
symfony server:status
symfony server:stop
```

## Tests et qualité

```bash
# Unitaires, intégration, puis applicatifs
make test

# Linters, PHP CS Fixer et PHPStan
make quality
```

La suite d’intégration crée `fulljamdev4_test`, applique les migrations, exécute les tests puis supprime automatiquement cette base.

## Architecture courte

```text
src/
├── Application/     Services et logique regroupés par domaine
├── Controller/      Entrées HTTP Front et Dashboard
├── Entity/          Entités Doctrine
├── Repository/      Accès aux données
├── Service/         Infrastructure partagée
├── Twig/Components/ Composants Twig réutilisables
└── UI/              Builders, resolvers et DTO d’interface
```

Le flux applicatif habituel est :

```text
Controller → Service → Provider/Persister/Writer → Repository
```

## Documentation

Le sommaire complet se trouve dans [docs/README.md](docs/README.md).

1. [Installation et environnement](docs/installation.md)
2. [Configuration applicative](docs/configuration.md)
3. [Architecture](docs/architecture.md)
4. [Constructeur de pages](docs/page-builder.md)
5. [Réservations](docs/reservations.md)
6. [Front-end et assets](docs/frontend.md)
7. [Tests et qualité](docs/tests-and-quality.md)
8. [Workflow Git](docs/git-workflow.md)
9. [Déploiement](docs/deployment.md)

## Commandes essentielles

```bash
make test
make quality
make migrate
make test-page
make test-reservation
make test-settings
```

## Contribution

Les branches partent de `develop` et les commits suivent Conventional Commits avec la nomenclature Angular :

```text
feat(page): ajoute un nouveau bloc
fix(reservation): corrige le calcul des créneaux
test(settings): couvre le cache de configuration
docs(readme): complète l’installation
```

## Licence

Projet propriétaire. Toute utilisation, reproduction ou redistribution nécessite l’autorisation du propriétaire.
