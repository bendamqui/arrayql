# Installation

* `copy .env.example to .env`
* `docker-compose up --build -d`
* `docker-compose exec php composer install`
* `docker-compose exec php composer test`

# Debug with PhpStorm

* Add a php cli interpreter in Preference > Languages & Frameworks > PHP. Choose the
php version from the container.

* Set debug port to 9001 in Preference > Languages & Frameworks > PHP > Debug

* Create a server in Preference > Languages & Frameworks > PHP > Server.
    * name: Has to be equal to the value of PHP_IDE_CONFIG in the .env file.
    * host: localhost
    * port: 80
    * Set the path mapping.

    


