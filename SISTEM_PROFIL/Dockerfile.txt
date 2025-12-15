FROM php:8.2-apache

# Enable Apache rewrite
RUN a2enmod rewrite

# Set document root ke /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

# Install mysqli
RUN docker-php-ext-install mysqli

# Copy project files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html
