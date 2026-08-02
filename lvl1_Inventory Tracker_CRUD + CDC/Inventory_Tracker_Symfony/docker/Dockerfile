FROM php:8.4-fpm-alpine

# Zainstaluj zależności systemowe oraz rozszerzenia PHP niezbędne dla Symfony + Postgres + Kafka (rdkafka)
RUN apk add --no-cache \
    bash \
    icu-dev \
    libpq-dev \
    libzip-dev \
    librdkafka-dev \
    zip \
    unzip \
    git \
    linux-headers \
    $PHPIZE_DEPS \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        intl \
        zip \
        opcache \
        bcmath \
        sockets \
    && pecl install rdkafka mongodb \
    && docker-php-ext-enable rdkafka mongodb \
    && apk del $PHPIZE_DEPS

# Pobierz instalator Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
