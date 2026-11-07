# Deployment

## CI / GitHub Actions
GitHub Actions are **disabled** for this project. Do not add `.github/workflows/*`. Run tests and builds locally (`composer test`, `npm run build`) before deploy.

## Suggested stack
Nginx + PHP-FPM 8.3 + MySQL 8 + Redis + Supervisor

## Supervisor programs
- `php artisan queue:work --sleep=1 --tries=3`
- `php artisan schedule:work` (or cron `* * * * * php artisan schedule:run`)

## Checklist
1. `APP_ENV=production`, `APP_DEBUG=false`
2. Strong `APP_KEY`
3. MySQL credentials + Redis
4. `DRUTOGOTI_ENABLED=false` unless restricted operators need it
5. `ADMIN_PANEL_PREFIX=utility-admin` (keep Filament on `/admin`)
6. HTTPS only; secure session cookies
7. Backups for database + `storage/app`
8. Rotate demo credentials — never ship `Demo@12345` to production
