# استخدام نسخة PHP حديثة
FROM php:8.2-cli

# تثبيت الإضافات اللازمة لربط قاعدة البيانات
RUN apt-get update -y && apt-get install -y unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# تثبيت أداة Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# تحديد مسار العمل
WORKDIR /app
COPY . .

# تثبيت الحزم
RUN composer install --no-dev --optimize-autoloader

# إعطاء الصلاحيات للمجلدات
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# بناء الجداول وتشغيل السيرفر تلقائياً
CMD sh -c "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"
