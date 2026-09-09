# Installation et environnement

[Sommaire](README.md) · [Configuration](configuration.md)

## Prérequis

- PHP 8.3 avec `pdo_mysql` ;
- Composer ;
- MySQL 8.0.32 ;
- Symfony CLI recommandé ;
- Docker Compose facultatif ;
- GNU Make.

`composer.json` accepte actuellement PHP 8.2 ou supérieur, mais le projet est développé avec PHP 8.3.

```bash
composer check-platform-reqs
php -v
php -m | grep pdo_mysql
```

## Installer les dépendances

```bash
composer install
```

Les recettes Composer installent également les fichiers nécessaires à AssetMapper.

## Configurer MySQL

Les valeurs partagées sont dans `.env`. Les secrets et adaptations propres à la machine vont dans `.env.local` ou `.env.dev.local`.

```dotenv
DATABASE_URL="mysql://root:root@127.0.0.1:3306/fulljamdev4?serverVersion=8.0.32&charset=utf8mb4"
```

## Utiliser Docker

```bash
docker compose up -d database mailer
```

| Service | Adresse | Utilisation |
|---|---|---|
| MySQL | `127.0.0.1:3306` | base `fulljamdev4`, compte `root`/`root` |
| Mailpit SMTP | `127.0.0.1:1025` | réception des emails de développement |
| Mailpit UI | `http://127.0.0.1:8025` | inspection et débogage des emails |

Pour utiliser Mailpit :

```dotenv
# .env.dev.local
MAILER_DSN=smtp://127.0.0.1:1025
```

Mailpit permet d’inspecter le HTML, le texte brut, les en-têtes, les pièces jointes et la source complète d’un email.

```bash
docker compose down
```

## Créer la base

```bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
```

Charger les fixtures de réservation :

```bash
php bin/console doctrine:fixtures:load
```

Cette dernière commande purge les données existantes.

## Démarrer Symfony

```bash
symfony server:start -d
symfony server:status
```

Arrêter le serveur :

```bash
symfony server:stop
```
