#!/bin/sh

if [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer"
    composer install --no-interaction --no-progress
else
    composer dump-autoload
fi

if [ ! -f "/logs" ]; then
    mkdir /logs
fi

cp .env.example .env

php artisan storage:link

php artisan key:generate --force

printenv > /etc/environment

php artisan migrate --force --seed

#clear cache
php artisan filament:optimize-clear
php artisan optimize:clear

php artisan optimize
php artisan filament:optimize

php artisan octane:install --server=frankenphp

supervisord -c  /etc/supervisor/conf.d/supervisord.conf

