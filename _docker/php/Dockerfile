FROM php:8.2-fpm AS common

RUN apt update

RUN docker-php-ext-install -j$(nproc) pdo_mysql
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install sockets

RUN apt install -y libonig-dev && docker-php-ext-install mbstring

RUN apt install -y libcurl4-nss-dev && docker-php-ext-install curl

RUN apt install -y \libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
    && docker-php-ext-install -j$(nproc) gd zip

RUN apt install -y default-mysql-client

WORKDIR /var/www/site

COPY "./prod/php.ini" "$PHP_INI_DIR/php.ini"
COPY "./prod/php-fpm.conf" /usr/local/etc/
COPY "./prod/www.prod.conf" /usr/local/etc/php-fpm.d/

FROM common as prod

FROM common as dev

RUN pecl install xdebug-3.2.1 \
&& docker-php-ext-enable xdebug

COPY "./dev/xdebug.ini" "$PHP_INI_DIR/conf.d/99-xdebug.ini"

FROM common as composer

RUN apt install unzip # нужен для более стабильной работы PHP COMPOSER
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

FROM common as cron

RUN apt-get -y install cron && which cron && \
                                rm -rf /etc/cron.*/*

COPY cron.entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

# https://manpages.ubuntu.com/manpages/trusty/man8/cron.8.html
# -f | Stay in foreground mode, don't daemonize.
# -L loglevel | Tell  cron  what to log about jobs (errors are logged regardless of this value) as the sum of the following values:
CMD ["cron","-f", "-L", "2"]
COPY cron.d /etc/cron.d/cron-job
