# Roles and Permissions

Uses Spatie Laravel Permission.

## Platform
- super_admin, platform_admin (+ support/finance/marketing/developer roles available for extension)

## Business demo roles
| Role | Focus |
|---|---|
| business_owner | Full tenant (non-platform) access |
| operations_manager | CRM, bookings, scheduling, jobs, quality |
| branch_manager | Branch bookings/workforce/inventory |
| quality_inspector | Inspections |
| cleaning_supervisor | Job execution oversight |
| cleaning_staff | Assigned jobs + attendance |
| accountant | Invoices, payments, expenses |
| customer | Portal only |

Permissions are module-derived (`crm.manage`, `quality.inspect`, etc.). Custom roles can be created per tenant using Spatie APIs.
