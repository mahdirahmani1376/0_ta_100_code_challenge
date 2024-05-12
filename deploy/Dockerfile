#FROM keeper.hostiran.com/library/base-ubuntu-php:8.2
#FROM keeper.hostiran.com/library/base-php-mainapp-bookworm-fpm:8.1
FROM keeper.hostiran.com/library/base-php-bookworm-apache-all:8.2
MAINTAINER Reza Bajelan

ARG WWW_DATA_USER_ID
ENV TZ=GST-3:30


# Change TimeZone
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone && dpkg-reconfigure -f noninteractive tzdata

RUN chmod ugo+x /usr/local/bin/*

# Instal timezone db
RUN pecl upgrade timezonedb

# Config Apache
ADD ./deploy/apache/default.conf /etc/apache2/sites-enabled/000-default.conf
RUN \
    printf "\nServerName localhost" >> /etc/apache2/apache2.conf  && \
    a2enmod rewrite && a2enmod headers


COPY ./deploy/entrypoint.sh /
COPY ./src/ /var/www/
COPY ./deploy/config/php.ini /usr/local/etc/php/php.ini

ADD ./deploy/config/supervisor/* /etc/supervisor/conf.d/

# Copy cron file to the cron.d directory
COPY ./deploy/config/cron/task /etc/cron.d/task

RUN chmod 0644 /etc/cron.d/task
RUN chmod a+x /entrypoint.sh
RUN crontab /etc/cron.d/task
RUN touch /var/log/cron.log


#RUN chown -R www-data:www-data /var/www
RUN chown -R www-data:www-data /var/www &&\
    usermod -u 1002 www-data && groupmod -g 1002 www-data


USER www-data

WORKDIR /var/www

RUN rm -f /var/www/composer.lock


RUN composer install --ignore-platform-req=ext-pcntl

USER root
#RUN rm /var/www/.env
# Override workdir to an existing directory

CMD ["/entrypoint.sh"]
