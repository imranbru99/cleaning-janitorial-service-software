# Database

Primary schema is created in `database/migrations/2026_11_07_180000_create_cleaning_platform_tables.php`.

## Conventions
- Money: `decimal(15,2)`
- Tenant ownership via `tenant_id` FK
- Soft deletes on customers, bookings, jobs, invoices, employees, assets, inventory
- Indexes on tenant + status / schedule date pairs

## Important table notes
- Queue jobs table remains Laravel `jobs`; operational cleaning jobs use `cleaning_jobs`
- Package `attendance_logs` is reserved by Attendance Hub; application attendance is `workforce_attendance_logs`
- AI Hub / AI Catalog / Drutogoti / slug history tables are published from packages

See `ERD.md` for entity relationships.
