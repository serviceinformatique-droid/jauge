FROM php:8.2-apache

# Autoriser l'affichage en iframe (retire X-Frame-Options, autorise le CSP frame-ancestors)
RUN a2enmod headers
COPY apache-headers.conf /etc/apache2/conf-enabled/zz-iframe.conf

# Copie de l'application
COPY tableau-de-bord-rentree.html /var/www/html/index.html
COPY save.php /var/www/html/save.php

# Dossier de stockage des données (sera monté en volume, voir docker-compose.yml)
RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html/data \
    && chmod -R 775 /var/www/html/data

EXPOSE 80
