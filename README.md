# Sample cashier desk API

Sample implementation of REST API for cashier desk using [Docker](https://www.docker.com/), [PHP 7.4](https://www.php.net/), [Slim 4](http://www.slimframework.com/), [Mongo DB](https://www.mongodb.com/), [Swagger](https://swagger.io/), [Codeception](https://codeception.com/). 

### Prerequisites
- docker
- docker compose
- make

### Usage:
Run following commands in the directory of this repository:
- **make client**
    
    Run sample client implemented as codeception test suite.
- **make run** 

    Run sample production environment. Requests and responses are logged to the console. Visit http://localhost/ (see also generated docs at http://localhost/doc/)
    
- **make start**

    Run sample product environment as a background service. Visit http://localhost/ (see also generated docs at http://localhost/doc/)
    
- **make stop**

    Stop sample product environment running on background.
    
- **make dev**

    Run development environment. 
    
    API is running at http://localhost/api/v1/
    
    MongoDB administration tool is available at http://localhost:8081/
    
    Swagger UI is available at http://localhost/
    
- **make clean**

    Clear everything except production DB.