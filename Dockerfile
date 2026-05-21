FROM php:8.2-cli

WORKDIR /var/www/html

# 1. Dépendances système & Extensions PHP
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libzip-dev libxml2-dev libonig-dev unzip zip nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mbstring zip bcmath xml dom fileinfo \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

# 3. Copie du projet
COPY . .

# 4. Installation & Build
RUN composer install --optimize-autoloader --no-dev --no-interaction --ignore-platform-reqs \
    && npm ci --no-audit --no-fund \
    && NODE_OPTIONS="--max-old-space-size=512" npm run build \
    && rm -rf node_modules

# 5. Structure, Permissions & SQLite
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && touch storage/database.sqlite \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 10000

# 🔥 6. Démarrage : Migrations + Cache Clear + Serveur
CMD php artisan migrate --force \
    && php artisan optimize:clear \
    && php artisan serve --host=0.0.0.0 --port=10000