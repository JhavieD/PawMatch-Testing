# Use PHP 8.2 FPM with Alpine Linux for smaller image size
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    supervisor \
    nginx \
    mysql-client \
    sqlite \
    sqlite-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    xml \
    zip \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy package files for Node.js dependencies
COPY package.json package-lock.json ./

# Install Node.js dependencies
RUN npm ci --only=production

# Copy application code
COPY . .

# Build frontend assets
RUN npm run build

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create .env file if it doesn't exist
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Generate application key
RUN php artisan key:generate --no-interaction

# Optimize Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Create nginx configuration
RUN echo 'server { \
    listen 80; \
    server_name _; \
    root /var/www/html/public; \
    index index.php index.html; \
    \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; \
        include fastcgi_params; \
    } \
    \
    location ~ /\.(?!well-known).* { \
        deny all; \
    } \
}' > /etc/nginx/http.d/default.conf

# Create supervisor configuration
RUN echo '[supervisord] \
nodaemon=true \
user=root \
logfile=/var/log/supervisor/supervisord.log \
pidfile=/var/run/supervisord.pid \
\
[program:php-fpm] \
command=php-fpm \
autostart=true \
autorestart=true \
stderr_logfile=/var/log/supervisor/php-fpm.err.log \
stdout_logfile=/var/log/supervisor/php-fpm.out.log \
\
[program:nginx] \
command=nginx -g "daemon off;" \
autostart=true \
autorestart=true \
stderr_logfile=/var/log/supervisor/nginx.err.log \
stdout_logfile=/var/log/supervisor/nginx.out.log \
\
[program:laravel-queue] \
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3 --max-time=3600 \
autostart=true \
autorestart=true \
stderr_logfile=/var/log/supervisor/laravel-queue.err.log \
stdout_logfile=/var/log/supervisor/laravel-queue.out.log \
user=www-data \
\
[program:laravel-schedule] \
command=php /var/www/html/artisan schedule:work \
autostart=true \
autorestart=true \
stderr_logfile=/var/log/supervisor/laravel-schedule.err.log \
stdout_logfile=/var/log/supervisor/laravel-schedule.out.log \
user=www-data' > /etc/supervisor/conf.d/supervisord.conf

# Create log directories
RUN mkdir -p /var/log/supervisor

# Expose port 80
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"] 