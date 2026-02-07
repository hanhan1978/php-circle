FROM php:8.3-cli

# unzipとzipエクステンションをインストール
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
