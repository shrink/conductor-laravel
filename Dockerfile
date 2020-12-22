ARG PHP_VERSION=8
ARG COMPOSER_VERSION=2
ARG COMPOSER=composer:${COMPOSER_VERSION}

FROM $COMPOSER AS composer

FROM php:${PHP_VERSION}-cli-alpine

WORKDIR /srv

RUN apk add --no-cache git

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN composer install --no-ansi --no-interaction
