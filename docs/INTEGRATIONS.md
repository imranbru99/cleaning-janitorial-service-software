# Integrations

## AI Hub
Optional. Configure provider keys in `.env` / `config/ai-hub.php`. Use `App\Services\AI\AiAssistantService` which no-ops safely when disabled.

## AI Catalog
Installed for assisted service description/catalog generation. Adapt prompts to cleaning services; do not assume ecommerce product semantics.

## Attendance Hub
Device drivers live in the package. Application manual/mobile attendance writes to `workforce_attendance_logs` via `AttendanceHubAdapter`. Device sync calls package facade when configured; vendor protocols differ.

## Unicode PDF
`UnicodePdfAdapter` prefers Unicode PDF, falls back to DomPDF.

## Universal Slug
Used on Service, ServiceCategory, CmsPage, BlogPost.

## Maps
Location fields exist; travel-time claims require a configured mapping provider (not hardcoded).

## Payments
Adapter pattern for Stripe/PayPal/bKash/Nagad/manual. Success redirects are insufficient without gateway verification.
