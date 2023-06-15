FROM php:7.4-fpm

WORKDIR /var/www/html

# Install required dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    libzip-dev \
    zip \
    && docker-php-ext-install zip pdo pdo_mysql

# Remove default Nginx configuration
RUN rm /etc/nginx/sites-enabled/default

# Copy Nginx configuration for Laravel
COPY docker/nginx/laravel.conf /etc/nginx/sites-available/
RUN ln -s /etc/nginx/sites-available/laravel.conf /etc/nginx/sites-enabled/

# Copy Laravel files
COPY . /var/www/html

# Set up storage and cache permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Set permissions for resources/json directory
RUN chmod -R 777 /var/www/html/resources/json/

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Update Composer dependencies
RUN composer update

# Expose port 80 for web server
EXPOSE 80

# Start services
CMD php-fpm -D && nginx -g "daemon off;"
