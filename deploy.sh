#!/bin/bash

# Laravel Blog Deployment Script
# This script optimizes Laravel for production deployment

echo "🚀 Starting deployment process..."

# 1. Put application in maintenance mode
echo "📦 Enabling maintenance mode..."
php artisan down

# 2. Pull latest changes (if using git)
echo "📥 Pulling latest changes..."
git pull origin main

# 3. Install/update dependencies
echo "📚 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "📚 Installing NPM dependencies..."
npm ci --production

# 4. Build frontend assets
echo "🎨 Building frontend assets..."
npm run build

# 5. Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# 6. Clear and cache configuration
echo "⚡ Optimizing Laravel..."
php artisan config:clear
php artisan config:cache

php artisan route:clear
php artisan route:cache

php artisan view:clear
php artisan view:cache

php artisan event:cache

php artisan optimize

# 7. Clear application cache
echo "🧹 Clearing application cache..."
php artisan cache:clear

# 8. Storage link (if not exists)
echo "🔗 Creating storage symlink..."
php artisan storage:link

# 9. Set proper permissions
echo "🔒 Setting file permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 10. Restart queue workers
echo "🔄 Restarting queue workers..."
php artisan queue:restart

# 11. Bring application back online
echo "✅ Disabling maintenance mode..."
php artisan up

echo "🎉 Deployment completed successfully!"
