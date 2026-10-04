#!/usr/bin/env bash

# تثبيت الحزم البرمجية
composer install --no-dev --optimize-autoloader

# مسح الكاش
php artisan config:clear
php artisan route:clear
php artisan view:clear

# تنفيذ قواعد البيانات (Migrations)
php artisan migrate --force --seed
