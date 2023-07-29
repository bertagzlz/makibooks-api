FROM php:8.1-fpm-alpine
# it will use the latest version 8.0

#COPY . /var/www/html
#WORKDIR /var/www/html
#WORKDIR /web
#COPY /web /var/www/html

# FROM php:7.1.3-fpm
# part of the official PHP package and allows us to install PHP extensions.

#RUN apt-get update \
#&& apt-get install -y zip unzip libzip-dev default-mysql-client

RUN docker-php-ext-install pdo pdo_mysql

# Install composer for the application dependencies
#RUN curl -sS https://getcomposer.org/installer | php \
#&& mv composer.phar /bin/composer

# Install project dependencies
#RUN composer install -noa