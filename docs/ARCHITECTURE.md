# Architecture

CleanOps is a modular multi-tenant Laravel 12 SaaS for cleaning and janitorial businesses.

## Layers
- **Public website / CMS**: Blade marketing pages + CMS models (`cms_pages`, `blog_posts`, `faqs`, `testimonials`)
- **Admin**: Filament v3 at `/admin` (primary admin). `imrandevbd/laravel-admin-panel` is installed at `/utility-admin` for reusable utilities only — Filament remains primary.
- **Customer portal**: `/portal` authenticated self-service
- **API**: versioned REST at `/api/v1` with Sanctum
- **Domain services**: Booking, Inspection scoring, Invoicing, ModuleManager, TenantManager, Automation, AI adapters

## Tenancy
- Shared database, `tenant_id` on tenant-owned tables
- `BelongsToTenant` global scope + auto-assignment via `App\Support\Tenancy`
- Resolution order: `X-Tenant` header → session → domain → subdomain → authenticated user tenant
- Background jobs and API requests must initialize tenancy before querying

## Module engine
- Registry in `config/modules.php`
- Runtime state in `modules` + `tenant_modules`
- `ModuleManager` enforces dependencies, circular-dependency checks, and non-destructive deactivation
- Filament resources call `shouldRegisterNavigation()` / `canAccess()` against active modules
- API routes use `module:` middleware

## Packages
| Package | Role | Notes |
|---|---|---|
| filament/filament | Primary admin | `/admin` |
| imrandevbd/laravel-admin-panel | Utility panel | Prefixed `/utility-admin` to avoid conflict |
| imrandevbd/laravel-ai-hub | Multi-provider AI | Optional; guarded by config/keys |
| imrandevbd/laravel-ai-catalog | AI catalog assist | Adapted for services |
| imrandevbd/laravel-attendance-hub | Device attendance | Package owns `attendance_logs`; app workforce attendance uses `workforce_attendance_logs` |
| imrandevbd/laravel-unicode-pdf | Multilingual PDF | Adapter with DomPDF fallback |
| imrandevbd/laravel-universal-slug | Unicode slugs | Services, CMS, blog |
| imrandevbd/drutogoti | Performance diagnostics | Disabled by default (`DRUTOGOTI_ENABLED=false`) |
