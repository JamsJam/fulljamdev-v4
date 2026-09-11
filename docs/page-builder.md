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


## Contributions JSON-LD des blocs

`BuiltPageContextProvider` collecte les contributions des blocs de la page affichée
avec `BlockJsonLdCollector`. Chaque service implémentant
`BlockJsonLdContributorInterface` est automatiquement enregistré avec le tag
`app.json_ld.block_contributor`. `PageContext` reçoit uniquement des
`JsonLdContribution` contenant des données publiques, sans entités ni DTO de blocs.
`JsonLdBuilder` fait assembler ces contributions avec la définition de page et le
fil d’Ariane par `ContributionGraphAssembler`.

Blocs pris en charge :

- `faq.main` : questions et réponses complètes, dédupliquées à l’échelle de la page.
  La page reçoit aussi le type `FAQPage` et les questions comme `mainEntity`.
  Si elle possède déjà une entité principale (profil), celle-ci est conservée et
  les questions sont reliées par `mentions`, sans ajouter `FAQPage`.
- `blog.latest` : `ItemList` des articles effectivement affichés, avec références
  `BlogPosting` utilisant les mêmes identifiants que les pages articles.
- `project.featured` : `ItemList` des projets publiés sélectionnés, représentés
  par des `CreativeWork`. Ce bloc porte seul la source dynamique des projets.
- `services.main` : liste statique de cartes décrites comme des `Service`.
- `card_display.with_image` : liste statique de cartes. Une carte liée par une
  route article ou projet est typée en conséquence ; les autres restent des
  `Thing`.

Les listes sont reliées à la page par `mentions` : afficher une liste d’articles
ne transforme pas la page en article. Les identifiants des services sont propres
aux cartes ; un bouton vers une page contact commune ne fusionne pas les services.
Les sélections des blocs dynamiques d’articles et de projets sont partagées avec Twig et mémorisées
sur la requête courante, pour garder les mêmes contenus et éviter une seconde
requête SQL. Les pages `noindex` ne déclenchent pas la collecte.

Pour ajouter un contributeur :

1. Implémenter `supports(PageBlockDTO)` pour les types et DTO compatibles.
2. Produire une `JsonLdContribution` dans `contribute()`, avec des nœuds identifiés
   par `@id` et les identifiants à relier à la page dans `mentions`.
3. Utiliser l’identifiant absolu du bloc fourni par le collecteur pour les objets
   locaux, et l’identité canonique de l’objet pour les contenus partagés.
4. Réutiliser la source de l’affichage ; ne publier que les données visibles.
5. Tester le graphe, les blocs vides et les interactions avec les autres blocs.

Aucun bloc pricing n’existe actuellement. Aucune `Offer` n’est déduite d’un texte
ou d’un montant isolé. Un futur bloc tarifaire devra porter explicitement le
service ou produit, le prix, la devise et les conditions nécessaires.
Les blocs de mise en page, CTA, planning et chronologie n’ajoutent pas de schéma
spécialisé sur la seule base de leur présentation.

Références : [ItemList](https://schema.org/ItemList),
[FAQPage](https://schema.org/FAQPage), [mentions](https://schema.org/mentions).
