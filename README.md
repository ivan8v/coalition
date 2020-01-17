deploy steps

clone project
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run production
