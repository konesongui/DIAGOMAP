#!/bin/sh
# Script d'exemple pour serveur Linux - adapter PHP_BIN et APP_ROOT si nécessaire
PHP_BIN="/usr/bin/php"
APP_ROOT="/var/www/diagoma"   # MODIFIER selon emplacement réel sur le serveur
CRON_KEY="79f7629a88d7bcba31d8a98b4c9cd034a9f7a42f"
LOGFILE="$APP_ROOT/backup/cron_log.txt"

# S'assurer que le dossier backup existe
mkdir -p "$APP_ROOT/backup"

# Exécuter la commande de backup (controler le chemin vers index.php)
"$PHP_BIN" "$APP_ROOT/index.php" cron "$CRON_KEY" >> "$LOGFILE" 2>&1

exit $?
