FROM php:8.1-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork

COPY . /var/www/html/

RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf \
    && sed -i 's/80/${PORT}/g' /etc/apache2/sites-enabled/000-default.conf

EXPOSE ${PORT}
