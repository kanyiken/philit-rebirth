# Pipii.co.ke — Spectacular Car Selling Platform

A PHP + MySQL + Tailwind prototype for an admin-led car-selling platform with agent memberships, 1-month spotlight listings, and AJAX endpoints.

## Features
- Admin publishes listings; agents get 30 active listings for KES 15,000 per year (vs. KES 1,000 per listing).
- Listings expire after one month and can be renewed via API.
- Tailwind-driven showroom UI with hero sliders, grid cards, and rounded "Starlight"-style buttons.
- Simple authentication via API tokens with admin-gated agent creation.

## Endpoints
- `POST api/login.php` — email/password login, returns token.
- `POST api/admin_create_agent.php` — admin-only agent creation (header `X-Admin-Key`).
- `POST api/create_listing.php` — create listing for logged-in admin/agent; enforces limits.
- `POST api/renew_listing.php` — renew listing for another month (owner or admin).
- `GET api/listings.php` — fetch active listings.

## Frontend
Open `public/index.php` in your webserver root. The page fetches listings via AJAX and provides login, post, and renew flows.

## Configuration
- Switch environments with one variable while testing: set `PIPII_ENV=local` to use the local DB credentials (see `config.php`). Default is `prod`.
- Update `config.php` with your MySQL credentials and `ADMIN_API_KEY`. Import `schema.sql` to create the database schema.
- Health endpoints: `api/health.php` (JSON) and `public/doctor.php` (UI) gate the app on every load and run DB/CDN/asset/security checks.

## Quick start (local)
```bash
cp config.php{,.local} # edit with credentials
php -S 0.0.0.0:8000 -t public
```
Then visit `http://localhost:8000`.
