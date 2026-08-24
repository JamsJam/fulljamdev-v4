# Architecture

[Sommaire](README.md) · [Configuration](configuration.md) · [Page builder](page-builder.md)

## Organisation

```text
src/
├── Application/     Cas d’usage regroupés par domaine
│   ├── Page/
│   ├── Reservation/
│   └── Settings/
├── Controller/      Adaptateurs HTTP Front et Dashboard
├── Entity/          Entités Doctrine
├── EventSubscriber/ Événements Symfony
├── Form/            Formulaires transverses
├── Repository/      Accès Doctrine
├── Service/         Services partagés et infrastructure
├── Twig/Components/ Composants Twig réutilisables
└── UI/              Composants UI indépendants du domaine
```

## Règles de responsabilité

- un contrôleur lit la requête, délègue à un service et construit la réponse ;
- le point d’entrée public d’un cas d’usage métier est un service applicatif ;
- un provider lit les données ;
- un persister ou writer les modifie ;
- un repository encapsule Doctrine ;
- un factory construit une entité ;
- un builder construit un DTO complexe ;
- un resolver transforme ou sélectionne une valeur selon le contexte ;
- un proxy ajoute une préoccupation transverse, comme le cache, sans modifier le fournisseur réel.

Flux persistant habituel :

```text
Controller → Service → Provider/Persister/Writer → Repository → Doctrine
```

## Domaines

### Page

Contient les définitions de blocs, éléments réutilisables, DTO, formulaires, registre, mapping, validation des chemins et persistance des pages.

### Reservation

Contient les plannings, disponibilités, indisponibilités, rendez-vous, notifications, rappels et règles du workflow.

### Settings

Contient les sous-domaines `General` et `Account`, le stockage YAML, les caches et les services de lecture/écriture.

### UI

Contient les services indépendants du métier nécessaires aux composants, notamment Calendar et DatePicker.

## Conventions PHP

- code compatible avec Symfony 7.4 ;
- PHP 8.3 ciblé pour le développement ;
- injection par constructeur ;
- classes `final` lorsque l’héritage n’est pas prévu ;
- DTO typés et validation Symfony aux frontières d’entrée ;
- logique métier hors des contrôleurs et templates.
