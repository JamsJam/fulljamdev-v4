# Guide : Workflow GitHub et Gestion des Branches (Actionnable)

## 🎯 Objectif

Ce guide fournit **les actions concrètes à suivre** pour travailler proprement avec GitHub et éviter les conflits.

---

## 🌿 🧭 Schéma de branches

```
main
  │
  └───┐
      │
   develop
      │
      ├── feature/like
      ├── feature/login
      └── feature/register
```

### Rôles :

* `main` → production (stable)
* `develop` → intégration (doit etre stable)
* `feature/*` → développement 

---

## 🚀 🔁 Workflow COMPLET (Étapes à suivre)

### 1. Creer les branches 

A partir de main on cree la branche développe si ce n'est pas déja fait:

```bash
git checkout -b develop
git push -u origin develop  #mise en ligne de la branche develop
```
Si la branche est deja en ligne, il suffit de se déplacer sur la branche la récupérer

C'est a partir de cette branche que les branche de développement seront créer

---

### 2. 🌱 Créer une branche de développement

```bash
git checkout -b feature/ma-feature
```

👉 Toujours partir de `develop`

Le nom de la branche est arbittraire.


---

### 3. 💻 Développer

👉 Faire des commits petits et fréquents !

```bash
git add .
git commit -m "feat: ajout de ma feature"
```

---

### 4. 🔄 Se resynchroniser régulièrement

👉  A faire obligatoirement avant de push !

```bash
git checkout develop # se déplacer sur develop
git pull origin develop # mettre a jour developp

git checkout feature/ma-feature # se déplacer sur la branche de développement
git merge develop # Amener le code de dévelope sur la branche en cours
```


---

### 5. Gérer les conflits

Si des conflits apparaissent, il faudra choisir entre le code existant sur la branche et le code entrant

```
<<<<<<< HEAD
Votre code
=======
le code entrant
>>>>>>> branche
```
### Actions :

1. Modifier le fichier
2. Supprimer les marqueurs
3. Garder le bon code

Puis :

```bash
git add .
git commit
```

---

### 6. 📤 Push la branche

Si c'est le premier push sur cette branche : 
```bash
    git push origin feature/ma-feature
```
sinon : 

```bash
    git push 
```
---

### 6. 🔍 Créer une Pull Request

* Aller sur GitHub
* Créer une PR vers `develop`
* Vérifier les conflits
* Attendre review

Si les points précédents ont bien été respecter, il ne devrait pas y avoir de conflits, sinon répéter les étapes du `point 5`
---


### 7. ✅ Merge

* Une fois validée → Merge dans `develop`

---

## 🔥 Règles IMPORTANTES

* ❌ Ne jamais commit sur `main`
* ❌ Ne jamais travailler directement sur `develop`
* ✅ Toujours passer par une branche
* ✅ Toujours pull avant de travailler
* ✅ Toujours sync avant PR




*Fin du guide*
