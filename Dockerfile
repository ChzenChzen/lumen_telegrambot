FROM php:7.3-fpm

WORKDIR /var/www

# Install dependencies
RUN apt-get update \
    && apt-get install -y zlib1g-dev libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install zip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install lumen framework
RUN composer global require "laravel/lumen-installer"

# Add user for lumen application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

# Update PATH for lumen
ENV PATH=${PATH}:/var/www/vendor/bin

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]