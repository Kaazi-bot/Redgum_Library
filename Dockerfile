# Redgum Community Library - PHP 8.2 + Apache + MariaDB in one container.
FROM php:8.2-apache

# PDO MySQL driver required by config/db.php
RUN docker-php-ext-install pdo_mysql

# Database server runs alongside Apache (Render's free tier has no MySQL add-on)
RUN apt-get update \
 && apt-get install -y --no-install-recommends mariadb-server mariadb-client \
 && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers

# config/db.php connects to host 'localhost', which PDO resolves to a unix
# socket. Point PHP at MariaDB's real socket path so that no application
# file needs to be modified for deployment.
RUN { \
      echo "pdo_mysql.default_socket=/run/mysqld/mysqld.sock"; \
      echo "mysqli.default_socket=/run/mysqld/mysqld.sock"; \
    } > /usr/local/etc/php/conf.d/zz-mysql-socket.ini

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
CMD ["docker-entrypoint.sh"]
