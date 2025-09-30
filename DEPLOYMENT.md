# Deployment Guide - Laravel Blog Platform

This guide covers the steps required to deploy the Laravel Blog Platform to production.

## Table of Contents
- [Server Requirements](#server-requirements)
- [Pre-Deployment Checklist](#pre-deployment-checklist)
- [Deployment Steps](#deployment-steps)
- [Post-Deployment](#post-deployment)
- [Troubleshooting](#troubleshooting)

---

## Server Requirements

### Minimum Requirements
- **PHP**: 8.2 or higher
- **Web Server**: Nginx or Apache
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **Redis**: 6.0+ (for caching)
- **Node.js**: 18.x or higher (for asset building)
- **Composer**: 2.x
- **Supervisor**: For queue workers

### PHP Extensions Required
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- Tokenizer
- XML
- GD or Imagick (for image processing)

---

## Pre-Deployment Checklist

### 1. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure environment variables
nano .env
```

### 2. Required Environment Variables
```env
APP_NAME="Your Blog Name"
APP_ENV=production
APP_KEY=base64:... (generated)
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_production
DB_USERNAME=blog_user
DB_PASSWORD=secure_password_here

# Cache & Sessions
CACHE_STORE=redis
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Filesystem
FILESYSTEM_DISK=public
```

### 3. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE blog_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'blog_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON blog_production.* TO 'blog_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## Deployment Steps

### Option 1: Using Deployment Script (Recommended)

```bash
# Make script executable
chmod +x deploy.sh

# Run deployment
./deploy.sh
```

### Option 2: Manual Deployment

#### Step 1: Prepare Application
```bash
# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci --production

# Build assets
npm run build
```

#### Step 2: Database Migration
```bash
# Run migrations
php artisan migrate --force

# Seed initial data (if needed)
php artisan db:seed --force
```

#### Step 3: Optimize Laravel
```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize autoloader
php artisan optimize
```

#### Step 4: Storage Setup
```bash
# Create storage symlink
php artisan storage:link

# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Web Server Configuration

### Nginx Configuration
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com;
    root /path/to/your/blog/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss;
}
```

### Apache Configuration (.htaccess already included)
Ensure `mod_rewrite` is enabled:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## Queue Worker Setup

### Using Supervisor

1. Copy configuration:
```bash
sudo cp blog-worker.conf /etc/supervisor/conf.d/
```

2. Edit the file:
```bash
sudo nano /etc/supervisor/conf.d/blog-worker.conf
```

Update paths:
- `/path/to/your/blog/artisan` → actual project path
- `/path/to/your/blog/storage/logs/worker.log` → actual log path

3. Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start blog-worker:*
```

4. Check status:
```bash
sudo supervisorctl status blog-worker:*
```

---

## SSL Certificate (Let's Encrypt)

```bash
# Install certbot
sudo apt-get install certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal (cron)
sudo certbot renew --dry-run
```

---

## Post-Deployment

### 1. Verify Installation
- Visit your domain: `https://yourdomain.com`
- Check health endpoint: `https://yourdomain.com/up`
- Test user registration and login
- Create a test post
- Upload an image

### 2. Monitor Logs
```bash
# Application logs
tail -f storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/error.log

# Queue worker logs
tail -f storage/logs/worker.log
```

### 3. Setup Cron Jobs
```bash
# Edit crontab
crontab -e

# Add Laravel scheduler
* * * * * cd /path/to/your/blog && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Backup Strategy
```bash
# Database backup script
#!/bin/bash
DATE=$(date +"%Y%m%d_%H%M%S")
mysqldump -u blog_user -p blog_production > /backups/db_$DATE.sql
gzip /backups/db_$DATE.sql

# Storage backup
tar -czf /backups/storage_$DATE.tar.gz storage/app/public

# Keep only last 7 days
find /backups -type f -mtime +7 -delete
```

Add to crontab (daily at 2 AM):
```
0 2 * * * /path/to/backup-script.sh
```

---

## Performance Optimization

### 1. PHP-FPM Tuning
Edit `/etc/php/8.2/fpm/pool.d/www.conf`:
```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
```

### 2. MySQL Optimization
Edit `/etc/mysql/my.cnf`:
```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
max_connections = 200
query_cache_type = 1
query_cache_size = 64M
```

### 3. Redis Configuration
Edit `/etc/redis/redis.conf`:
```ini
maxmemory 256mb
maxmemory-policy allkeys-lru
```

---

## Security Hardening

### 1. Firewall Setup
```bash
# UFW
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 2. Disable PHP Functions
Edit `php.ini`:
```ini
disable_functions = exec,passthru,shell_exec,system,proc_open,popen
```

### 3. Hide Server Information
Nginx: `server_tokens off;`
Apache: `ServerTokens Prod`

---

## Troubleshooting

### Common Issues

#### 1. Permission Errors
```bash
sudo chown -R www-data:www-data /path/to/blog
sudo chmod -R 755 /path/to/blog
sudo chmod -R 775 storage bootstrap/cache
```

#### 2. 500 Internal Server Error
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

#### 3. Images Not Loading
```bash
# Recreate symlink
rm public/storage
php artisan storage:link

# Check permissions
ls -la storage/app/public
```

#### 4. Queue Not Processing
```bash
# Check supervisor status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart blog-worker:*

# Manual test
php artisan queue:work --once
```

#### 5. Cache Issues
```bash
# Clear all caches
php artisan optimize:clear
redis-cli FLUSHALL
```

---

## Maintenance Mode

### Enable Maintenance Mode
```bash
# With custom message
php artisan down --message="Upgrading Database" --retry=60

# Allow specific IPs
php artisan down --secret="upgrade-token"
# Access: https://yourdomain.com/upgrade-token
```

### Disable Maintenance Mode
```bash
php artisan up
```

---

## Rollback Procedure

If deployment fails:

```bash
# 1. Enable maintenance mode
php artisan down

# 2. Rollback database migrations
php artisan migrate:rollback

# 3. Revert to previous code version
git checkout previous-working-commit

# 4. Reinstall dependencies
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build

# 5. Clear caches
php artisan optimize:clear
php artisan optimize

# 6. Disable maintenance mode
php artisan up
```

---

## Monitoring & Logging

### Setup Application Monitoring
Consider using:
- **Laravel Telescope** (development/staging)
- **Sentry** (error tracking)
- **New Relic** or **DataDog** (APM)

### Log Rotation
Create `/etc/logrotate.d/laravel-blog`:
```
/path/to/blog/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

---

## Support

For issues and questions:
- Check logs: `storage/logs/laravel.log`
- Review documentation
- Contact: support@yourdomain.com

---

## Changelog

Keep track of deployments:
```
2025-XX-XX: Initial production deployment
- Version: 1.0.0
- Features: Blog posts, comments, likes, tags, user profiles
```
