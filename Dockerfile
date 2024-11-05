FROM php:8.2-cli

WORKDIR /opt

RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev 

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /opt

# Install composer dependencies
RUN composer install

# Generate application key after dependencies are installed
RUN php artisan key:generate

RUN chown -R www-data:www-data /opt

EXPOSE 8000

CMD ["php", "artisan", "serve"]
