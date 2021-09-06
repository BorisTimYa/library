FROM php:7.4-fpm

RUN apt update && \
	apt install -y libpq-dev
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install opcache
