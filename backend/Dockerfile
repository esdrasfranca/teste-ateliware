# backend/Dockerfile
FROM php:8.4-fpm-alpine

# Instala dependências do sistema e extensões do PHP necessárias para o Laravel
RUN apk update && apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    libzip-dev \
    unzip

# Instala extensões do PHP via script nativo do Docker
RUN docker-php-ext-install mbstring exif pcntl bcmath gd zip

# Copia o Composer mais recente da imagem oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho conforme sua especificação
WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]