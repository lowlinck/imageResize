#!/bin/sh

# Замена переменной порта в конфигурации Nginx
sed -i "s,LISTEN_PORT,$PORT,g" /etc/nginx/nginx.conf

# Запуск PHP-FPM в фоновом режиме
php-fpm -D

# Проверка, что PHP-FPM запущен
while ! nc -w 1 -z 127.0.0.1 9000; do sleep 0.1; done;

# Запуск Nginx в фореграунд режиме
nginx -g 'daemon off;'
