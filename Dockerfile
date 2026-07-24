# Utiliser une image PHP avec Apache
FROM php:8.2-apache

# Installer les extensions nécessaires
RUN docker-php-ext-install pdo_mysql

# Activer le module Apache mod_rewrite
RUN a2enmod rewrite

# Copier tous les fichiers du projet dans le conteneur
COPY . /var/www/html/

# Donner les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configurer le fichier de configuration
COPY docker-php.ini /usr/local/etc/php/conf.d/custom.ini

# Exposer le port 80
EXPOSE 80 
