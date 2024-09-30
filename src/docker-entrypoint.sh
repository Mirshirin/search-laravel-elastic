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


# اضافه کردن cron job
echo "* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1" | crontab -

# راه‌اندازی cron
cron


# راه‌اندازی supervisor برای مدیریت queue
supervisord -c /etc/supervisor/supervisord.conf

# راه‌اندازی PHP-FPM
exec php-fpm
