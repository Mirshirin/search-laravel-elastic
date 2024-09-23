#!/bin/bash

# تاخیر برای آماده شدن MySQL
sleep 10

# اجرای migration
php artisan migrate

# راه‌اندازی PHP-FPM
exec php-fpm
