#!/bin/sh

sleep 5


# wait for Elasticsearch to be available
while ! curl -s http://elasticsearch:9200; do
    echo "Waiting for Elasticsearch..."
    sleep 10
done

# Then run the queue worker
php artisan queue:work redis --tries=3 --timeout=60


# راه‌اندازی PHP-FPM
exec php-fpm
