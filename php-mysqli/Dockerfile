FROM php:7.2-fpm

RUN apt-get update \
    && apt-get install iputils-ping \
    && docker-php-ext-install mysqli && docker-php-ext-enable mysqli
