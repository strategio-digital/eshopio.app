#!/bin/bash
COMPOSER_VERSION="1.9.1"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

help() {
  echo -e "${YELLOW}COMMANDS:"
  echo -e "${GREEN}./sc.sh composer"
  echo -e "./sc.sh exec-php"
  echo -e "./sc.sh resolve-permissions"
  echo -e "${NC}"
}

if test "$1" = "composer"
then
  docker run --rm --interactive --tty --volume $PWD:/app --volume $COMPOSER_HOME:/tmp composer:$COMPOSER_VERSION ${@}
elif test "$1" = "exec-php"
then
  docker-compose exec php bash
elif test "$1" = "resolve-permissions"
then
  mkdir -p ./www/temp/static
  mkdir -p ./assets/dynamic
  mkdir -p ./temp
  mkdir -p ./log
  chmod -R ugo+rw ./www/temp/static
  chmod -R ugo+w ./assets/dynamic
  chmod -R ugo+w ./temp
  chmod -R ugo+w ./log
  echo -e "${GREEN}Permissions successfully resolved.${NC}"
else
  help
fi