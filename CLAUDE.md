# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 8 API-based order management system called "SOD API" that serves as the backend for an e-commerce platform. It provides two main API interfaces:

- **Customer Facing API (Front)**: Product browsing, cart management, checkout, user accounts, wishlists, reviews
- **Vendor API**: Product management, order management, customer management, inventory, financials, settings

## Architecture

The application follows Laravel's MVC pattern with API-first design:

- **Models** (`app/Models/`): Eloquent models for database entities (Order, Product, User, Cart, etc.)
- **Controllers** (`app/Http/Controllers/`): Split into `Front/` (customer-facing) and `Vendor/` (admin) controllers
- **API Routes**: Main routes in `routes/api.php`, vendor routes in `routes/api/vendor.php`
- **Jobs** (`app/Jobs/`): Background processing for courier status checks and SMS sending
- **Resources** (`app/Http/Resources/`): API response transformers for consistent JSON output
- **Helper Functions**: `app/Helper/General.php` and `app/Helper/Query.php` are autoloaded

## Development Commands

### Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

### Development Server
```bash
php artisan serve
```

### Frontend Assets
```bash
npm install
npm run dev          # Development build
npm run watch        # Watch for changes
npm run production   # Production build
```

### Testing
```bash
php artisan test
./vendor/bin/phpunit
```

### Database Operations
```bash
php artisan migrate
php artisan migrate:rollback
php artisan db:seed
php artisan tinker
```

### Queue Processing
```bash
php artisan queue:work
```

## Key Integrations

- **Authentication**: Laravel Sanctum for API tokens, separate auth guards for customers and vendors
- **File Storage**: AWS S3 and Dropbox integration via Flysystem
- **PDF Generation**: mPDF for invoice generation
- **Excel Export**: Maatwebsite Excel for order exports
- **Image Processing**: Intervention Image for product images
- **SMS**: Background jobs for SMS notifications
- **Courier Integration**: Multiple courier service APIs (ECourier, RedX, SteadFast, Sundarban, USB)
- **Backup**: Spatie Laravel Backup for automated backups

## Database Structure

Key entities and relationships:
- **Users**: Customer accounts with addresses and orders
- **Vendors**: Multi-vendor support with separate vendor users
- **Products**: With categories, subcategories, prices, and inventory tracking
- **Orders**: With order items, status history, and courier tracking
- **Cart**: Session-based shopping cart
- **Reviews**: Product reviews by customers

## Docker Setup

The project includes Docker configuration:
- `docker-compose.yml`: Multi-container setup
- `docker/php/Dockerfile`: Custom PHP container
- `docker/nginx/default.conf`: Nginx configuration

## API Structure

- **Health Check**: `GET /api/health`
- **Customer API**: All routes under `/api/` (products, cart, checkout, account)
- **Vendor API**: All routes under `/api/vendor/` (management interfaces)
- **Authentication**: Token-based authentication for both customer and vendor APIs

## Important Notes

- The application uses custom helper functions that are autoloaded
- Background jobs are used extensively for external API calls
- Multiple courier integrations require proper configuration
- File uploads support both local and cloud storage
- The system supports both single and multi-vendor operations