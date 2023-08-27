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
    libcap2-bin

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
RUN pecl install redis && docker-php-ext-enable redis

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
