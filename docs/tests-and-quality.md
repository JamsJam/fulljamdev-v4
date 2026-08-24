# Tests et qualité

[Sommaire](README.md) · [Architecture](architecture.md) · [Déploiement](deployment.md)

## Organisation

Les tests sont regroupés d’abord par domaine, puis par type :

```text
tests/
├── Page/{Unit,Integration}
├── Reservation/{Unit,Integration}
├── Settings/{Integration,Application}
├── Shared/Unit
└── UI/Unit
```

## Types de tests

- `Unit` : classe isolée, sans kernel Symfony ;
- `Integration` : combinaison de services avec `KernelTestCase`, conteneur ou infrastructure réelle ;
- `Application` : application complète avec requêtes HTTP ;
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

1. crée la base MySQL isolée `fulljamdev4_test` ;
2. applique les migrations ;
3. exécute les tests Symfony et Doctrine ;
4. supprime la base, même si PHPUnit échoue.

Les tests Doctrine utilisent des transactions afin de rester indépendants.

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
