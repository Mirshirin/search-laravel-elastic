# Used for prod build.
FROM php:8.2-fpm 


# Install dependencies.
RUN apt-get update && apt-get install -y unzip libpq-dev libcurl4-gnutls-dev nginx libonig-dev

# Install PHP extensions.
RUN docker-php-ext-install mysqli pdo pdo_mysql bcmath curl opcache mbstring

# Copy composer executable.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# نصب Node.js و npm

RUN apt-get update && apt-get install -y curl
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
    RUN apt-get install -y nodejs
  


# Set working directory to /var/www.
WORKDIR /var/www
# نصب وابستگی‌های پروژه از composer.json
COPY ./src /var/www/html/
# نصب Laravel CLI


# نصب پکیج Elasticsearch
WORKDIR /var/www/html



COPY ./src/composer.json ./src/composer.lock ./
COPY ./src/package.json ./src/package-lock.json ./


#RUN composer global require laravel/installer
RUN composer install --no-scripts --no-autoloader

RUN composer require elasticsearch/elasticsearch
RUN composer require thiagoalessio/tesseract_ocr
RUN composer dump-autoload
RUN php artisan key:generate
# Copy files from current folder to container current folder (set in workdir).
COPY --chown=www-data:www-data . .

# Create laravel caching folders.
RUN mkdir -p /var/www/storage/framework
RUN mkdir -p /var/www/storage/framework/cache
RUN mkdir -p /var/www/storage/framework/testing
RUN mkdir -p /var/www/storage/framework/sessions
RUN mkdir -p /var/www/storage/framework/views

# Fix files ownership.
RUN chown -R www-data /var/www/storage
RUN chown -R www-data /var/www/storage/framework
RUN chown -R www-data /var/www/storage/framework/sessions


# Adjust user permission & group
RUN usermod --uid 1000 www-data
RUN groupmod --gid 1001 www-data
RUN apt-get update && apt-get install -y nodejs npm

RUN npm install


#CMD ["npm", "start"]

# Run the entrypoint file.
#ENTRYPOINT [ "docker/entrypoint.sh" ]
