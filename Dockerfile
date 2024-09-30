# Used for prod build.
FROM php:8.2-fpm 

# Install dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    libcurl4-gnutls-dev \
    nginx \
    libonig-dev \
    curl \
    nodejs \
    npm \
    cron \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    bcmath \
    curl \
    opcache \
    mbstring

# Install Redis PHP extension
RUN pecl install redis && docker-php-ext-enable redis



# Copy composer executable
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# نصب Node.js و npm
RUN apt-get update && apt-get install -y curl
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get install -y nodejs

# Set working directory to /var/www
WORKDIR /var/www

# کپی فایل‌های پروژه Laravel به داخل کانتینر
COPY ./src /var/www/html/

# نصب پکیج‌های Composer
WORKDIR /var/www/html
RUN composer install --no-scripts --no-autoloader

# نصب پکیج Elasticsearch و Tesseract OCR
#RUN composer require elasticsearch/elasticsearch
#RUN composer require thiagoalessio/tesseract_ocr

# اجرای دستور dump-autoload برای بروزرسانی Autoload
#RUN composer dump-autoload

# کپی دوباره فایل‌ها (در صورتی که نیاز است)
COPY --chown=www-data:www-data . .

# تنظیم کلید اپلیکیشن Laravel
#RUN php artisan key:generate

# Create laravel caching folders
RUN mkdir -p /var/www/storage/framework/{cache,sessions,testing,views}

# Fix files ownership
RUN chown -R www-data /var/www/storage
RUN chown -R www-data /var/www/storage/framework

# Adjust user permission & group
RUN usermod --uid 1000 www-data
RUN groupmod --gid 1001 www-data

# نصب پکیج‌های npm
RUN npm install
RUN mkdir -p /etc/supervisor/conf.d
# کپی کردن فایل تنظیمات supervisor به کانتینر
#Copy ./src/laravel-worker.conf /etc/supervisor/conf.d/
#COPY ./etc/supervisor/conf.d/laravel-worker.conf /etc/supervisor/conf.d/
COPY ./src/laravel-worker.conf /etc/supervisor/conf.d/