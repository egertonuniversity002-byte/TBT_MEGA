# Use official PHP image with Apache
FROM php:8.1-apache

# Copy frontend source code to Apache document root
COPY . /var/www/html/

# Enable Apache mod_rewrite for URL rewriting if needed
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
