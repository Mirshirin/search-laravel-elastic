#!/bin/sh

sleep 15

# اولین migrate
php artisan migrate --force

# اجرای seeder برای ProductsSeeder
php artisan db:seed --class=ProductsSeeder --force

# دوباره migrate
php artisan migrate --force

# اجرای npm
npm install
npm run dev &


# Start php-fpm as a background process
php-fpm &

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
