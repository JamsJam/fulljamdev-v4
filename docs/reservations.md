# Réservations

[Sommaire](README.md) · [Architecture](architecture.md) · [Tests](tests-and-quality.md)

## Sous-domaines

- `Planner` : configuration des plannings ;
- `Availability` : disponibilités hebdomadaires ;
- `Unavailability` : périodes indisponibles ;
- `Appointment` : demandes, rendez-vous, notifications et rappels ;
- `UI/Calendar` et `UI/DatePicker` : construction des DTO calendaires.

## Parcours public

Les routes `/book-meeting/{slug}` permettent de sélectionner une date, un créneau et les informations de contact. Les étapes dynamiques répondent avec des Turbo Streams.

## Dashboard

Le dashboard permet notamment :

- de voir les demandes et rendez-vous à venir ;
- de confirmer ou refuser une demande ;
- d’annuler ou reprogrammer un rendez-vous ;
- de signaler une absence ;
- de marquer un rendez-vous comme réalisé ;
- d’ajouter un compte rendu ;
- de configurer les plannings et disponibilités.

## Workflow

Le workflow `appointment` est défini dans `config/packages/workflow.yaml`.

```text
requested ──confirm──> confirmed ──mark_held──> occurred ──complete──> complete
    │                     │
    ├──reject──> rejected ├──no_show──> no_show
    └──cancel─────────────┴──cancel───> cancelled

proposed ──confirm──> confirmed
    ├──reject──> rejected
    └──cancel──> cancelled
```

La transition `reschedule` conserve l’état `confirmed`. Les guards empêchent de marquer prématurément un rendez-vous futur comme réalisé ou absent.

## Notifications et rappels

Les notifications sont séparées des services de création. Les rappels sont transmis via Messenger avec des délais adaptés à leur type.

Google Calendar est facultatif. Lorsque sa configuration est absente, aucun appel externe n’est effectué.
