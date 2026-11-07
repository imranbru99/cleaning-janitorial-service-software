# Modules

Configured in `config/modules.php` and synced to `modules` by `ModuleManager::syncRegistry()`.

## Core (cannot disable)
- dashboard, settings, users

## Dependency highlights
- bookings → crm, catalog
- scheduling → bookings, workforce
- jobs → bookings, workforce
- quality → jobs
- payments → invoicing
- payroll → workforce, attendance
- procurement → inventory
- franchise → branches
- portal → crm, bookings

## Rules
1. Disabled modules disappear from Filament navigation
2. API endpoints abort 403 via `module:` middleware
3. Historical records are retained
4. Re-activation restores functionality
5. Dependents block deactivation of required modules
