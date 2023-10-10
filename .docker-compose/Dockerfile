FROM php:8.2.8-fpm

# Arguments defined in docker-compose.yml
ARG USER
ARG UID

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libzip-dev \
    unzip \
    supervisor \
    libcap2-bin \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install exif
RUN docker-php-ext-install pcntl
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install gd
RUN docker-php-ext-install zip

#installing redis extension
RUN printf "\n" | curl 'http://pecl.php.net/get/redis-5.3.7.tgz' -o redis-5.3.7.tgz
RUN pecl install redis-5.3.7.tgz \
    &&  rm -rf redis-5.3.7.tgz \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis
#Installing mongodb
RUN printf "\n" | curl 'http://pecl.php.net/get/mongodb-1.16.2.tgz' -o mongodb-1.16.2.tgz
RUN pecl install mongodb-1.16.2.tgz \
    &&  rm -rf mongodb-1.16.2.tgz \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable mongodb

#RUN pecl install mongodb && docker-php-ext-enable mongodb

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $UID -d /home/$USER $USER
RUN mkdir -p /home/$USER/.composer && \
    chown -R $USER:$USER /home/$USER

COPY php.ini /usr/local/etc/php/php.ini
COPY horizon.conf /etc/supervisor/conf.d/horizon.conf
COPY ./entrypoint.sh /

# Set working directory
WORKDIR /var/www

USER $USER

#ENTRYPOINT ["/entrypoint.sh"]
