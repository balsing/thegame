FROM php:7.4-fpm-alpine

RUN apk add --no-cache \
      postgresql-dev \
      libzip-dev \
      zip \
    && docker-php-ext-install zip

RUN docker-php-ext-install mysqli pdo pdo_mysql pdo_pgsql && docker-php-ext-enable pdo_mysql pdo_pgsql