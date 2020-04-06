DC_BASE=docker/docker-compose.base.yml

help:		## 		Show this help.
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | sed -e 's/:[ ]*[^ ]\+//'

client: prod-build		## 		Run client simulation (tests)
	docker-compose -f $(DC_BASE) -f docker/docker-compose.test.yml up --build --abort-on-container-exit
	docker-compose -f $(DC_BASE) -f docker/docker-compose.test.yml rm -fsv
dist: prod-build		## 		Run production environment simulation. Visit http://localhost/ (also see generated docs at http://localhost/doc)
	docker-compose -f $(DC_BASE) -f docker/docker-compose.dist.yml up --build
dev:		## 		Run dev environment. Api is running at http://localhost/api/v1
	docker-compose -f $(DC_BASE) -f docker/docker-compose.dev.yml up --build
clean:		## 		Clear everything
	docker-compose -f $(DC_BASE) -f docker/docker-compose.test.yml rm -fsv
	docker-compose -f $(DC_BASE) -f docker/docker-compose.dev.yml rm -fsv
	docker-compose -f $(DC_BASE) -f docker/docker-compose.dist.yml rm -fs
prod-build:
	docker build . -f docker/dev/php-fpm/Dockerfile -t dev-php-fpm
	docker build . -f docker/dev/apache/Dockerfile -t dev-apache
