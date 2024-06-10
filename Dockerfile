FROM --platform=linux/amd64 396192154208.dkr.ecr.ap-southeast-1.amazonaws.com/skribble-learn-base-docker-image:1.0
ARG env

# Setup laravel
WORKDIR /var/www

RUN rm -rf /var/www/*

# composer and npm
COPY composer.json /var/www/composer.json
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader --ansi -vvv

COPY package.json /var/www/package.json
RUN npm install

COPY . .
COPY .env.$env .env

RUN composer dump-autoload && \
    php artisan config:cache && php artisan route:cache

# Configure nginx
RUN rm -rf /var/www/dockerfiles && \
    rm -rf /var/www/Dockerfile && \
    rm -rf /var/www/kubernetes && \
    rm -rf /var/www/storage/logs && \
    mkdir /var/www/storage/logs && \
    rm /etc/nginx/nginx.conf && \
    rm -rf /etc/nginx/sites-available/default && \
    rm -rf /etc/supervisord.conf && \
    touch /var/log/laravel_worker.log && \
    chown root:www-data /var/log/laravel_worker.log && \
    chmod 775 /var/log/laravel_worker.log && \
    touch /var/log/laravel_worker_audit.log && \
    chown root:www-data /var/log/laravel_worker_audit.log && \
    chmod 775 /var/log/laravel_worker_audit.log && \
    touch /var/log/cron.log && \
    chown www-data:www-data /var/log/cron.log && \
    echo '* * * * * www-data php /var/www/artisan schedule:run >> /var/log/cron.log 2>&1' > /etc/cron.d/laravel-cron && \
    chmod 0644 /etc/cron.d/laravel-cron && \
    touch /etc/cron.d/laravel-cron && \
    chown -R www-data:www-data /var/www/ && \
    chown -R www-data:www-data /var/www/storage && \
    chown -R www-data:www-data /var/www/storage/framework && \
    chown -R root:www-data /var/www/storage/logs && \
    chmod -R 775 /var/www/storage && \
    chmod -R 775 /var/www/bootstrap/cache && \
    rm -rf /var/lib/apt/lists/*

# PORTS
EXPOSE 80

COPY dockerfiles/nginx.conf /etc/nginx/nginx.conf
COPY dockerfiles/nginx_app.conf /etc/nginx/sites-available/default
COPY dockerfiles/supervisord.conf /etc/supervisord.conf

STOPSIGNAL SIGQUIT

# first command run must be supervisord so supervisord will get PID 1, as docker only sends SIGQUIT to PID 1.
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
