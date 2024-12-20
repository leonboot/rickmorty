FROM php:8.3-apache AS base

ARG TZ=Europe/Amsterdam

RUN cp $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini && \
    cp $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php-cli.ini && \
    sed -i 's/^memory_limit = .*/memory_limit = -1/' $PHP_INI_DIR/php-cli.ini && \
    sed -i 's/^memory_limit = .*/memory_limit = 256M/' $PHP_INI_DIR/php.ini && \
    echo "date.timezone = ${TZ}" >> $PHP_INI_DIR/conf.d/timezone.ini && \
    mkdir -p /app && \
    rm -rf /var/www/html && \
    ln -s /app/public /var/www/html && \
    chown -R www-data:www-data /var/www /app && \
    a2enmod rewrite env && \
    echo 'SetEnv APP_ENV ${APP_ENV}' > /etc/apache2/conf-enabled/environment.conf

WORKDIR /app

FROM base AS dev

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN --mount=type=bind,from=mlocati/php-extension-installer:latest,source=/usr/bin/install-php-extensions,target=/usr/local/bin/install-php-extensions \
    install-php-extensions xdebug && \
    apt-get update && \
    apt-get install -y git unzip && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

USER www-data

FROM dev AS no-xdebug

USER root

RUN rm $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini

USER www-data

FROM node:22 AS webpack

WORKDIR /build

COPY . /build

RUN npm install && npm run build

FROM no-xdebug AS build

USER root

COPY . /app

COPY --from=webpack /build/public/build /app/public/build

ENV APP_ENV=prod

RUN composer install --no-dev --no-interaction --no-progress --no-suggest && \
    tar -czf /app.tar.gz -C /app bin config public src templates var/cache/prod vendor .env composer.json

FROM base AS prod

ENV APP_ENV=prod

RUN --mount=type=bind,from=build,source=/app.tar.gz,target=/app.tar.gz \
    tar -xzf /app.tar.gz -C /app

USER www-data
