FROM php:8.2-apache

# 1. Install dependencies sistem & ekstensi PHP
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

# 2. Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# 3. Copy source code ke /var/www/html/
COPY . /var/www/html/

# 4. Buat folder secara manual jika belum ada (antisipasi folder kosong yang tidak ter-push ke git)
# Lalu set izin akses (ownership & permissions)
RUN mkdir -p /var/www/html/uploads/kasus \
             /var/www/html/uploads/kunjungan \
             /var/www/html/uploads/layanan \
             /var/www/html/gambar/sistem \
             /var/www/html/gambar/user \
    && chown -R www-data:www-data /var/www/html/uploads /var/www/html/gambar \
    && chmod -R 775 /var/www/html/uploads /var/www/html/gambar

EXPOSE 80