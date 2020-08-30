# Eshopio.app
Micro e-shop system with cart & invoices

## Installation guide
1. Install PHP dependencies: `./sc.sh composer i`
1. Install Node modules: `npm i`
1. Compile assets: `npm run gulp-dev`
1. Create .env file `cp .env.example .env`
1. Run Docker: `docker-compose up --build --d`
1. Connect to php container `./sc.sh exec-app`
1. Execute migrations `php ./www/index.php migration:migrate`
1. Create user `php ./www/index.php wakers:create-user <email> <password>`