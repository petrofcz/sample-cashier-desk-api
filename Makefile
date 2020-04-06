DC_BASE=docker/docker-compose.base.yml
PROJECT_NAME=cdAPI

help:		## 		Show this help.
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | sed -e 's/:[ ]*[^ ]\+//'

run: prod-build		## 		Run production environment simulation. Visit http://localhost/ (see also generated docs at http://localhost/doc/)
	docker-compose -p $(PROJECT_NAME)_prod -f $(DC_BASE) -f docker/docker-compose.dist.yml up --build
start: prod-build 	##	Starts production environment simulation. Visit http://localhost/ (see also generated docs at http://localhost/doc/)
	docker-compose -p $(PROJECT_NAME)_prod -f $(DC_BASE) -f docker/docker-compose.dist.yml up -d
stop:			##            Stops running production environment simulation.
	docker-compose -p $(PROJECT_NAME)_prod -f $(DC_BASE) -f docker/docker-compose.dist.yml stop
client: prod-build		## 		Run client simulation (tests)
	docker-compose -p $(PROJECT_NAME)_test -f $(DC_BASE) -f docker/docker-compose.test.yml up --build --abort-on-container-exit
	docker-compose -p $(PROJECT_NAME)_test -f $(DC_BASE) -f docker/docker-compose.test.yml rm -fsv
dev:		## 		Run dev environment. Api is running at http://localhost/api/v1/
	docker-compose -p $(PROJECT_NAME)_dev -f $(DC_BASE) -f docker/docker-compose.dev.yml up --build
clean:		## 		Clear everything except production DB
	docker-compose -p $(PROJECT_NAME)_dev -f $(DC_BASE) -f docker/docker-compose.dev.yml rm -fsv
	docker-compose -p $(PROJECT_NAME)_test -f $(DC_BASE) -f docker/docker-compose.test.yml rm -fsv
	docker-compose -p $(PROJECT_NAME)_prod -f $(DC_BASE) -f docker/docker-compose.dist.yml rm -fs
prod-build:
	docker build . -f docker/dev/php-fpm/Dockerfile -t dev-php-fpm
	docker build . -f docker/dev/apache/Dockerfile -t dev-apache
