FROM php:8.2-apache

WORKDIR /var/www/html

# 1. Dépendances & Extensions PHP
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libzip-dev libxml2-dev libonig-dev unzip zip libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mbstring zip bcmath intl xml dom fileinfo \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Configuration Apache pour le port 10000 (Requis par Render)
RUN sed -i 's/80/10000/g' /etc/apache2/ports.conf

# 3. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

# 4. Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 5. Copie du projet
COPY . .

# 6. Installation & Build
RUN composer install --optimize-autoloader --no-dev --no-interaction --ignore-platform-reqs \
    && npm ci --no-audit --no-fund \
    && NODE_OPTIONS="--max-old-space-size=512" npm run build \
    && rm -rf node_modules

# 7. Permissions & SQLite
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && touch storage/database.sqlite \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# 8. Config Apache
COPY apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 10000

# 9. Démarrage via Apache (Plus stable pour les assets CSS/JS)
CMD ["apache2-foreground"]