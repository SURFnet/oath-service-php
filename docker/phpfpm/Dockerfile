#
# PHP Dependencies
#
FROM composer:1.7 as composer


#
# Application
#
FROM php:5.6-fpm AS base

RUN apt-get update \
    && apt-get install -y git zlib1g-dev \
    && docker-php-ext-install zip \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY . /var/www/html


#
# Develop
#
FROM base as develop

RUN pecl install xdebug-2.5.5
RUN docker-php-ext-enable xdebug