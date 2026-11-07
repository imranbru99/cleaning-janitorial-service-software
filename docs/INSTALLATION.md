# Installation

## Requirements
PHP 8.3+, Composer, Node 20+, SQLite or MySQL 8+, Redis recommended, ext-mbstring

```bash
cd cleaning-and-janitorial-service-software-scheduling-quality-checks
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # or configure MySQL
php artisan migrate --seed
npm install && npm run build
php artisan storage:link
php artisan serve
```

Admin: `/admin`  
Public site: `/`  
API: `/api/v1`

Demo password for all seeded accounts: `Demo@12345`
