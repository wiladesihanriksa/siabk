FROM php:8.2-apache

# Install dependencies sistem & ekstensi PHP untuk MySQL, Excel, dan PDF
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install mysqli pdo pdo_mysql gd zip xml

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Copy source code
COPY . /var/www/html/

# Set izin akses folder uploads dan gambar (recursively)
RUN chown -R www-data:www-data /var/www/html/uploads /var/www/html/gambar \
    && chmod -R 775 /var/www/html/uploads /var/www/html/gambar

EXPOSE 80