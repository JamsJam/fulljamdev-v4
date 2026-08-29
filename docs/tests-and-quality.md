# Tests et qualité

[Sommaire](README.md) · [Architecture](architecture.md) · [Déploiement](deployment.md)

## Organisation

Les tests sont regroupés d’abord par domaine, puis par type :

```text
tests/
├── Blog/Application
├── Experience/{Integration,Application}
├── Page/{Unit,Integration}
├── Project/{Integration,Application}
├── Reservation/{Unit,Integration}
├── Settings/{Integration,Application}
├── Shared/Unit
└── UI/Unit
```

## Types de tests

- `Unit` : classe isolée, sans kernel Symfony ;
- `Integration` : combinaison de services avec `KernelTestCase`, conteneur ou infrastructure réelle ;
- `Application` : application complète avec requêtes HTTP et vérification des réponses ;
- E2E : futurs parcours JavaScript/Turbo avec Panther.

Les tests unitaires couvrent les succès, erreurs et limites pertinents.

## Exécution globale

```bash
make test
```

Les suites s’exécutent dans cet ordre : Unit, Integration, Application. La suite suivante ne démarre que si la précédente réussit.

## Exécution par type

```bash
make test-unit
make test-integration
make test-application
```

`make test-integration` :

1. démarre le service MySQL `database` avec Docker Compose et attend qu’il soit prêt ;
2. crée la base MySQL isolée `fulljamdev4_test` ;
3. applique les migrations ;
4. exécute les tests Symfony et Doctrine ;
5. supprime la base de test, même si PHPUnit échoue ;
6. arrête et supprime le conteneur MySQL de test sans supprimer son volume.

Le conteneur MySQL de test est exposé par défaut sur le port hôte `3307` afin de ne pas entrer en conflit avec une base locale sur `3306`. Ce port peut être remplacé avec `TEST_DATABASE_PORT`.

Les tests Doctrine utilisent des transactions afin de rester indépendants.

`make test-application` utilise le même cycle Docker pour permettre aux parcours HTTP d’accéder à une base isolée.

## Exécution par domaine

```bash
make test-page
make test-reservation
make test-settings
make test-shared
make test-ui
```

## Qualité

```bash
# Linters, style et analyse statique
make quality

# Contrôles séparés
make lint
make phpstan
make cs-scan

# Appliquer PHP CS Fixer
make cs-scan DR=0
```

`make lint` vérifie le conteneur Symfony, Twig et YAML.

## Migrations

```bash
make migrate
```

Cette commande lance les linters, simule les migrations puis les applique si la simulation réussit.
