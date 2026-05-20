FROM php:8.2-apache

WORKDIR /var/www/html

# 1. Dépendances système + Extensions PHP Laravel
RUN apt-get update && apt-get install -y \
    git curl libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libxml2-dev libonig-dev unzip zip libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mbstring zip bcmath intl xml dom fileinfo \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Composer + Configuration mémoire
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

# 3. Node.js 20 (requis par Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 4. Copie du projet
COPY . .

# 5. Installation PHP (avec ignore-platform-reqs pour éviter le blocage Docker/Render)
RUN composer install --optimize-autoloader --no-dev --no-interaction --ignore-platform-reqs

# 6. Installation JS + Build Vite (limite mémoire pour Render Free)
RUN npm ci --no-audit --no-fund \
    && NODE_OPTIONS="--max-old-space-size=512" npm run build \
    && rm -rf node_modules

# 7. Structure Laravel + SQLite + Permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && touch storage/database.sqlite \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# 8. Config Apache (optionnel mais recommandé)
COPY apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 10000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]