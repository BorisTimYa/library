FROM php:7.4-fpm

RUN apt-get update && \
		apt-get install -y libpq-dev wget

RUN docker-php-ext-install pdo_pgsql
RUN wget https://get.symfony.com/cli/installer -O - | bash
#RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony
#RUN chmod +x /usr/local/bin/symfony