FROM php:7.2-apache

RUN docker-php-ext-install exif mbstring

COPY src/ /var/www/html/

RUN mkdir /var/www/html/upload && \
    chmod 777 /var/www/html/upload
