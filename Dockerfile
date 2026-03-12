# SỬ DỤNG FRANKENPHP SIÊU NHẸ CHO LOCAL
FROM dunglas/frankenphp:1-php8.2-alpine

# Cài đặt PHP extensions cần thiết nhất
RUN install-php-extensions \
    pdo_mysql \
    gd \
    intl \
    zip \
    bcmath \
    mbstring

# Thiết lập thư mục làm việc
WORKDIR /app

# CHỈ COPY FILE CODE (Đã loại bỏ node_modules thông qua .dockerignore)
COPY . .

# Cấu hình Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Thiết lập quyền và link storage
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chmod -R 777 storage bootstrap/cache

# Cấu hình Server
ENV SERVER_NAME=:8000
ENV APP_ENV=local
ENV APP_DEBUG=true

EXPOSE 8000

CMD ["frankenphp", "php-server", "--listen", ":8000"]
