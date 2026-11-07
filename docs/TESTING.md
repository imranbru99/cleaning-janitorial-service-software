# Testing

```bash
php artisan test
```

Covered:
- Demo auth / Sanctum tokens
- Tenant isolation
- Module activate/deactivate + dependency protection
- Booking → assignment → invoice → payment → failed inspection corrective action
- Public homepage + Filament login

Use `RefreshDatabase` + `DemoSeeder` for feature tests.
