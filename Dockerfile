FROM php:8.1-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql

RUN apk add --no-cache nginx

COPY . /var/www/html/

RUN echo 'server { \
    listen ${PORT}; \
    root /var/www/html; \
    index index.php; \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        include fastcgi_params; \
    } \
}' > /etc/nginx/http.d/default.conf

EXPOSE ${PORT}

CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"
