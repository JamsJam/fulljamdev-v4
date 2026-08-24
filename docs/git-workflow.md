# Workflow Git

[Sommaire](README.md) · [Tests et qualité](tests-and-quality.md)

## Branches

- `main` : production stable ;
- `develop` : branche d’intégration ;
- branche dédiée : développement d’une fonctionnalité ou correction.

Exemples :

```text
feat/page-builder
fix/reservation-calendar
refactor/settings-cache
test/page-integration
docs/readme
```

Toujours créer la branche depuis `develop` :

```bash
git checkout develop
git pull origin develop
git checkout -b feat/ma-fonctionnalite
```

## Commits Angular

Le titre, le corps et le footer suivent Conventional Commits avec la nomenclature Angular :

```text
type(scope): description courte à l’impératif

Corps facultatif expliquant le pourquoi et les conséquences.

Footer facultatif.
```

Types usuels :

- `feat` : fonctionnalité ;
- `fix` : correction ;
- `refactor` : restructuration sans changement fonctionnel ;
- `test` : tests ;
- `docs` : documentation ;
- `style` : formatage ;
- `perf` : performance ;
- `build` : construction ou dépendances ;
- `ci` : intégration continue ;
- `chore` : maintenance ;
- `revert` : annulation.

Exemple :

```text
test(page): couvre la persistance avec Doctrine

Vérifie l’enregistrement, la recherche et l’unicité des chemins
avec les vrais services du conteneur Symfony.

Refs: #42
```

## Avant la pull request

```bash
git checkout develop
git pull origin develop
git checkout <branche>
git merge develop
make quality
make test
git push
```

Résoudre les éventuels conflits, vérifier le diff puis ouvrir une pull request vers `develop`. Ne pas travailler directement sur `main` ou `develop`.
