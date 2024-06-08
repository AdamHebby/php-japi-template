FROM php:8-apache

RUN usermod -u 1000 www-data

# Install and enable pdo_mysql
RUN docker-php-ext-install pdo_mysql


RUN a2enmod ssl && a2enmod rewrite
RUN mkdir -p /etc/apache2/ssl
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY ./certs/*.pem /etc/apache2/ssl/
COPY ./apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Fix perms on certs so apache can read them
RUN chown -R www-data:www-data /etc/apache2/ssl
USER www-data

WORKDIR /var/www/html

EXPOSE 80
EXPOSE 443
