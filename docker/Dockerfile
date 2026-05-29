FROM php:8.3-cli

# Pakiety systemowe potrzebne do rozszerzen PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Rozszerzenia PHP wymagane przez Laravel i PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Composer (kopiujemy z oficjalnego obrazu)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8000
