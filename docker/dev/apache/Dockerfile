ARG APACHE_VERSION=""
FROM httpd:${APACHE_VERSION:+${APACHE_VERSION}-}alpine

RUN apk update; \
    apk upgrade;

# Copy apache vhost file to proxy php requests to php-fpm container
COPY ./docker/dev/apache/apache-fpm.conf /usr/local/apache2/conf/apache-fpm.conf
RUN echo "Include /usr/local/apache2/conf/apache-fpm.conf" \
    >> /usr/local/apache2/conf/httpd.conf

# Enable mod_rewrite support
RUN sed -i '/LoadModule rewrite_module/s/^#//g' /usr/local/apache2/conf/httpd.conf
RUN { \
  echo 'IncludeOptional conf.d/*.conf'; \
} >> /usr/local/apache2/conf/httpd.conf \
  && mkdir /usr/local/apache2/conf.d

RUN mkdir -p /var/www/public && echo "DONE"