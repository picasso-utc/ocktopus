FROM php:8.3-cli

# Installation des dépendances systèmes
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    unzip \
    libzip-dev

# Nettoyage
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP requises par Laravel et SQLite/MySQL
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8000

# Lancement du serveur intégré de Laravel 
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
