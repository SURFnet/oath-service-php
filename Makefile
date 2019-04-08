COMMAND=docker-compose -f docker/docker-compose.yml

install: set-env up composer

set-env:
	printf "WEB_PORT=80\nDB_PORT=3306\nUID=`id -u`\nGID=`id -g`\nIDE_SERVER_NAME=oathservice" > .env

up:
	${COMMAND} up -d

down:
	${COMMAND} down

composer:
	${COMMAND} run --rm php composer install --no-interaction

tests: test-phpmd test-phpcs test-phpunit test-security

test-phpmd:
	${COMMAND} run --rm php bin/phpmd src text config/phpmd.xml --exclude */Tests/*

test-phpcs:
	${COMMAND} run --rm php bin/phpcs --report=full --standard=config/phpcs.xml --warning-severity=0 --extensions=php src

test-phpunit:
	${COMMAND} run --rm php bin/phpunit -c config/phpunit.xml --coverage-text

test-security:
	${COMMAND} run --rm php bin/security-checker security:check

fix-cs:
	${COMMAND} run --rm php bin/phpcbf --standard=config/phpcs.xml --extensions=php src