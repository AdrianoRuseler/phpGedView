# Use official PHP image with Apache
FROM php:7.4-apache

# Install system dependencies, PHP extensions, subversion, rsync, and SQLite
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    subversion \
    rsync \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo_sqlite \
        zip \
        calendar \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite module (useful for phpGedView pretty URLs)
RUN a2enmod rewrite

# Set working directory
WORKDIR /tmp

# Configure SVN timeout and checkout phpGedView folder
RUN echo "[global]" > /etc/subversion/servers \
    && echo "http-timeout = 6000" >> /etc/subversion/servers \
    && svn checkout https://svn.code.sf.net/p/phpgedview/svn/trunk/phpGedView/ phpgedview-checkout \
    && mkdir -p /var/www/html/PhpGedView \
    && rsync -a phpgedview-checkout/ /var/www/html/PhpGedView \
    && rm -rf phpgedview-checkout \
    && chown -R www-data:www-data /var/www/html/PhpGedView \
    && chmod -R 755 /var/www/html/PhpGedView

# Create SQLite database file
RUN mkdir -p /var/www/html/PhpGedView/data \
    && touch /var/www/html/PhpGedView/data/phpgedview.sqlite \
    && chown www-data:www-data /var/www/html/PhpGedView/data/phpgedview.sqlite \
    && chmod 644 /var/www/html/PhpGedView/data/phpgedview.sqlite
# 	&& chmod -R 755 /var/www/html/PhpGedView/data
	
# Set Apache document root to the PhpGedView directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/PhpGedView
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy custom php.ini (optional, for configuration tweaks)
COPY ./php.ini /usr/local/etc/php/php.ini

COPY ./config.php /var/www/html/PhpGedView/config.php
RUN chown www-data:www-data /var/www/html/PhpGedView/config.php
RUN chmod 644 /var/www/html/PhpGedView/config.php

# Add health check script
COPY ./healthcheck.php /var/www/html/PhpGedView/healthcheck.php

# Expose port 80 for Apache
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]