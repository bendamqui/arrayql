FROM php:7.4-cli-alpine

WORKDIR /var/www/html

# Xdebug
RUN apk update
RUN apk add --no-cache git
RUN apk add --no-cache $PHPIZE_DEPS

RUN docker-php-ext-install mysqli
RUN pecl install xdebug-2.9.1
RUN docker-php-ext-enable xdebug

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug-dev.ini

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
