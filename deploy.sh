#!/bin/bash
set -e

cd "$(dirname "$0")"

echo "=== Construction de l'image Docker ==="
docker compose build

echo "=== Démarrage du conteneur ==="
docker compose up -d

echo ""
echo "=== Terminé ==="
docker ps --filter "name=jauge-rentree"
echo ""
echo "Le tableau de bord est accessible sur : http://<IP_DU_LXC>:8105"
