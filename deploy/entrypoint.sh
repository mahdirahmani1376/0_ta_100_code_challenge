#!/usr/bin/env bash

echo "RUNNING..."

php artisan config:cache \
   && php artisan migrate --force \
    && php artisan horizon:install \
    && php artisan horizon:publish

/usr/bin/supervisord -c /etc/supervisor/supervisord.conf

service cron start
supervisorctl restart all

source /etc/apache2/envvars && \
    apache2 -DFOREGROUND

#
##echo "ENV..."
##env
#
#echo "RUNNING..."
#
#php artisan config:cache \
#   && php artisan migrate --force \
#    && php artisan horizon:install \
#    && php artisan horizon:publish
#
#/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
#supervisorctl restart all
#
#service cron start
#
#mkdir /var/run/php
#echo "[global]
#daemonize = yes
#
#[www]
#listen = /var/run/php/php-fpm.sock
#user = www-data
#group = www-data
#
#
#pm = dynamic
#
#pm.max_children = 15
#
#pm.start_servers = 8
#
#pm.min_spare_servers = 5
#
#pm.max_spare_servers = 12
#
#
#
#pm.max_requests = 500" > /usr/local/etc/php-fpm.d/zz-docker.conf
#
#php-fpm -D &
#
#
#sleep 10
#touch /var/run/nginx.pid
#
#
#nginx -g "daemon off;"
