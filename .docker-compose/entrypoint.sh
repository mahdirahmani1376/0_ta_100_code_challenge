#!/usr/bin/env bash

echo "RUNNING..."

/usr/bin/supervisord -c /etc/supervisor/supervisord.conf

service cron start

source /etc/apache2/envvars && \
    apache2 -DFOREGROUND
