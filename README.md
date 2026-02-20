# CommunityTix v1 — Multi-Tenant Community Ticketing Platform

CommunityTix is a multi-tenant web platform for community organisations (cultural associations, clubs, non-profits) that provides:

- A modern, responsive public website with Home, Blog, and Events pages
- RSVP management for free events with capacity tracking
- Online ticketing for paid events via PayPal Orders API v2
- "Pay What You Can" (PWYC) flexible pricing for ticketed events
- Member management with approval workflows (Guest / Ordinary / Governing)
- A full-featured admin control panel for governing users
- A platform admin dashboard for managing tenants across the platform

---

## Live Demo

A fully-populated demo tenant is available for exploration:

| | |
|---|---|
| **Public Site** | [https://demo.communitytix.org](https://demo.communitytix.org) |
| **Admin Panel** | [https://demo.communitytix.org/admin](https://demo.communitytix.org/admin) |
| **Login Email** | `governor@democommunitytix.org` |
| **Password** | `demo1` |

### What to explore

**Public Site** (no login required):
- **Homepage** — carousel, upcoming events, latest blog posts, membership CTA
- **Events** — 20 events across 2026 (10 past, 10 upcoming) mixing FREE and TICKETED types
- **Event Detail** — RSVP for free events; ticket selection + PayPal checkout for paid events
- **Pay What You Can** — 5 events have PWYC enabled with suggested amount buttons and custom input
- **Blog** — 4 posts with featured images covering community topics
- **Member Signup** — `/join` form with pending-approval workflow

**Admin Panel** (login with demo credentials above):
- **Dashboard** — stat cards (members, events, orders, revenue), recent orders, upcoming events
- **Home Page** — manage carousel items and content blocks displayed on the public homepage
- **Blog Posts** — create/edit/delete posts with featured images, WYSIWYG editor, draft/published status
- **Events** — full event CRUD with ticket types, PWYC pricing, event images, capacity management
- **Orders** — all PayPal orders with status filtering, order detail view, refund marking
- **RSVPs** — all free-event RSVPs with event filtering and CSV export
- **Cash Collections** — record and track cash payments collected at events
- **Card at Door (POS)** — record card payments from external readers pending reconciliation
- **Members** — view, approve, promote, suspend members; filter by type and status
- **Settings** — organisation details (name, tagline, logo, currency, timezone) and PayPal configuration
- **Help & Guide** — comprehensive admin documentation with annotated screenshots

> **Note:** PayPal checkout on the demo site uses sandbox mode. You can test the checkout flow using [PayPal sandbox test accounts](https://developer.paypal.com/tools/sandbox/accounts/).

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
| **Fonts** | Noto Sans family (Latin, Bengali, Devanagari, CJK) |
| **Rich Text** | Summernote WYSIWYG editor |
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

### User & Role Model

CommunityTix uses a three-table user/membership structure:

- **`users`** — authentication credentials + `platform_role` (user / PLATFORM_ADMIN)
- **`tenant_users`** — bridge table linking users to tenants with `role_in_tenant` (MEMBER / TENANT_GOVERNING)
- **`members`** — tenant-scoped member profiles with `member_type` (GUEST / ORDINARY / GOVERNING) and `status` (ACTIVE / PENDING_APPROVAL / SUSPENDED)

A user with `TENANT_GOVERNING` role in `tenant_users` can access the admin panel for that tenant. Platform admins (identified by `platform_role = PLATFORM_ADMIN`) can manage all tenants via `/platform`.

### Security

- CSRF protection on all forms (PayPal webhooks excluded)
- Rate-limited login attempts (5 per minute)
- PayPal credentials encrypted at rest using Laravel's `Crypt` facade
- Tenant isolation via database-level scoping (global scope on all queries)
- Platform admin role check for tenant management
- Governing role check for admin panel access

---

## Features

### Public Site
- **Homepage**: Hero carousel, upcoming event spotlight, latest blog posts, content blocks, join CTA
- **Events**: Listing with upcoming/past tabs, detail pages with RSVP or ticket purchase
- **Pay What You Can**: Suggested amount buttons + custom input for PWYC-enabled ticketed events
- **Blog**: Index with pagination, full post view with author and date
- **Member Signup**: Application form with pending approval workflow
- **Responsive**: Mobile-first design, works on phones, tablets, and desktops
- **Multilingual Fonts**: Noto Sans family supports Latin, Bengali, Devanagari, Chinese, Japanese, Korean scripts

### Admin Panel (`/admin`)
- **Dashboard**: 6 stat cards (members, pending, events, posts, orders, revenue), recent orders, upcoming events
- **Blog Management**: Create, edit, delete posts with featured images, WYSIWYG editor, and draft/published status
- **Event Management**: Free (RSVP) and ticketed events with multiple ticket types, PWYC pricing, event images, capacity tracking
- **Order Management**: View orders, filter by status/event, order details with line items, refund marking
- **RSVP Management**: View all RSVPs with event filtering and CSV export
- **Cash Collections**: Record and export cash payments at events
- **Card at Door (POS)**: Record card payments pending reconciliation with CSV export
- **Member Management**: View, approve, promote, suspend members with type/status filtering
- **Home Page Content**: Manage carousel items and content blocks with WYSIWYG editing
- **Settings**: Organisation details (name, tagline, currency, timezone, logo) and PayPal configuration
- **Help & Guide**: Built-in admin documentation with annotated screenshots and step-by-step instructions
- **Responsive**: Collapsible sidebar, scrollable tables, adaptive layouts for mobile and tablet

### Platform Admin (`/platform`)
- **Tenant Management**: Create, edit, activate/deactivate tenants
- **Tenant Purge**: Safely delete all tenant data with confirmation safeguard (type slug to confirm)
- **Admin User Creation**: Set up initial governing user per tenant
- **Subscription Tracking**: Automated nightly check deactivates expired tenants

---

## Project Structure

```
app/
├── Console/Commands/
│   └── SeedDemoContent.php     # Demo tenant content seeder
├── Http/
│   ├── Controllers/
│   │   ├── Auth/               # Login, forgot/reset password
│   │   ├── Admin/              # Dashboard, blog, events, orders, members, settings
│   │   ├── PublicSite/         # Home, blog, events, member signup
│   │   ├── Platform/           # Platform admin tenant management
│   │   └── Webhook/            # PayPal webhook handler
│   └── Middleware/
│       ├── ResolveTenant.php
│       ├── EnsureTenantExists.php
│       ├── EnsureGoverning.php
│       └── EnsurePlatformAdmin.php
├── Models/
│   ├── Tenant.php, User.php, Member.php, TenantUser.php
│   ├── BlogPost.php, Event.php, EventImage.php
│   ├── TicketType.php, Order.php, OrderItem.php
│   ├── Rsvp.php, CashCollection.php, PosPayment.php
│   ├── CarouselItem.php, ContentBlock.php
│   ├── PayPalSetting.php, AuditLog.php
│   ├── Scopes/TenantScope.php
│   └── Traits/BelongsToTenant.php
├── Services/
│   ├── PayPalService.php       # PayPal Orders API v2 integration
│   └── TenantPurgeService.php  # Safe tenant data deletion
resources/views/
├── public/                     # Public-facing Blade templates
├── admin/                      # Admin panel templates (including help page)
├── platform/                   # Platform admin templates
├── auth/                       # Authentication templates
└── emails/                     # Order confirmation email template
public/
└── help-images/                # Admin help page screenshots
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

# (Optional) Seed demo tenant content
php artisan demo:seed
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

### Demo Tenant Management

```bash
# Seed demo content (creates events, blog posts, members, orders, etc.)
php artisan demo:seed

# Reset and re-seed demo content
php artisan demo:seed --purge-first
```

---

## Business Rules

- **Tenant resolution**: Determined solely by `Host` header subdomain
- **Admin access**: Only `TENANT_GOVERNING` users can access `/admin`
- **Platform access**: Only `PLATFORM_ADMIN` users can access `/platform`
- **Tenant lockout**: When `tenant_active=false`, governing login is blocked; public site continues to work
- **Membership flow**: Ordinary signups are `PENDING_APPROVAL` until approved by a governing user
- **Member promotion**: Guest -> Ordinary -> Governing
- **Events**: `FREE` (RSVP-based) or `TICKETED` (PayPal checkout)
- **PWYC**: Ticketed events can optionally enable "Pay What You Can" with up to 3 suggested amounts
- **Ticketed events**: Support multiple ticket types with individual pricing and capacity
- **Refunds**: Manual — governing user marks an order as refunded in the admin panel
- **Cash collections**: Recorded in-app by governing users for events
- **Card-at-door**: Recorded as `PENDING_RECONCILIATION` (external card reader, reconciled manually)
- **Order confirmation**: Automated email sent on successful PayPal payment via Brevo SMTP

---

## License

All rights reserved.
