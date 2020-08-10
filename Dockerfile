FROM php:7.1-fpm

RUN docker-php-ext-install pdo_mysql mysqli && docker-php-ext-enable mysqli

