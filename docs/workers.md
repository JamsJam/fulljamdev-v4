# Workers

[Sommaire](README.md) · [Déploiement](deployment.md)

Chaque famille de tâches dispose de sa propre file Doctrine Messenger. Un incident ou un pic sur une file ne bloque donc pas les autres flux.

| File | Responsabilité | Démarrage | Nombre initial |
|---|---|---|---:|
| `scheduler_articles` | Déclenche la publication d’articles | `make worker-scheduler-articles` | 1 |
| `scheduler_page_assets` | Déclenche le nettoyage des images de blocs | `make worker-scheduler-page-assets` | 1 |
| `article_publication` | Publie ou déprogramme les articles à échéance | `make worker-articles` | 1 |
| `page_asset_cleanup` | Supprime quotidiennement les images de blocs orphelines | `make worker-page-assets` | 1 |
| `appointment_reminders` | Traite les rappels J-1 et H-1 de rendez-vous | `make worker-reminders` | 1 à 2 |
| `emails` | Envoie les emails transactionnels et de rappel | `make worker-emails` | 1 à 2 |

Les workers `scheduler_articles` et `scheduler_page_assets` ne réalisent pas les tâches métier : ils les placent dans leur file dédiée. Ils doivent être actifs pour que leur tâche planifiée respective soit déclenchée.

## Mise à l’échelle

Lancer deux instances de la même commande crée deux consommateurs concurrents de la même file. Messenger garantit qu’un message est pris par un seul des deux consommateurs.

Commencer avec un worker par file, puis doubler uniquement `appointment_reminders` ou `emails` si leur attente augmente. Garder une seule instance pour les schedulers, `article_publication` et `page_asset_cleanup` : leurs volumes sont faibles et certaines opérations modifient les mêmes données ou fichiers.

Un gestionnaire de processus doit maintenir ces commandes en vie et les relancer après un arrêt. Lors d’un déploiement, demander leur arrêt propre avec :

```bash
php bin/console messenger:stop-workers --env=prod
```

Le gestionnaire les redémarrera avec le nouveau code. Les messages qui échouent après leurs tentatives sont disponibles dans la file `failed` :

```bash
php bin/console messenger:failed:show --env=prod
php bin/console messenger:failed:retry --env=prod
```
