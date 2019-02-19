#!/usr/bin/env bash

BASEDIR=$(dirname "$0")
cd "${BASEDIR}/../"

docker-compose -f docker/docker-compose.yml \
    run \
    --rm \
    php \
    php -d memory_limit=-1 /usr/bin/composer "$@"