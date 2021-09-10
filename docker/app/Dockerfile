FROM php:7.4-fpm

RUN apt-get update && \
	apt-get install -y libpq-dev git unzip
#RUN apk add --no-cache libpq-dev git unzip

RUN docker-php-ext-install pdo_pgsql
RUN pecl install xdebug-2.9.2
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-install opcache
#RUN apk add --no-cache $PHPIZE_DEPS \
#    && pecl install xdebug-2.9.2 \
#    && docker-php-ext-enable xdebug  \
