FROM php:7.4-fpm

RUN apt update && \
	apt install -y git libpq-dev libzip-dev zip unzip

RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install opcache
