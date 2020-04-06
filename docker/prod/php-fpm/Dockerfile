# Install composer dependencies
FROM composer as composer
COPY ./backend/composer.* ./
RUN composer config platform.php ${PHP_VERSION} && \
    composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# Setup PHP-FPM
FROM dev-php-fpm
COPY ./backend/ /var/www/
COPY --from=composer /app/vendor/ /var/www/vendor/
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"