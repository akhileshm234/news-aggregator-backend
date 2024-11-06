# Use the official PHP CLI image with PHP 8.2
FROM php:8.2-cli

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application code
COPY . /var/www/html

# Fix permissions for the web server
RUN chown -R www-data:www-data /var/www/html

# Expose port 8000 for Laravel development server
EXPOSE 8000

# Command to run Laravel development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]