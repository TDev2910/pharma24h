FROM composer:latest AS vendor
WORKDIR /app

COPY composer.json composer.lock ./

# Install dependencies without scripts/plugins to speed up and stay secure
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

FROM dunglas/frankenphp:1-php8.2-alpine

ENV SERVER_NAME=:8000
ENV APP_ENV=local
ENV APP_DEBUG=true

RUN apk add --no-cache \
    curl \
    libpng-dev \
    libzip-dev \
    && install-php-extensions \
    pdo_mysql \
    gd \
    intl \
    zip \
    bcmath \
    opcache

# 2. Set up the working directory
WORKDIR /app

COPY --from=vendor /app/vendor /app/vendor

COPY . .

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

HEALTHCHECK --interval=30s --timeout=5s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8000/ || exit 1

EXPOSE 8000

# USER www-data

CMD ["frankenphp", "php-server", "--listen", ":8000"]
