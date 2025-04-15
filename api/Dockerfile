# Étape 1 : Créer un conteneur PHP avec les extensions nécessaires
FROM php:8.2-fpm AS build

# Installation des extensions PHP nécessaires et des dépendances
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libonig-dev libzip-dev zip libpq-dev mariadb-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache gd

# Étape 2 : Installer Composer et les dépendances PHP
WORKDIR /app

# Copier les fichiers composer
COPY ./composer.* ./

# Installer les dépendances PHP
RUN curl -sS https://getcomposer.org/installer | php \
    && php composer.phar install --no-dev --prefer-dist --no-progress --no-scripts

# Étape 3 : Copier le code dans le conteneur final
FROM php:8.2-fpm

# Installation des extensions PHP nécessaires et des dépendances
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libicu-dev libonig-dev libzip-dev zip libpq-dev mariadb-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache gd

WORKDIR /var/www/html

# Copier les dépendances installées et le code
COPY --from=build /app/vendor /var/www/html/vendor
COPY ./ /var/www/html/

# Exposer le port 9000 pour PHP-FPM
EXPOSE 9000

# Lancer PHP-FPM
CMD ["php-fpm"]