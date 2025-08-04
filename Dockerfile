# Usa uma imagem PHP com Apache e suporte ao Laravel
FROM php:8.2-apache

# Instala extensões necessárias
RUN apt-get update && apt-get install -y \
    git unzip zip curl libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho
WORKDIR /var/www/html

# Copia arquivos
COPY . .

# Instala dependências do Laravel
RUN composer install --optimize-autoloader --no-dev

# Dá permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Define que vai usar Apache
EXPOSE 80

# Garante que .env e chave existam
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    apache2-foreground
