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
