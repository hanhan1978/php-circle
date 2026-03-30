FROM php:8.4-cli

# unzip/zip/git をインストール（symfony/tui の取得に git が必要）
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    git \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# symfony/tui はまだ Packagist 未公開のため fabpot の tui ブランチから取得
RUN git clone --depth=1 --branch=tui https://github.com/fabpot/symfony.git /symfony-src

WORKDIR /app
