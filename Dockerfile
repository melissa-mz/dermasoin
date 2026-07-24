# Utiliser une image PHP avec Apache
FROM php:8.2-apache

# Installer les extensions nécessaires pour MySQL et PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql

# Activer le module Apache mod_rewrite
RUN a2enmod rewrite

# Copier tous les fichiers du projet dans le conteneur
COPY . /var/www/html/

# Donner les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exposer le port 80
EXPOSE 80