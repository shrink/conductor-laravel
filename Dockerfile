ARG PHP_VERSION=7.4
ARG COMPOSER_VERSION=1
ARG COMPOSER=composer:${COMPOSER_VERSION}

FROM $COMPOSER AS composer

FROM php:${PHP_VERSION}-cli-alpine

WORKDIR /srv

RUN apk add --no-cache git

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer global require hirak/prestissimo --dev --prefer-dist && \
    composer install --no-ansi --no-interaction
