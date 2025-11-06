#!/bin/bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$(realpath "$SCRIPT_DIR/../../../")"
TMP_DIR="$SCRIPT_DIR/tmp"
REPO="picasso-utc/ocktopus"
ARTIFACT_NAME="laravel-deploy-package"

source "../../../.env"
if [ -z "$GITHUB_TOKEN" ]; then
  echo "GITHUB_TOKEN non trouvé dans .env"
  exit 1
fi

ARTIFACT_ID=$(curl -s -H "Authorization: Bearer $GITHUB_TOKEN" \
  "https://api.github.com/repos/$REPO/actions/artifacts" \
  | jq ".artifacts[] | select(.name==\"$ARTIFACT_NAME\") | .id" \
  | head -n 1)

if [ -z "$ARTIFACT_ID" ]; then
  echo "Erreur : aucun artefact trouvé."
  exit 1
fi

curl -L -H "Authorization: Bearer $GITHUB_TOKEN" \
  -o artifact.zip \
  "https://api.github.com/repos/$REPO/actions/artifacts/$ARTIFACT_ID/zip"

rm -rf "$TMP_DIR"
mkdir -p "$TMP_DIR"
unzip artifact.zip -d "$TMP_DIR"
tar -xzf "$TMP_DIR/deploy.tar.gz" -C "$TMP_DIR"

rsync -av --delete \
  --exclude='.env' \
  --exclude='storage' \
  "$TMP_DIR/" "$APP_DIR/"

cd "$APP_DIR"
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Déploiement terminé"