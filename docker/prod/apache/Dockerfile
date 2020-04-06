FROM openapitools/openapi-generator-cli AS docgen
COPY doc/openapi.yaml /tmp/openapi.yaml
RUN mkdir -p /tmp/doc
RUN /usr/local/bin/docker-entrypoint.sh generate \
    -i /tmp/openapi.yaml \
    -g html \
    -o /tmp/doc

FROM dev-apache
COPY --from=docgen /tmp/doc/ /var/www/public/doc/
COPY backend/public/ /var/www/public/
