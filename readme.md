# Contactio.app
E-mail campaign management and async campaign sender.

## Installation guide
1. Clone repository: `git clone git@github.com:jzaplet/contactio.app.git`
1. Install PHP dependencies: `./sc.sh composer install --ignore-platform-reqs`
1. Install Node modules: `npm i`
1. Compile assets: `npm run gulp-dev`
1. Create .env file `cp .env.example .env`
1. Create nginx config `cp ./docker/nginx/nginx.example.conf ./docker/nginx/nginx.conf`
1. Run Docker: `docker-compose up --build --d`
1. Connect to php container `./sc.sh exec-php`
1. Execute migrations `php ./www/index.php migration:migrate`
1. Insert core DB data `php ./www/index.php wakers:insert-data`
1. Create user `php ./www/index.php wakers:create-user <email> <password>`

## Run mail sender infinity loop
1. Run container for async php loop`./sc.sh loop start`
1. Start async php loop `./sc.sh loop exec`
1. Start async php loop in background `./sc.sh loop exec-quiet`
1. Stop container for async php loop `./sc.sh loop stop`