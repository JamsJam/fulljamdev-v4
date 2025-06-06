#!/bin/bash

# Dernier tag version (ex: v1.2.3)
LAST_TAG=$(git describe --tags --abbrev=0)

if [ -z "$(git log "$LAST_TAG"..HEAD --oneline)" ]; then
  echo "🚫 Aucun nouveau commit depuis le dernier tag ($LAST_TAG). Le changelog ne sera pas mis à jour."
  exit 0
fi

# Date actuelle
DATE=$(date +%F)

# Supprimer le préfixe "v" si présent (ex: v1.2.3 → 1.2.3)
RAW_VERSION=$(echo "$LAST_TAG" | sed 's/^v//')
IFS='.' read -r MAJOR MINOR PATCH <<< "$RAW_VERSION"

# Flags pour déterminer le type de changement
HAS_BREAKING=false
HAS_FEAT=false
HAS_FIX=false

# Analyser les types de commit depuis le dernier tag
git log "$LAST_TAG"..HEAD --pretty=format:"%s%n%b" | while IFS= read -r line; do
  if echo "$line" | grep -qi "BREAKING CHANGE"; then
    HAS_BREAKING=true
  elif echo "$line" | grep -qE '^feat:|^add:'; then
    HAS_FEAT=true
  elif echo "$line" | grep -qE '^fix:'; then
    HAS_FIX=true
  fi
done

# Appliquer la logique de versioning sémantique
if $HAS_BREAKING; then
  ((MAJOR++))
  MINOR=0
  PATCH=0
elif $HAS_FEAT; then
  ((MINOR++))
  PATCH=0
elif $HAS_FIX; then
  ((PATCH++))
else
  echo "ℹ️ Aucun changement significatif détecté. Le changelog ne sera pas mis à jour."
  exit 0
fi

NEW_VERSION="${MAJOR}.${MINOR}.${PATCH}"


# Fichiers temporaires
CHANGELOG_TMP="new_changelog.md"
BREAKING_TMP="breaking_changes.tmp"
ADDED_TMP="added.tmp"
CHANGED_TMP="changed.tmp"
FIXED_TMP="fixed.tmp"
OTHER_TMP="other.tmp"

# Nettoyage fichiers temporaires
> $CHANGELOG_TMP
> $BREAKING_TMP
> $ADDED_TMP
> $CHANGED_TMP
> $FIXED_TMP
> $OTHER_TMP

# Extraction des commits depuis le dernier tag
git log "$LAST_TAG"..HEAD --pretty=format:"%s%n%b" | while IFS= read -r line; do
  if echo "$line" | grep -qi "BREAKING CHANGE"; then
    echo "- ❗ ${line}" >> $BREAKING_TMP
  elif echo "$line" | grep -qE '^feat:|^add:'; then
    echo "- ${line}" >> $ADDED_TMP
  elif echo "$line" | grep -qE '^fix:'; then
    echo "- ${line}" >> $FIXED_TMP
  elif echo "$line" | grep -qE '^change:|^refactor:|^update:'; then
    echo "- ${line}" >> $CHANGED_TMP
  elif [ -n "$line" ]; then
    echo "- ${line}" >> $OTHER_TMP
  fi
done

# Écriture du header du changelog
echo "## [$NEW_VERSION] - $DATE" >> $CHANGELOG_TMP

# Section breaking changes si présente
if [ -s $BREAKING_TMP ]; then
  echo -e "\n### ⚠️ Breaking Changes" >> $CHANGELOG_TMP
  cat $BREAKING_TMP >> $CHANGELOG_TMP
fi

# Sections par type de commit
if [ -s $ADDED_TMP ]; then
  echo -e "\n### ✨ Added" >> $CHANGELOG_TMP
  cat $ADDED_TMP >> $CHANGELOG_TMP
fi

if [ -s $CHANGED_TMP ]; then
  echo -e "\n### 🔧 Changed" >> $CHANGELOG_TMP
  cat $CHANGED_TMP >> $CHANGELOG_TMP
fi

if [ -s $FIXED_TMP ]; then
  echo -e "\n### 🐛 Fixed" >> $CHANGELOG_TMP
  cat $FIXED_TMP >> $CHANGELOG_TMP
fi

if [ -s $OTHER_TMP ]; then
  echo -e "\n### 📦 Other" >> $CHANGELOG_TMP
  cat $OTHER_TMP >> $CHANGELOG_TMP
fi

# Ajout d’un saut de ligne
echo -e "\n" >> $CHANGELOG_TMP

# Fusion avec changelog existant
if [ -f CHANGELOG.md ]; then
  cat $CHANGELOG_TMP CHANGELOG.md > tmp && mv tmp CHANGELOG.md
else
  mv $CHANGELOG_TMP CHANGELOG.md
fi

# Nettoyage
rm -f $BREAKING_TMP $ADDED_TMP $CHANGED_TMP $FIXED_TMP $OTHER_TMP

# Affichage final
echo "✅ Changelog généré :"
cat CHANGELOG.md
