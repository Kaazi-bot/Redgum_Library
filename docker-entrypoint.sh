#!/bin/bash
# Boots MariaDB, loads sql/schema.sql on first run, then hands off to Apache.
#
# The database is configured to match config/db.php exactly as written
# (host 'localhost' via unix socket, user 'root', empty password) so that
# no application file needs to be modified for deployment.
set -e

APP_PORT="${PORT:-80}"
sed -ri "s/^Listen [0-9]+/Listen ${APP_PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${APP_PORT}>/" /etc/apache2/sites-available/000-default.conf

DATADIR=/var/lib/mysql

if [ ! -d "$DATADIR/mysql" ]; then
    echo "[init] creating MariaDB data directory"
    mysql_install_db --user=mysql --datadir="$DATADIR" >/dev/null
fi

echo "[init] starting MariaDB (port 3307, bound to localhost only)"
mysqld_safe --user=mysql --datadir="$DATADIR" --port=3307 --bind-address=127.0.0.1 >/dev/null 2>&1 &

for i in $(seq 1 60); do
    mysqladmin ping --silent 2>/dev/null && break
    sleep 1
done
mysqladmin ping --silent 2>/dev/null || { echo "[fatal] MariaDB did not start"; exit 1; }

# Allow the Apache user (www-data) to connect as root with no password over the
# local socket. MariaDB defaults root to unix_socket auth, which only permits
# the OS root account; config/db.php connects as root from www-data.
echo "[init] configuring root authentication for local socket access"
mysql -u root <<'SQL' || true
ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('');
FLUSH PRIVILEGES;
SQL

if ! mysql -u root -e "USE redgum_library; SELECT 1 FROM users LIMIT 1;" >/dev/null 2>&1; then
    echo "[init] loading sql/schema.sql (tables + seed data)"
    mysql -u root < /var/www/html/sql/schema.sql
else
    echo "[init] database already present, skipping schema load"
fi

echo "[init] starting Apache on port ${APP_PORT}"
exec apache2-foreground
