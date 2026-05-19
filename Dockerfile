FROM node:20 AS node

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .

RUN npm run build


FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install \
    bcmath \
    intl \
    pdo_mysql \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

COPY --from=node /app/public/build ./public/build

RUN composer install --optimize-autoloader --no-interaction

RUN mkdir -p storage/framework/cache/data
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8080

CMD php artisan migrate

CMD php artisan db:seed

CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
