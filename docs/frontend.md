# Front-end et assets

[Sommaire](README.md) · [Page builder](page-builder.md)

## Technologies

- Twig ;
- Symfony UX Twig Components ;
- Turbo 8 ;
- Stimulus 3 ;
- AssetMapper ;
- SassBundle.

Aucun bundler Node n’est nécessaire pour l’exécution actuelle.

## Points d’entrée

- `assets/app.js` : application publique commune ;
- `assets/home.js` : page d’accueil ;
- `assets/admin.js` : dashboard ;
- `assets/controllers/` : contrôleurs Stimulus ;
- `assets/styles/` : styles Sass.

## Turbo

Turbo est utilisé pour :

- charger les sections des réglages ;
- afficher les dialogues de rendez-vous et de planning ;
- actualiser le calendrier et les créneaux ;
- soumettre le parcours de réservation ;
- mettre à jour les formulaires sans rechargement complet.

Les réponses serveur dynamiques utilisent le type `text/vnd.turbo-stream.html` et ciblent des Frames ou Streams identifiés explicitement.

## Composants de formulaire

Les champs réutilisables s’appuient sur les Twig Components du dossier `src/Twig/Components/Form`. Les nouveaux formulaires doivent réutiliser ces composants afin d’éviter de redéfinir le design des inputs.

Les attributs HTML éditables utilisent une collection de paires clé/valeur.

## Sass et production

```bash
# Compilation ponctuelle
php bin/console sass:build

# Mode développement
php bin/console sass:build --watch

# Compilation AssetMapper pour la production
php bin/console asset-map:compile
```
