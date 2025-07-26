# Use official PHP with Apache
FROM php:8.2-apache

# Install PDO and MySQL driver
RUN docker-php-ext-install pdo pdo_mysql

# Copy backend code
COPY ucstinternship/ /var/www/html/

# Set proper permissions (optional)
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
# 