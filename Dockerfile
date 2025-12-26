FROM dunglas/frankenphp:php8.2-bookworm

RUN install-php-extensions intl zip

WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader
