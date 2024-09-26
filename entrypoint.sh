#!/bin/bash

# تاخیر برای آماده شدن MySQL
sleep 10

# بررسی اتصال به دیتابیس
until php artisan migrate:status > /dev/null 2>&1; do
    echo "Waiting for MySQL to be ready..."
    sleep 5
done

# اجرای migration
php artisan migrate --force
php artisan db:seed ProductsSeeder

# راه‌اندازی PHP-FPM
exec php-fpm
