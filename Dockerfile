# PHP image
FROM php:8.2-fpm

# Install packages and extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo_mysql gd zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/app

# Copy the application code to the container
COPY . .

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM service
CMD ["php-fpm"]

