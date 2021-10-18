COMMAND=docker-compose -f docker/docker-compose.yml

install: prep-env up composer

up:
	${COMMAND} up -d

down:
	${COMMAND} down

prep-env:
	printf "WEB_PORT=80\nDB_PORT=3306\nUID=`id -u`\nGID=`id -g`\nIDE_SERVER_NAME=oathservice" > docker/.env

composer:
	${COMMAND} run --rm php composer install --no-interaction

tests: test-phpmd test-phpcs test-phpunit test-security

test-phpmd:
	${COMMAND} run --rm php ./vendor/bin/phpmd src text config/phpmd.xml --exclude */Tests/*

test-phpcs:
	${COMMAND} run --rm php ./vendor/bin/phpcs --report=full --standard=config/phpcs.xml --warning-severity=0 --extensions=php src

test-phpunit:
	${COMMAND} run --rm php ./vendor/bin/phpunit -c config/phpunit.xml

test-security:
	wget https://github.com/fabpot/local-php-security-checker/releases/download/v1.0.0/local-php-security-checker_1.0.0_linux_amd64 -O ./bin/local-php-security-checker && ${COMMAND} run --rm php chmod +x ./bin/local-php-security-checker && ${COMMAND} run --rm php ./bin/local-php-security-checker && ${COMMAND} run --rm php rm ./bin/local-php-security-checker

fix-cs:
	${COMMAND} run --rm php ./vendor/bin/phpcbf --standard=config/phpcs.xml --extensions=php src
