# Project instructions — InfoKobuleti

Use this file as the source of truth for **architecture and conventions**. It reflects the **current codebase** (Phases 1–4 implemented).

| Field | Value |
|--------|--------|
| Name | InfoKobuleti |
| Stack | PHP 8.1+, MySQL, PDO, no Composer, no framework |
| Public entry | `index.php` |
| Admin entry | `admin/index.php` |
| GitHub | `https://github.com/Murvanidz3/infokob.git` |

---

## Core rules

1. **PDO only** — prepared statements; no string interpolation in SQL.
2. **Escape output** — use `Helpers::e()` / `htmlspecialchars` for HTML.
3. **CSRF on every POST** — token in session (`CSRF_TOKEN_KEY`); forms use `name="csrf"`; validate with `Helpers::verifyCsrf()`.
4. **Passwords** — `password_hash` / `password_verify` (bcrypt).
5. **Listings go live only when `status = active`** after admin approval (or seed data).
6. **UTF-8** — `utf8mb4` DB, `ENT_QUOTES | ENT_HTML5` where applicable.
7. **Front controllers** — all public routes in `config/routes.php`; admin routes in `config/admin_routes.php`.

---

## Routing

- **Public:** `Router::match()` on the request path (after stripping script directory) against `config/routes.php`.
- **Admin:** same router against `config/admin_routes.php`; paths are relative to `/admin` (e.g. `/admin/properties` → `/properties` internally after `stripBasePath`).
- **Apache:** root `.htaccess` sends `/admin…` to `admin/index.php` and other non-files to `index.php`.

---

## Configuration

- **`config/config.php`** — defines `BASE_URL` (current script directory), `PUBLIC_BASE_URL` (site root; parent of `/admin` when running admin), `UPLOAD_*`, constants for images, session, i18n, rate limits. **No DB credentials here.**
- **`config/database.php`** — PDO singleton; reads **`config/db.local.php`** if present (`db.local.php.example` template).
- **`bootstrap.php`** — loads config, database, helpers, `Router`, `View`, `SEO`, `Setting`.

---

## MVC shape

- **Controllers** (`app/controllers/*.php`) — request handling, auth checks, call models, `View::render($view, $data, $layout)`.
- **Models** — static methods, return arrays/`null`; no HTML.
- **Views** (`views/…`) — PHP templates; layouts in `views/layouts/` (`main`, `user`, `auth`, `admin`).

---

## Auth

- **Session:** `user_id`, `user_role`, `user_name`, `user_email` (see `Auth::loginWithUser`).
- **`Auth::requireAuth()`** — redirects to **`PUBLIC_BASE_URL . '/login'`** if guest.
- **`Auth::requireAdmin()`** — if guest → redirect to public **`/login`**; if logged in but not `role === 'admin'` → **404** HTML (no admin disclosure).

Admin does **not** use a separate admin login route.

---

## Helpers (reference)

| Piece | Role |
|--------|------|
| `Helpers::__()` | Lang string from `lang/{code}.php` |
| `Helpers::e()` | HTML escape |
| `Helpers::csrfToken()` / `verifyCsrf()` | CSRF |
| `Helpers::slug()` | URL slug (Georgian transliteration) |
| `Helpers::formatPrice()` | Price + currency + negotiable |
| `Helpers::asset()` | Static URL under `PUBLIC_BASE_URL/public/assets/` |
| `Helpers::restructureUploadedFiles()` | Normalizes `$_FILES['images']` |
| `Language::get()` / `init()` | Current lang (`ka` default) |
| `Image::validateUpload`, `processAndSave`, `getImageUrl`, `deleteFiles` | Uploads under `uploads/properties/{original,medium,thumb}` |
| `View::render`, `View::partial` | Layouts |
| `SEO::defaultMeta()` | Meta defaults |

---

## i18n

- Languages: **`ka`** (default), **`ru`**, **`en`** — PHP arrays in `lang/*.php`.
- Switch: **`GET /lang/{code}`** (`LanguageController`).
- Property copy: `property_translations` joined by `lang`.
- **Admin UI** also uses `Helpers::__()` (strings in `lang/*`); not “Georgian-only” in code.

---

## Users & listings (implemented behaviour)

- **Register / login / logout** — `AuthController`; bcrypt; rate limits on failed login / register.
- **User area** — `/my/dashboard`, `/my/listings`, `/my/listings/create` (3-step form), edit/update, delete, mark sold, archive, profile.
- **Ownership** — models/controllers scope by `user_id` for non-admin actions.
- **Edit:** owners can edit their listings via `Property::updateForUser` (keep moderation rules in mind if you change status on edit).
- **Images** — at least one image on create; formats jpg/png/webp; max size `IMAGE_MAX_BYTES`.
- **Featured** — admin toggles on **active** listings; duration from setting `featured_duration_days`; expiry handled by `Property::deactivateExpiredFeatured()` on key public requests.

---

## Listing visibility (public)

- Public listing queries require **`status = active`** (and deal/type filters as implemented in `Property::buildWhere`).
- **Sold** / **archived** handling follows `PropertyController` / views (sold may still appear where designed).

---

## Admin panel (Phase 4)

- **Routes:** `config/admin_routes.php`.
- **Controller:** `AdminController` — dashboard, properties list + detail, approve/reject, featured toggle, users (`user` role only for activate/deactivate), settings keys whitelist in controller.
- **CSRF** on all admin POSTs.

**Not in scope of current admin:** separate Kobuleti HTML CMS editor, bulk translation editor, dedicated admin login page.

---

## Security checklist for new code

- New POST handler → CSRF + validation + redirect/flash pattern.
- New DB access → prepared statements in a model.
- New HTML output → escape.
- Admin-only actions → `Auth::requireAdmin()` first line.
- File uploads → `Image::validateUpload` path; never trust client MIME/extension alone.

---

## Build phases (status)

| Phase | Scope | Status |
|-------|--------|--------|
| 1 | Config, PDO, routes, bootstrap, install SQL, core helpers | Done |
| 2 | Public site, listings, property page, Kobuleti, contact, assets | Done |
| 3 | Users, dashboard, CRUD listings, profile, CSRF, validation | Done |
| 4 | Admin router, moderation, users, settings, featured | Done |
| 5 | Sitemap, robots, JSON-LD, extra polish, audits | Planned / partial |

---

## Dependencies (allowed)

- **CDN:** Alpine.js, Phosphor Icons, Google Fonts, Google Maps (if used).
- **No** Composer packages, no jQuery, no Bootstrap (custom CSS).

---

## Files not to commit

- `config/db.local.php`
- Contents of `uploads/*` (keep `.gitkeep` only)
- `storage/cache/*` (except `.gitkeep`)

---

## Testing hints

- Mobile width ~375px, three languages, empty states, CSRF tampering, wrong user edit URL, guest `/admin`, non-admin `/admin`, image oversize / bad type.

after making changes make push to git 