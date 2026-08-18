FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libzip-dev \
    python3 \
    python3-pip \
    python3-venv \
    && docker-php-ext-install \
    pdo_mysql \
    zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --prefer-dist

RUN python3 -m venv /opt/data-science-venv \
    && /opt/data-science-venv/bin/pip install --upgrade pip \
    && /opt/data-science-venv/bin/pip install -r Data-Science/requirements.txt

ENV DATA_SCIENCE_PYTHON=/opt/data-science-venv/bin/python

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
