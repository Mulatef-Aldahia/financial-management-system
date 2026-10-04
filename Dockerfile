# استخدام نسخة PHP 8.4
FROM php:8.4-cli

# تثبيت الإضافات والمكتبات الأساسية التي يطلبها Laravel
RUN apt-get update -y && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    && docker-php-ext-install pdo pdo_mysql mbstring xml bcmath zip

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