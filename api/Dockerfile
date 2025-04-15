FROM composer:2 AS vendor

WORKDIR /app
COPY ./composer.* ./
RUN composer install --no-dev --prefer-dist --no-progress --no-scripts

FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libonig-dev libzip-dev zip libpq-dev mariadb-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache gd

WORKDIR /var/www/html

COPY --from=vendor /app/vendor/ ./vendor/
COPY ./ ./

EXPOSE 9000

CMD ["php-fpm"]