#!/bin/bash

# hack to set env when cron task runs, else laravel won't be able to detect server-set ENV settings
#printenv | sed 's/^\(.*\)\=\(.*\)$/export \1\="\2"/g' > /var/www/env.sh

# start docker image supervisord
/usr/bin/supervisord -n -c /etc/supervisord.conf

# Cache and route config
php /var/www/artisan config:cache
php /var/www/artisan route:cache

# Initialize application data
#php /var/www/artisan cloudflare:update
