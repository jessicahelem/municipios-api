# Utilizar a imagem base do PHP
FROM php:8.2-fpm

# Atualizar pacotes e instalar dependências necessárias
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    libssl-dev \
    && docker-php-ext-install \
       pdo_mysql \
       mbstring \
       exif \
       pcntl \
       bcmath \
       gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar o diretório de trabalho
WORKDIR /var/www/html

# Copiar todos os arquivos do projeto
COPY . .

# Configurar permissões para o Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Instalar dependências do Composer
RUN composer install --optimize-autoloader --no-dev

# Expor a porta do PHP-FPM
EXPOSE 9000

# Definir o comando inicial do contêiner
CMD ["php-fpm"]
