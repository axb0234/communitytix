# CommunityTix v1 — Multi-Tenant Community Ticketing Platform

CommunityTix is a multi-tenant web platform for community organisations (cultural associations, clubs, non-profits) that provides:

- A modern, responsive public website with Home, Blog, and Events pages
- RSVP management for free events with capacity tracking
- Online ticketing for paid events via PayPal Orders API v2
- Member management with approval workflows (Guest / Ordinary / Governing)
- A full-featured admin control panel for governing users
- A platform admin dashboard for managing tenants

**Live demo**: [https://moitree.communitytix.org](https://moitree.communitytix.org)

---

## Tech Stack

| Component | Technology |
|-----------|-----------|
| **Framework** | Laravel 12 (PHP 8.3) |
| **Database** | PostgreSQL 16 |
| **Web Server** | Nginx with PHP-FPM |
| **Hosting** | Azure Ubuntu 24.04 VM |
| **SSL** | Let's Encrypt wildcard (DNS-01 via Porkbun) |
| **UI** | Bootstrap 5.3 + Font Awesome 6 |
| **Payments** | PayPal Orders API v2 (sandbox + live) |
| **Mail** | Brevo SMTP relay |

---

## Architecture

### Multi-Tenancy

Tenants are resolved by subdomain: `{slug}.communitytix.org`

- The `ResolveTenant` middleware extracts the subdomain from the `Host` header
- A global Eloquent scope (`TenantScope`) automatically filters all tenant-scoped queries
- The `BelongsToTenant` trait auto-sets `tenant_id` on model creation
- Session cookie domain is set to `.communitytix.org` for cross-subdomain auth

### Security

- CSRF protection on all forms (PayPal webhooks excluded)
- Rate-limited login attempts (5 per minute)
- PayPal credentials encrypted at rest using Laravel's `Crypt` facade
- Tenant isolation via database-level scoping
- Platform admin role check for tenant management

---

## Features

### Public Site
- **Homepage**: Hero section / carousel, upcoming event spotlight, latest blog posts, join CTA
- **Events**: Listing with upcoming/past separation, detail pages with RSVP or ticket purchase
- **Blog**: Index with pagination, full post view with author and date
- **Member Signup**: Application form with pending approval workflow
- **Responsive**: Mobile-first design, works on phones, tablets, and desktops

### Admin Panel (`/admin`)
- **Dashboard**: 6 stat cards (members, pending, events, posts, orders, revenue), recent orders, upcoming events
- **Blog Management**: Create, edit, delete posts with featured images and draft/published status
- **Event Management**: Free (RSVP) and ticketed events with multiple ticket types, event images, capacity tracking
- **Order Management**: View orders, filter by status/event, order details with line items
- **RSVP Management**: View all RSVPs with event filtering
- **Cash Collections**: Record cash payments at events
- **Card at Door (POS)**: Record card payments pending reconciliation
- **Member Management**: View, approve, promote, suspend members with type/status filtering
- **Home Page Content**: Manage carousel items and content blocks
- **Settings**: Organisation details (name, tagline, currency, timezone, logo) and PayPal configuration
- **Responsive**: Collapsible sidebar, scrollable tables, adaptive layouts

### Platform Admin (`/platform`)
- **Tenant Management**: Create, edit, activate/deactivate tenants
- **Admin User Creation**: Set up initial governing user per tenant
- **Subscription Tracking**: Automated nightly check deactivates expired tenants

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/           # Login, forgot/reset password
│   │   ├── Admin/          # Dashboard, blog, events, orders, members, settings
│   │   ├── PublicSite/     # Home, blog, events, member signup
│   │   ├── Platform/       # Platform admin tenant management
│   │   └── Webhook/        # PayPal webhook handler
│   └── Middleware/
│       ├── ResolveTenant.php
│       ├── EnsureTenantExists.php
│       ├── EnsureGoverning.php
│       └── EnsurePlatformAdmin.php
├── Models/
│   ├── Tenant.php, User.php, Member.php
│   ├── BlogPost.php, Event.php, EventImage.php
│   ├── TicketType.php, Order.php, OrderItem.php
│   ├── Rsvp.php, CashCollection.php, PosPayment.php
│   ├── PayPalSetting.php, AuditLog.php
│   ├── Scopes/TenantScope.php
│   └── Traits/BelongsToTenant.php
├── Services/
│   └── PayPalService.php   # PayPal Orders API v2 integration
resources/views/
├── public/                 # Public-facing Blade templates
├── admin/                  # Admin panel templates
├── platform/               # Platform admin templates
└── auth/                   # Authentication templates
```

---

## Setup

### Prerequisites
- PHP 8.3 with extensions: pgsql, mbstring, xml, curl, zip, gd
- PostgreSQL 16+
- Composer
- Nginx

### Installation

```bash
# Clone and install dependencies
git clone https://github.com/axb0234/communitytix.git
cd communitytix
composer install

# Configure environment
cp .env.example .env
php artisan key:generate
# Edit .env with database, mail, PayPal credentials

# Run migrations and seed
php artisan migrate
php artisan db:seed

# Link storage
php artisan storage:link
```

### Environment Variables

Key `.env` settings:

```env
APP_DOMAIN=communitytix.org
SESSION_DOMAIN=.communitytix.org

DB_CONNECTION=pgsql
DB_DATABASE=communitytix

PAYPAL_MODE=sandbox
PAYPAL_SANDBOX_CLIENT_ID=your_client_id
PAYPAL_SANDBOX_CLIENT_SECRET=your_secret

MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com

PLATFORM_ADMIN_EMAIL=admin@example.com
```

### Nginx Configuration

Requires wildcard SSL certificate and server block matching `*.communitytix.org` and `communitytix.org`.

### Scheduler

Add to crontab for automated subscription checks:

```
* * * * * cd /var/www/communitytix && php artisan schedule:run >> /dev/null 2>&1
```

---

## Business Rules

- **Tenant resolution**: Determined solely by `Host` header subdomain
- **Admin access**: Only `TENANT_GOVERNING` users can access `/admin`
- **Tenant lockout**: When `tenant_active=false`, governing login is blocked; public site continues to work
- **Membership flow**: Ordinary signups are `PENDING` until approved by a governing user
- **Member promotion**: Guest -> Ordinary -> Governing
- **Events**: `FREE` (RSVP-based) or `TICKETED` (PayPal checkout)
- **Ticketed events**: Support multiple ticket types with individual pricing and capacity
- **Refunds**: Manual — governing user marks an order as refunded
- **Cash collections**: Recorded in-app by governing users
- **Card-at-door**: Recorded as `PENDING_RECONCILIATION` (external card reader)

---

## License

All rights reserved.
