FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo_mysql gd


WORKDIR /var/www


COPY . .


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install

EXPOSE 9000
CMD ["php-fpm"]