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
  echo -e "./sc.sh loop [<start|stop|exec|exec-quiet>]"
  echo -e "./sc.sh resolve-permissions"
  echo -e "${NC}"
}

if test "$1" = "composer"
then
  docker run --rm --interactive --tty --volume $PWD:/app --volume $COMPOSER_HOME:/tmp composer:$COMPOSER_VERSION ${@} --ignore-platform-reqs
elif test "$1" = "exec-php"
then
  docker-compose exec php bash
elif test "$1" = "loop"
then
  #docker-compose restart php-cli
  if test "$2" = "start"
  then
    docker-compose start php-cli
  elif test "$2" = "stop"
  then
    docker-compose stop php-cli
    echo -e "${GREEN}Loop has been stopped.${NC}"
  elif test "$2" = "exec-quiet"
  then
    docker-compose exec -d php-cli php ./www/index.php wakers:start-sender-loop
    echo -e "${GREEN}Loop started in quiet mode. You can stop this loop by ./sc.sh loop stop.${NC}"
  elif test "$2" = "exec"
  then
    docker-compose exec php-cli php ./www/index.php wakers:start-sender-loop
  else
    help
  fi
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