FROM php:7.4.9-apache

# Apt packages update
RUN apt-get update -y

# Install libs & extensions
RUN apt-get install -y libzip-dev zip
RUN docker-php-ext-install zip
RUN docker-php-ext-install mysqli

RUN a2enmod rewrite

# Xdebug
#RUN pecl install xdebug-2.6.0
#RUN docker-php-ext-enable xdebug

COPY .env /var/www/html/.env