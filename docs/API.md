# API v1

Base: `/api/v1`

## Auth
`POST /login` `{email,password,device_name?}` → `{token,user}`

`Authorization: Bearer {token}`

`GET /me` · `POST /logout`

## Resources
| Method | Path | Module |
|---|---|---|
| CRUD | `/customers` | crm |
| GET | `/services` | catalog |
| CRUD | `/bookings` | bookings |
| GET/PATCH | `/jobs` | jobs |
| GET/POST | `/inspections` | quality |
| GET | `/invoices` | invoicing |

Tenant isolation is enforced by global scopes after tenancy initialization (user tenant / `X-Tenant` header).

Gateway payments must be verified server-side; browser redirects alone never mark payments complete.
