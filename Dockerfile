FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx wget

RUN mkdir -p /run/nginx

COPY nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p /app
COPY . /app


RUN sh -c "wget http://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer"
RUN cd /app && \
    /usr/local/bin/composer install --no-dev

RUN chown -R www-data: /app
COPY startup.sh /app/docker/startup.sh

CMD ["sh", "/app/docker/startup.sh"]
