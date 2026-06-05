#!/bin/bash

# Start PHP-FPM in the background
php-fpm &

# Start nginx
nginx -g "daemon off;"
