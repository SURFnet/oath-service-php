# Development environment


The docker development environment

## Install

In order to use the development environment you should have docker-compose installed.

To get it running:

    make install
    make up

You could see the `Makefile` to see the available commands.

#### Be aware
There is no separate test database to run tests against. This should be addressed later on. 


## Composer

In order to use composer you could use for example:

    ./docker/composer.sh install


