FROM php:8.3-cli

# Install system dependencies and Node.js
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install PHP extensions required for Laravel and PostgreSQL
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql zip gd

# Set working directory
WORKDIR /app

# Copy all project files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies and build Tailwind/Vite assets
RUN npm install && npm run build

# Ensure storage directories exist and have correct permissions
RUN mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions storage/logs \
    && chmod -R 777 storage bootstrap/cache

# Run migrations and start the Laravel server
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
