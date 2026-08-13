ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libicu-dev \
    libxml2-dev \
    libxslt1-dev \
    libzip-dev \
    libonig-dev \
    libsodium-dev \
    libssl-dev \
    unzip \
    git \
    cron \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        gd \
        intl \
        mbstring \
        opcache \
        pdo_mysql \
        soap \
        sockets \
        sodium \
        ftp \
        xsl \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN groupadd -g 1000 magento \
    && useradd -g 1000 -u 1000 -d /var/www/html -s /bin/bash magento

WORKDIR /var/www/html

COPY install-magento.sh /usr/local/bin/install-magento.sh
RUN chmod +x /usr/local/bin/install-magento.sh

EXPOSE 9000
CMD ["php-fpm"]
