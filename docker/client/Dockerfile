ARG PHP_VERSION="7.4"

# Install composer dependencies
FROM composer as composer
COPY ./client/composer.* ./
RUN composer config platform.php ${PHP_VERSION} && \
    composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# Configure PHP-Cli
FROM php:${PHP_VERSION:+${PHP_VERSION}-}cli-alpine
ENV DOCKERIZE_VERSION v0.6.0
RUN apk update; \
    apk upgrade;
RUN apk add curl libcurl curl-dev libxml2-dev
RUN docker-php-ext-install curl dom
RUN apk del gcc g++ curl-dev libxml2-dev &&\
    rm -rf /var/cache/apk/*
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz
RUN mkdir -p /usr/src/
WORKDIR /usr/src
COPY ./client/ ./
COPY --from=composer /app/vendor/ ./vendor/

CMD ["php", "/usr/src/vendor/bin/codecept", "run", "api", "--env local"]