Development guide
=================

## Options
To run the Oath Service locally, there are two options. One is the method described in the base [README.md](../README.md)
where you run the application locally using Symfony's built-in web server. The other more robust and modern solution is
to use the Docker containers that are shipped with this application.

### Built-in webserver
Start with downloading the Symfony binary <sup>[1]</sup>

Then run the app: `symfony server:start -d`

As this method is not supporting the PDO solution that was introduced for storing secrets in a mysql database, this
option might not be the best solution. For better support use the Docker based solution.

1: https://symfony.com/download

### Docker
Using docker-compose you can build a fully functional development environment.

1. Set the desired `/docker/.env` vars, do so by creating a `.env` file in the `docker` directory. Or create on based on
   the `.env.dist` file
2. Start the service by running: `docker-compose up --build -d`
3. If all building succeeds you should be able to consult the webserver on your local host.

**Troubleshooting:**
1. My database is not accessible. Review the parameters.yaml file, check the dsn is correct. Also test if you can
   connect manually using the specified DSN.
2. How can I test my oath service works as intended? Use the specified Postman collection to test all available
   endpoints.
3. How can I run the code quality tests? See section below on how to run those.

## Running tests and
A set of code quality checks have been provided with the service. They are run on every Travis build. But can also be
triggered manually.

### Manual running
When using the built-in webserver solution please run the tests manually by running the binaries from the `vendor/bin`
folder with the specified config files found in the `config` folder. For example run the PHP Mess Detector by calling:

`$ ./vendor/bin/phpmd src text config/phpmd.xml --exclude "*/Tests/*"`

You can also run PHP CS, PHP Unit and Symfony Security Checker. See the `Makefile` for details on how you can run them.
Running the Unit tests might prove difficult as they rely on a database connection and a running webserver.

### Using the Makefile
Using the Makefile it becomes easy to run the automated tests on the Docker containers. Simply run the commands
specified in the Makefile. Some examples:

```
# To start the container
$ make up

# To run the PHP MD checker
$ make test-phpmd

# To perform security checks:
$ make test-security
```

### Alternatives

Alternatively these tests can be run directly from the docker container using the exec/run features that Docker Compose
provides

`$ docker-compose run --rm php ./vendor/bin/phpmd src text config/phpmd.xml --exclude */Tests/*`

Another possibility is to start an interactive bash session on the container:

`$ docker-compose exec php bash`

### Configuring PHPStorm
Some pointers to be able to run your tests directly from the PHPStorm IDE.

1. First make sure to create a PHP interpreter based on the docker-compose configuration found in the `docker` folder
2. Next configure your PHPUnit runtime configuration to use this interpreter.
3. That should do the trick.

It is possible to run the PHPUnit tests on your bare metal, but this requires you to re-configure the parameters.yaml.
The database connection DSN should be adapted to directly connect to the Docker database container. And in the
acceptance tests you'll need to update the web address that is used to visit the REST API on.
