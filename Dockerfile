FROM dunglas/frankenphp:php8.3-bookworm

# Install PHP extensions
RUN install-php-extensions intl zip pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

RUN npm install && npm run build

# Laravel permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Tell FrankenPHP to use our Caddyfile
COPY Caddyfile /etc/frankenphp/Caddyfile
