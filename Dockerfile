FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libzip-dev \
    && docker-php-ext-install \
    pdo_mysql \
    zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --prefer-dist

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
