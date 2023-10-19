FROM  php:8.2-fpm

# TODO: read about docker stages

# Set working directory
WORKDIR /var/www

RUN apt-get update \
  && apt-get install -y \
  git \
  curl \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  zip \
  unzip \
  zlib1g-dev \
  libpq-dev \
  libzip-dev

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo pdo_pgsql pgsql zip bcmath gd

# Install xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# install composer by copying the composer.phar file from the official composer image to the /usr/bin/composer path
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Get NodeJS
COPY --from=node:20-slim /usr/local/bin /usr/local/bin
# Get npm
COPY --from=node:20-slim /usr/local/lib/node_modules /usr/local/lib/node_modules

# Set to a non-root user to run the application
#USER 3000

# run composer install
#RUN composer install
#
#RUN php artisan migrate

#FROM php:8.2-apache-buster as production
#
## setting the two environment variables, APP_ENV to “production” and APP_DEBUG to false
#ENV APP_ENV=production
#ENV APP_DEBUG=false
#ENV TZ=UTC
#
##  PHP 8.0 with opcache for speed
##RUN docker-php-ext-configure opcache --enable-opcache && \
##    docker-php-ext-install pdo pdo_mysql
##COPY docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
#
## copy the whole app and its composer dependencies downloaded in the build stage to the production stage at
## /var/www/html the default document root for the official PHP Apache docker image.
#COPY --from=build /app /var/www/html
#
## copy the Apache configuration from ./docker/000-default.conf to /etc/apache2/sites-available/000-default.conf
## inside the container
#COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
#
## copy the .env.prod file that has the configs and credentials we need to run the application
#COPY .env.prod /var/www/html/.env
#
#RUN php artisan config:cache && \
#    php artisan route:cache && \
#    chmod 777 -R /var/www/html/storage/ && \
#    chown -R www-data:www-data /var/www/ && \
#    a2enmod rewrite \
