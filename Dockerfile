# 1. Usamos PHP 8.4 con Apache
FROM php:8.4-apache

# 2. Instalar dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libsqlite3-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. Habilitar mod_rewrite para que las rutas de Laravel funcionen
RUN a2enmod rewrite

# 4. Configurar el directorio de trabajo
WORKDIR /var/www/html

# 5. Copiar los archivos del proyecto al contenedor
COPY . /var/www/html

# 6. Instalar Composer y las dependencias de PHP
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 7. Crear el archivo de base de datos SQLite y dar permisos
# Es vital que el usuario www-data sea dueño de la carpeta database para poder escribir
RUN mkdir -p storage bootstrap/cache database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache database

# 8. Configurar Apache para que apunte a la carpeta /public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 9. Exponer el puerto 80
EXPOSE 80

# 10. Comando final: Migrar y arrancar Apache
# Usamos "sh -c" para asegurar que ambos comandos se ejecuten correctamente
CMD sh -c "php artisan migrate --force && apache2-foreground"
