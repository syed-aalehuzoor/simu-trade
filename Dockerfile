FROM dunglas/frankenphp:php8.3-bookworm

# Install required PHP extensions
RUN install-php-extensions intl zip pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel permissions
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080
