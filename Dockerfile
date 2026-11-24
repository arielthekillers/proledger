# PaketFlow Production Dockerfile for Coolify / Docker
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql gd zip opcache

# Enable Apache modules
RUN a2enmod rewrite headers

# Configure Apache DocumentRoot to point to /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configure Apache Directory permissions for .htaccess
RUN echo '<Directory /var/www/html/public>\n\
    Options -Indexes +FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/paketflow.conf \
    && a2enconf paketflow

# PHP Production Configuration
RUN echo 'upload_max_filesize = 50M\n\
post_max_size = 50M\n\
memory_limit = 256M\n\
max_execution_time = 120\n\
date.timezone = Asia/Jakarta\n\
session.gc_maxlifetime = 7200\n\
expose_php = Off' > /usr/local/etc/php/conf.d/custom-php.ini

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Create uploads directory and ensure permissions
RUN mkdir -p /var/www/html/public/uploads \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/public/uploads

# Expose port 80
EXPOSE 80

# Ensure uploads directory permissions at startup (for Coolify Persistent Storage mounts) and start Apache
CMD ["sh", "-c", "mkdir -p /var/www/html/public/uploads && chown -R www-data:www-data /var/www/html/public/uploads && chmod -R 775 /var/www/html/public/uploads && exec apache2-foreground"]
