git pull

composer update --no-interaction

php artisan vendor:publish --tag=laravel-assets --ansi --force

php artisan key:generate

php artisan migrate

php artisan db:seed

php artisan optimize

php artisan optimize:clear