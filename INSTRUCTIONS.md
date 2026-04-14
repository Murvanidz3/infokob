# 🌊 InfoKobuleti — Project Instructions

## Overview

**InfoKobuleti** is a real estate marketplace platform for Kobuleti, Georgia.
- **Domain:** [infokobuleti.com](https://infokobuleti.com)
- **Stack:** PHP 8.2 + MySQL 8.0 (no frameworks, no Composer)
- **Hosting:** Hostinger shared hosting

---

## 🏗️ Architecture

```
infokob/
├── admin/              ← Admin panel (separate entry point)
│   ├── index.php       ← Admin front controller
│   └── views/          ← Admin templates
├── app/                ← Application logic
│   ├── controllers/    ← 5 controllers (Home, Property, Auth, User, Page)
│   ├── helpers/        ← 5 helpers (Auth, Helpers, Image, Language, SEO)
│   └── models/         ← 3 models (Property, User, Setting)
├── config/
│   ├── config.php      ← Constants, enums, paths
│   ├── database.php    ← PDO singleton connection
│   └── routes.php      ← URI → Controller mapping
├── install/
│   └── database.sql    ← Full schema + seed data
├── lang/               ← Translation files (KA, RU, EN)
├── public/assets/      ← CSS, JS, images
├── scripts/            ← Build/deploy scripts
├── uploads/            ← User-uploaded images (3 sizes)
├── views/              ← PHP templates
│   ├── layouts/        ← Main, auth, user layouts
│   ├── partials/       ← Header, footer, card, pagination
│   ├── home/           ← Homepage
│   ├── properties/     ← Listings + single property
│   ├── auth/           ← Login + register
│   ├── user/           ← Dashboard, create, edit, profile
│   ├── pages/          ← Kobuleti info, contact
│   └── errors/         ← 404
├── cache/              ← File-based cache (auto-generated)
├── index.php           ← Front controller (public entry)
└── .htaccess           ← URL rewriting + security
```

---

## 🔑 Key Features

### Public
- 🏠 **Homepage** — Hero with search, stats counter, featured listings, "how it works"
- 🔍 **Listings** — AJAX-filtered property grid (type, deal, price, rooms, district, sea distance)
- 📄 **Property Detail** — Image gallery with lightbox, specs, features, Google Map, contact (WhatsApp/Telegram/Phone)
- 🌊 **Kobuleti Info** — CMS-managed city guide
- 📬 **Contact** — Form with email delivery

### User
- 👤 **Register/Login** — bcrypt passwords, session auth, rate limiting
- 📊 **Dashboard** — Stats (active/pending/views) + listings table
- ➕ **Create Listing** — Type selector cards, deal toggles, image drag-n-drop upload
- ✏️ **Edit Listing** — Pre-filled form, manage existing images
- 🗑️ **Delete Listing** — With confirmation
- 👤 **Profile** — Edit name, phone, WhatsApp, Telegram, password

### Admin (`/admin`)
- 📊 **Dashboard** — Total stats + pending approval queue
- ✅ **Approve/Reject** — Listed with optional rejection reason
- ⭐ **Feature** — Promote listings (with auto-expiry)
- 👥 **Users** — View all users, block/activate
- ⚙️ **Settings** — Contact info, social media, analytics
- 📝 **Kobuleti CMS** — Add/edit/delete content sections per language

---

## 🌐 Multilingual System

Three languages supported: **Georgian (KA)**, **Russian (RU)**, **English (EN)**

- Translation files: `lang/ka.php`, `lang/ru.php`, `lang/en.php`
- Usage in views: `<?= __('translation_key') ?>`
- Language switch: Header buttons → `/lang/{code}` → stored in session + cookie

### Adding a new translation key:
1. Add the key to **all 3** language files in `lang/`
2. Use it in views with `__('your_key')`

---

## 🖼️ Image Processing

When a user uploads photos:
1. **Validated** — JPEG, PNG, WEBP only, max 5MB
2. **Resized** to 3 versions using PHP GD:
   - `original/` — 1920×1280 (detail page)
   - `medium/` — 800×600 (cards, og:image)
   - `thumb/` — 400×300 (thumbnails)
3. **Stored** in `uploads/properties/{size}/{filename}`

---

## 🔒 Security

| Feature | Implementation |
|---------|---------------|
| SQL Injection | PDO prepared statements everywhere |
| XSS | `e()` function (htmlspecialchars) on all output |
| CSRF | Token per session, verified on every POST |
| Passwords | `bcrypt` via `password_hash()` |
| Rate Limiting | Session-based, 5 attempts / 10 min lockout |
| File Upload | MIME type + extension + getimagesize validation |
| Directory Access | `.htaccess` blocks `/config`, `/app`, `/install`, `/lang` |
| Headers | X-Content-Type-Options, X-Frame-Options, X-XSS-Protection |

---

## 📱 Responsive Design

- **Mobile-first** CSS with breakpoints at 640px, 768px, 1024px
- **Glassmorphism** header with `backdrop-filter`
- **Mobile drawer** navigation (Alpine.js)
- **Pull-up filter** panel on mobile listings page
- **Touch-friendly** — all tap targets ≥ 40px

---

## 🗄️ Database Schema

| Table | Purpose |
|-------|---------|
| `users` | Registration, auth, profile |
| `properties` | All listing data (type, price, specs, location) |
| `property_translations` | Title + description per language |
| `property_images` | Uploaded photos with sort order |
| `kobuleti_info` | CMS sections for city guide |
| `settings` | Key-value site settings |

---

## ⚙️ Configuration

All constants are in `config/config.php`:

| Constant | Description |
|----------|-------------|
| `BASE_URL` | Site root URL (no trailing slash) |
| `GOOGLE_MAPS_KEY` | Google Maps JavaScript API key |
| `ITEMS_PER_PAGE` | Listings per page (default: 12) |
| `SUPPORTED_LANGS` | Available languages |
| `PROPERTY_TYPES` | apartment, house, cottage, land, commercial, hotel_room |
| `DEAL_TYPES` | sale, rent, daily_rent |
| `DISTRICTS` | Kobuleti districts enum |
| `SEA_DISTANCES` | Filter options for sea proximity |

---

## 🔄 URL Routes

### Public
| Method | URL | Controller |
|--------|-----|------------|
| GET | `/` | HomeController@index |
| GET | `/listings` | PropertyController@index |
| GET | `/listings/{slug}` | PropertyController@show |
| GET | `/kobuleti` | PageController@kobuleti |
| GET | `/contact` | PageController@contact |
| POST | `/contact` | PageController@sendContact |
| GET | `/lang/{code}` | LanguageController@set |
| GET | `/sitemap.xml` | PageController@sitemap |

### Auth
| Method | URL | Controller |
|--------|-----|------------|
| GET/POST | `/login` | AuthController |
| GET/POST | `/register` | AuthController |
| GET | `/logout` | AuthController@logout |

### User (requires login)
| Method | URL | Controller |
|--------|-----|------------|
| GET | `/my/dashboard` | UserController@dashboard |
| GET/POST | `/my/listings/create` | UserController@create |
| GET/POST | `/my/listings/{id}/edit` | UserController@edit |
| POST | `/my/listings/{id}/delete` | UserController@delete |
| GET/POST | `/my/profile` | UserController@profile |

### AJAX
| Method | URL | Controller |
|--------|-----|------------|
| GET | `/api/listings` | PropertyController@apiIndex |

### Admin (`/admin/`)
| URL | Action |
|-----|--------|
| `/admin` | Dashboard |
| `/admin/listings` | Manage listings |
| `/admin/listings/{id}/approve` | Approve listing |
| `/admin/listings/{id}/reject` | Reject listing |
| `/admin/listings/{id}/feature` | Feature listing |
| `/admin/users` | Manage users |
| `/admin/settings` | Site settings |
| `/admin/info` | Kobuleti CMS |

---

## 🧪 Local Development

### Requirements
- PHP 8.0+ with GD extension
- MySQL 8.0+
- Apache with `mod_rewrite`

### Setup
```bash
# 1. Clone
git clone https://github.com/Murvanidz3/infokob.git
cd infokob

# 2. Import database
mysql -u root -p < install/database.sql

# 3. Configure
# Edit config/config.php:
#   - DB_HOST, DB_NAME, DB_USER, DB_PASS
#   - BASE_URL (e.g. http://localhost/infokob)

# 4. Create directories
mkdir -p uploads/properties/original uploads/properties/medium uploads/properties/thumb cache

# 5. Point Apache/XAMPP to this directory
# Access http://localhost/infokob
```

### Default Admin Login
- **Email:** `admin@infokobuleti.com`
- **Password:** `admin123` ⚠️ CHANGE IMMEDIATELY

---

## 📦 Dependencies (all CDN — no npm/composer)

| Library | Version | Purpose |
|---------|---------|---------|
| [Alpine.js](https://alpinejs.dev) | 3.x | Reactive UI (mobile menu, tabs, gallery) |
| [Phosphor Icons](https://phosphoricons.com) | 2.0.3 | Icon system |
| [Inter Font](https://fonts.google.com/specimen/Inter) | Variable | Typography |
| [Google Maps JS API](https://developers.google.com/maps/documentation/javascript) | Latest | Property location maps |
