docker build ./docker/dev/php-fpm -t dev-php-fpm
docker build ./docker/dev/apache -t dev-apache
docker-compose -f docker/docker-compose.base.yml -f docker/docker-compose.dist.yml up