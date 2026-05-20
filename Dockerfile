FROM php:8.2-apache

WORKDIR /var/www/html

# 1. Dépendances système + Extensions PHP requises par Laravel
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libzip-dev libxml2-dev libonig-dev unzip zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mbstring zip bcmath pcntl xml dom fileinfo \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# 3. Node.js 20 & npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 4. Copie du projet
COPY . .

# 5. Installation PHP (étape isolée)
RUN composer install --optimize-autoloader --no-dev --no-interaction

# 6. Installation JS & Build (avec limite mémoire pour Render gratuit)
RUN npm ci && NODE_OPTIONS="--max-old-space-size=512" npm run build

# 7. Nettoyage, Permissions & SQLite
RUN rm -rf node_modules \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && touch storage/database.sqlite \
    && chown www-data:www-data storage/database.sqlite

EXPOSE 10000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]