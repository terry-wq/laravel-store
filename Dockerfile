FROM php:8.2-cli

# Instalar dependencias del sistema
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

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /app

# Copiar archivos
COPY . .

# Instalar dependencias PHP
RUN composer install --optimize-autoloader --no-interaction

# Generar cache Laravel
RUN php artisan config:clear || true
RUN php artisan route:clear || true
RUN php artisan view:clear || true

# Exponer puerto
EXPOSE 8080

# Iniciar servidor
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
