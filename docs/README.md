# Documentation

## Sommaire

| Guide | Contenu |
|---|---|
| [Installation et environnement](installation.md) | Prérequis, installation, MySQL, Docker, Mailpit et démarrage local |
| [Configuration applicative](configuration.md) | Variables d’environnement, réglages YAML, cache et Google Calendar |
| [Architecture](architecture.md) | Organisation du code, responsabilités des couches et flux applicatifs |
| [Constructeur de pages](page-builder.md) | Pages, chemins, blocs, CTA, images et ajout d’un composant |
| [Réservations](reservations.md) | Plannings, disponibilités, rendez-vous et workflow |
| [Front-end et assets](frontend.md) | AssetMapper, Sass, Twig Components, Turbo et Stimulus |
| [Tests et qualité](tests-and-quality.md) | Suites PHPUnit, base de test, PHPStan, CS Fixer et linters |
| [Workflow Git](git-workflow.md) | Branches, Conventional Commits Angular et pull requests |
| [Déploiement](deployment.md) | Compilation, migrations, secrets et workflows GitHub Actions |

## Principes du projet

- le point d’entrée d’un cas d’usage métier est un service applicatif ;
- les contrôleurs restent consacrés à HTTP ;
- les tests sont regroupés d’abord par domaine, puis par type ;
- la structure d’un bloc appartient au composant, son apparence commune au thème ;
- les secrets ne sont jamais enregistrés dans le dépôt.

[Retour au README principal](../README.md)
