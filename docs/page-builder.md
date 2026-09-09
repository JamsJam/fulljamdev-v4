# Constructeur de pages

[Sommaire](README.md) · [Architecture](architecture.md) · [Front-end](frontend.md)

## Modèle

Les pages utilisent les tables `content_page` et `content_page_block`. Une page possède :

- un titre ;
- un chemin public ;
- des métadonnées SEO ;
- une collection ordonnée de blocs.

Un même type de bloc peut être ajouté plusieurs fois.

## Chemins publics

Le chemin est enregistré sans slash initial :

```text
a-propos
services
services/developpement
projets/mon-projet
```

Le contrôleur générique `app_front_page` possède une priorité faible. Les routes explicites restent prioritaires. `PagePathValidator` teste aussi les variantes avec et sans slash final afin d’empêcher les collisions.

Les routes du dashboard, de réservation et les autres routes applicatives sont donc interdites comme chemins de page.

## Page d’accueil

La page rendue sur `/` est choisie dans les réglages généraux. Son ancien chemin explicite redirige définitivement vers `/`.

## Blocs

Chaque définition implémente `BlockDefinitionInterface` et indique notamment :

- son type stable, par exemple `hero.main` ;
- son libellé et sa catégorie ;
- la classe du DTO ;
- le formulaire Symfony associé ;
- les données initiales.

`BlockRegistry` découvre et regroupe les définitions. `BlockDataMapper` convertit les données JSON vers les DTO typés et inversement. `BlockAssetProcessor` traite les fichiers avant l’écriture.

## Ajouter un bloc

Créer au minimum :

1. un DTO ;
2. un `FormType` Symfony ;
3. une définition de bloc ;
4. un Twig Component ;
5. son template ;
6. ses styles structurels ;
7. des tests unitaires et d’intégration.

La structure et les variantes sont intrinsèques au composant. Les couleurs, typographies, rayons et espacements communs proviennent du thème.

## Éléments partagés

Les éléments Page réutilisables comprennent :

- titres et niveaux de titre ;
- textes ;
- images par URL ou média ;
- CTA ;
- badges ;
- attributs HTML sous forme de collection clé/valeur.

Les attributs dangereux sont filtrés. Les URL rendues sont également sécurisées.

## CTA

Un CTA peut cibler :

- une URL libre ;
- une route Symfony existante avec ses paramètres.

Les routes proposées dans l’interface sont classées entre routes publiques et routes du dashboard.
