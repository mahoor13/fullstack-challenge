#/bin/sh

if [ ! -f "/var/www/html/.env" ]
then 
    cd /var/www/html/
    cp .env.example .env
    php artisan key:generate
    php artisan db:seed
    php artisan test
fi