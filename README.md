# CommunityTix (v1) — Multi-tenant Community Website + Events + Ticketing

CommunityTix is a low-cost, open-source web platform for community organisations (cultural associations, clubs, non-profits) that need:
- a modern public website (Home + Blog)
- an Events listing with per-event landing pages
- RSVP for free events
- simple ticketing for paid events (PayPal)
- a lightweight Members database
- an admin control panel for governing users

The platform is designed to support multiple organisations via **subdomain tenancy**:
`{tenantSlug}.communitytix.org`

---

## Core business rules (v1)

- **Tenant is determined only by the hostname** (`Host` header). The client must never send `tenant_id`.
- **Admin access**: only **TENANT_GOVERNING** users can access `/admin`.
- **Tenant lockout**: if `tenant_active=false`, block **governing login only**; public site continues to work.
- **Membership types**: Guest / Ordinary / Governing  
  - Ordinary signups are **PENDING** until approved  
  - Guests (from RSVP/checkout) can be promoted to Ordinary  
  - Ordinary can be promoted to Governing
- **Global email uniqueness**: the same email cannot exist across multiple tenants.
- **Events**: FREE RSVP or TICKETED  
  - Ticketed events support **multiple ticket types** (name, price, capacity)
- **Refunds**: basic/manual — governing user marks an order as refunded.
- **On-site payments**:  
  - Cash is recorded in-app  
  - Card-at-door uses PayPal reader/app externally; in-app record is **pending reconciliation** (no instant confirmation)

---

## Tech stack (frozen for v1)

- **App**: Laravel (PHP 8.2/8.3) monolith
- **DB**: PostgreSQL (same server)
- **Web server**: Nginx
- **Hosting**: Single Azure Ubuntu VM
- **Uploads**: Filesystem (on the VM)
- **UI**: Bootstrap 5 + Font Awesome
  - Public theme: Start Bootstrap “Agency”
  - Admin theme: AdminLTE v4

---

## Repository structure (suggested)

