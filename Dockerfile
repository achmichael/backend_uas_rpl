FROM dunglas/frankenphp

# Install ekstensi PHP
RUN install-php-extensions pcntl pdo pdo_mysql

# Install dependensi sistem
RUN apt-get update && apt-get install -y libpq-dev libpng-dev libjpeg-dev libfreetype6-dev

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Salin sisa kode aplikasi ke dalam container
COPY . /app

COPY wait-for-it.sh /app/wait-for-it.sh

RUN chmod +x /app/wait-for-it.sh

WORKDIR /app

RUN composer dump-autoload

# Jalankan perintah Octane saat container dimulai
ENTRYPOINT ["sh", "./wait-for-it.sh", "mysql:3306", "--", "php", "artisan", "octane:start", "--server=frankenphp", "--host=0.0.0.0", "--port=8000", "--workers=4"]>