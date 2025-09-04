# Use official PHP image with Apache
FROM php:8.1-apache

# Enable Apache modules (rewrite + headers)
RUN a2enmod rewrite headers

# Fix Apache "ServerName" warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copy frontend source code to Apache document root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Set permissions (optional but good for Apache)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
