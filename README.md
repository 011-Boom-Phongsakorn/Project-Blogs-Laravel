# Laravel Blog Platform

A modern, full-featured blog platform built with Laravel 12, featuring a Medium-like interface with social features.

## Features

### Core Features
- 📝 **Post Management** - Create, edit, and publish blog posts with rich content
- 🏷️ **Tagging System** - Organize posts with tags
- 💬 **Comments** - Engage with readers through comments
- ❤️ **Likes** - Like posts and see popularity
- 🔖 **Bookmarks** - Save posts for later reading
- 👥 **User Profiles** - Author profiles with follower system
- 🔍 **Advanced Search** - Search posts by title, content, tags, and author

### Social Features
- 👤 **User Following** - Follow your favorite authors
- 📊 **User Stats** - Post counts, follower/following counts
- 📱 **Social Interactions** - Real-time like and bookmark counts

### Content Features
- ✍️ **Draft/Publish Workflow** - Save drafts before publishing
- 🖼️ **Image Upload** - Featured images with automatic optimization
- 🎨 **Clean UI** - Medium-inspired design with TailwindCSS
- 📱 **Responsive Design** - Works on all devices
- ⚡ **Fast Performance** - Optimized queries and caching

### Security Features
- 🔐 **Authentication** - Laravel Breeze authentication
- 🛡️ **Authorization** - Policy-based access control
- 🚦 **Rate Limiting** - Protection against abuse
- 🔒 **CSRF Protection** - Built-in security
- ✅ **Input Validation** - Comprehensive form validation

## Tech Stack

- **Framework**: Laravel 12
- **Frontend**: Blade Templates, TailwindCSS, Alpine.js
- **Database**: MySQL/SQLite
- **Cache**: Redis
- **Image Processing**: Intervention Image
- **Build Tool**: Vite

## Requirements

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18.x or higher
- MySQL 8.0+ or PostgreSQL 13+ (Production)
- Redis 6.0+ (Recommended for production)

### PHP Extensions
- BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML
- GD or Imagick (for image processing)

## Installation

### 1. Clone Repository
```bash
git clone <repository-url>
cd blogs
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env
```

### 4. Database Setup
```bash
# Run migrations
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed
```

### 5. Storage Setup
```bash
# Create storage symlink
php artisan storage:link
```

### 6. Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start Development Server
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Development

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=PostTest

# Run with coverage
php artisan test --coverage
```

### Code Style
```bash
# Format code with Laravel Pint
vendor/bin/pint
```

### Database Commands
```bash
# Fresh migration
php artisan migrate:fresh

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Rollback
php artisan migrate:rollback
```

### Cache Commands
```bash
# Clear all caches
php artisan optimize:clear

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Project Structure

```
blogs/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Application controllers
│   │   └── Requests/         # Form requests
│   ├── Models/               # Eloquent models
│   └── Policies/             # Authorization policies
├── database/
│   ├── migrations/           # Database migrations
│   ├── factories/            # Model factories
│   └── seeders/              # Database seeders
├── resources/
│   ├── views/                # Blade templates
│   ├── css/                  # Stylesheets
│   └── js/                   # JavaScript files
├── routes/
│   └── web.php               # Web routes
├── tests/
│   └── Feature/              # Feature tests
└── public/                   # Public assets
```

## API Endpoints

### Public Routes
- `GET /` - Home page (post list)
- `GET /posts` - All posts
- `GET /posts/{slug}` - Single post
- `GET /search` - Search posts
- `GET /tags` - All tags
- `GET /tags/{tag}` - Posts by tag
- `GET /users/{user}` - User profile

### Authenticated Routes
- `POST /posts` - Create post
- `PUT /posts/{slug}` - Update post
- `DELETE /posts/{slug}` - Delete post
- `POST /posts/{id}/like` - Like/unlike post
- `POST /posts/{id}/bookmark` - Bookmark/unbookmark post
- `POST /users/{id}/follow` - Follow/unfollow user
- `POST /posts/{id}/comments` - Add comment
- `DELETE /comments/{id}` - Delete comment

## Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed deployment instructions.

### Quick Deployment
```bash
# Run deployment script
chmod +x deploy.sh
./deploy.sh
```

### Manual Optimization
```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan migrate --force
php artisan optimize
```

## Configuration

### Key Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=blog_production
DB_USERNAME=blog_user
DB_PASSWORD=your_password

CACHE_STORE=redis
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
```

## Performance

### Optimizations Implemented
- ✅ Database indexing on frequently queried columns
- ✅ Eager loading to prevent N+1 queries
- ✅ Redis caching for popular tags
- ✅ Image lazy loading
- ✅ Optimized production assets (gzip: 14KB CSS, 35KB JS)
- ✅ Route and view caching
- ✅ Rate limiting on all routes

## Security

### Security Features
- ✅ CSRF protection on all forms
- ✅ Rate limiting (10-60 requests/minute per route)
- ✅ XSS protection (escaped output)
- ✅ SQL injection protection (Eloquent ORM)
- ✅ Authorization policies
- ✅ File upload validation
- ✅ Password hashing (bcrypt)

## Testing

The project includes comprehensive test coverage:
- 56 tests total
- 31 custom feature tests (Posts, Likes, Follows, Bookmarks)
- 25 authentication tests
- 119 assertions

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Troubleshooting

### Common Issues

**Permission Errors**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Images Not Loading**
```bash
php artisan storage:link
```

**Cache Issues**
```bash
php artisan optimize:clear
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Credits

Built with:
- [Laravel 12](https://laravel.com)
- [TailwindCSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)
- [Intervention Image](http://image.intervention.io)

## Support

For issues and questions:
- Check [DEPLOYMENT.md](DEPLOYMENT.md) for deployment help
- Review application logs: `storage/logs/laravel.log`
- Open an issue on GitHub

---

**Version**: 1.0.0
**Last Updated**: 2025-09-30
