#
# PHP Dependencies
#
FROM composer:1.7 as composer


#
# Application
#
FROM php:7.2-fpm AS base

RUN apt-get update \
    && apt-get install -y git libicu-dev \
    && docker-php-ext-install -j$(nproc) intl

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY . /var/www/html


#
# Develop
#
FROM base as develop

RUN pecl install xdebug-2.6.0
RUN docker-php-ext-enable xdebug