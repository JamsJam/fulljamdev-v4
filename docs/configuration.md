# Configuration applicative

[Sommaire](README.md) · [Installation](installation.md) · [Architecture](architecture.md)

## Variables d’environnement

Les principales variables sont :

```dotenv
APP_ENV=dev
APP_SECRET=
DATABASE_URL="mysql://root:root@127.0.0.1:3306/fulljamdev4?serverVersion=8.0.32&charset=utf8mb4"
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
MAILER_DSN=null://null
GOOGLE_CALENDAR_CLIENT_ID=
GOOGLE_CALENDAR_CLIENT_SECRET=
GOOGLE_CALENDAR_REFRESH_TOKEN=
GOOGLE_CALENDAR_ID=primary
```

Ne jamais placer de secret réel dans `.env`. Utiliser `.env.local`, `.env.dev.local` ou les variables du système de déploiement.

## Réglages YAML

La configuration administrable est stockée dans `src/Config/config.yaml` :

- `parameters` : paramètres généraux et fuseau horaire ;
- `account` : informations publiques du compte ;
- `pages` : identifiant de la page d’accueil.

Flux de lecture :

```text
Controller → Service → Proxy → Cache → Provider → YamlSettingsStorage
```

Flux d’écriture :

```text
Controller → Service → Writer → YamlSettingsStorage → invalidation du cache
```

Clés de cache :

- `cache.setting.GeneralSettingsDto` ;
- `cache.setting.AccountSettingsDto`.

Le `YamlParserService` centralise la lecture et l’écriture atomique. Les providers convertissent les tableaux YAML en DTO typés.

## Page d’accueil

La page choisie dans les réglages généraux est rendue sur `/`. Si son chemin public explicite est appelé, le subscriber redirige vers `/` avec un statut `301 Moved Permanently`.

## Google Calendar

Lorsque les quatre variables Google Calendar sont renseignées, un événement avec lien Google Meet peut être créé pour le rendez-vous. Sans configuration, la réservation continue normalement sans appel à Google.
