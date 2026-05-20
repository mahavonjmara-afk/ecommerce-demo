FROM php:8.2-apache

WORKDIR /var/www/html

# 1. Installer les dépendances système, Node.js 20 et les extensions PHP requises par Laravel
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip libsqlite3-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring zip exif pcntl bcmath gd xml dom fileinfo \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Copier les fichiers du projet
COPY . .

# 4. Installer les dépendances PHP/JS et compiler les assets
RUN composer install --optimize-autoloader --no-dev \
    && npm ci \
    && npm run build \
    && rm -rf node_modules

# 5. Permissions et création du fichier SQLite (pour la démo)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && touch /var/www/html/storage/database.sqlite \
    && chown www-data:www-data /var/www/html/storage/database.sqlite

# 6. Configuration Apache (optionnel si vous utilisez php artisan serve, mais utile)
COPY apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 10000

# 7. Lancement du serveur de développement Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]