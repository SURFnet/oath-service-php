COMMAND=docker-compose -f docker/docker-compose.yml

install: set-env up composer

set-env:
	printf "UID=`id -u`\nGID=`id -g`\nIDE_SERVER_NAME=oathservice" > .env

up:
	${COMMAND} up -d

down:
	${COMMAND} down

composer:
	${COMMAND} run --rm php composer install --no-interaction

tests: test-phpmd test-phpcs

test-phpmd:
	${COMMAND} run --rm php bin/phpmd src text config/phpmd.xml --exclude */Tests/*

test-phpcs:
	${COMMAND} run --rm php bin/phpcs --report=full --standard=config/phpcs.xml --warning-severity=0 --extensions=php src

test-phpunit:
	${COMMAND} run --rm php bin/phpunit -c config/phpunit.xml --coverage-text