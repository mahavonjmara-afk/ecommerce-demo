FROM php:8.2-cli

WORKDIR /var/www/html

# Dépendances système
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libzip-dev libxml2-dev libonig-dev unzip zip nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mbstring zip bcmath xml dom fileinfo \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie du projet
COPY . .

# Installation + Build + Cache clear
RUN composer install --optimize-autoloader --no-dev --no-interaction --ignore-platform-reqs \
    && npm ci --no-audit --no-fund \
    && NODE_OPTIONS="--max-old-space-size=512" npm run build \
    && php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && rm -rf node_modules

# Permissions & SQLite
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && touch storage/database.sqlite \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 10000

# Lancement
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]