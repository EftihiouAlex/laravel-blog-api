# PHP 8.2 με extensions
FROM php:8.2-fpm

# Εγκατάσταση system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Εγκατάσταση Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Δημιουργία workdir
WORKDIR /var/www

# Αντέγραψε τα αρχεία του Laravel
COPY . .

# Εγκατάσταση εξαρτήσεων
RUN composer install

# Δώσε δικαιώματα storage & bootstrap
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
