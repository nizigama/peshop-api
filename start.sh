#!/bin/bash

if [ ! -d "/var/www/html/vendor" ]; then
    exec composer install
fi

exec php artisan serve --host 0.0.0.0
