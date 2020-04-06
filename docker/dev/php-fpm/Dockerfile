ARG PHP_VERSION="7.4"
FROM php:${PHP_VERSION:+${PHP_VERSION}-}fpm-alpine
RUN apk update; \
    apk upgrade;
RUN apk --no-cache add pcre-dev openssl-dev ${PHPIZE_DEPS} \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apk del pcre-dev openssl-dev ${PHPIZE_DEPS}