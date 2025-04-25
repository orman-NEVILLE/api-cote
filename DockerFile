# Utiliser une image PHP officielle comme base
FROM php:8.0-apache

# Copier ton code source dans le conteneur
COPY . /var/www/html/

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Exposer le port 80
EXPOSE 80

# Lancer Apache en mode foreground
CMD ["apache2-foreground"]