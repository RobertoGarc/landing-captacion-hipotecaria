#!/usr/bin/env bash
set -e

# Render defaults PORT=10000; this image's Nginx listens on 80 unless we retarget it.
LISTEN_PORT="${PORT:-80}"

echo "Configuring Nginx to listen on port ${LISTEN_PORT}..."

for conf in \
  /etc/nginx/sites-available/default.conf \
  /etc/nginx/conf.d/default.conf \
  /etc/nginx/sites-enabled/default \
  /var/www/html/conf/nginx/nginx-site.conf
do
  if [ -f "$conf" ]; then
    sed -i "s/listen \[::\]:80/listen [::]:${LISTEN_PORT}/g" "$conf" || true
    sed -i "s/listen 80;/listen ${LISTEN_PORT};/g" "$conf" || true
    sed -i "s/listen 80 /listen ${LISTEN_PORT} /g" "$conf" || true
  fi
done

echo "Port configuration done."
