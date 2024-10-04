# Этап 1: Базовый образ с PHP 8.3 и необходимыми расширениями
FROM php:8.3-fpm

# Установка аргументов для гибкости
ARG WWWGROUP=1000
ARG NODE_VERSION=20

# Установка переменных окружения
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

# Установка рабочей директории
WORKDIR /var/www/html

# Обновление системы и установка необходимых пакетов
RUN apt-get update \
    && apt-get install -y \
        gnupg \
        gosu \
        curl \
        ca-certificates \
        zip \
        unzip \
        git \
        supervisor \
        sqlite3 \
        libcap2-bin \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libjpeg-dev \
        libpq-dev \
        libfreetype6-dev \
        libssl-dev \
        libzip-dev \
        libmagickwand-dev \
        libwebp-dev \
        nano \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip soap intl opcache \
    && pecl install imagick redis \
    && docker-php-ext-enable imagick redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Установка Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Установка Node.js и npm
RUN curl -fsSL https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm \
    && npm install -g pnpm \
    && npm install -g yarn

# Установка часового пояса
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Создание группы и пользователя для приложения
RUN groupadd --force -g $WWWGROUP sail \
    && useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

# Копирование файлов приложения в контейнер
COPY . /var/www/html

# Установка прав доступа для файлов и директорий
RUN chown -R sail:sail /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Переключение на пользователя 'sail'
USER sail

# Установка зависимостей Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Установка зависимостей NPM и сборка ассетов
RUN npm install \
    && npm run build

# Переключение обратно на пользователя root
USER root

# Копирование файлов конфигурации Supervisor и скрипта запуска
COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /usr/local/etc/php/conf.d/99-sail.ini

# Установка прав на скрипт запуска
RUN chmod +x /usr/local/bin/start-container

# Открытие порта 80 для входящих соединений
EXPOSE 80

# Установка точки входа
ENTRYPOINT ["start-container"]
