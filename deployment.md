# Deployment guide — InfoKobuleti (infokobuleti.com)

Plain PHP 8.x + MySQL, no Composer. This document matches the current repository layout.

---

## Prerequisites

- PHP **8.1+** (8.2 recommended) with extensions: `pdo_mysql`, `gd`, `mbstring`, `json`, `fileinfo`
- MySQL **8.0** (or compatible), `utf8mb4`
- Apache with `mod_rewrite` **or** equivalent URL rewriting to `index.php` and `admin/index.php`
- Domain DNS pointed at hosting

---

## 1. Repository layout on the server

Upload the project so the **web root** is the folder that contains `index.php`, `.htaccess`, `admin/`, `public/`, etc. (not only the `public/` subfolder).

Expected tree (high level):

```
index.php
bootstrap.php
.htaccess
admin/
app/
config/
install/
lang/
public/
uploads/
views/
storage/
```

---

## 2. Database credentials

Do **not** put passwords in `config/config.php`.

1. Copy `config/db.local.php.example` to **`config/db.local.php`** (this file is gitignored).
2. Set `host`, `dbname`, `user`, `pass`, `charset` for your MySQL user.

`config/database.php` loads `db.local.php` when present; otherwise it falls back to local defaults (only for development).

---

## 3. Import schema

1. Create an empty database in hPanel (or your panel).
2. Open **phpMyAdmin** → select the database → **Import**.
3. Upload and run **`install/database.sql`**.
4. **Immediately** change the seed admin password (`admin@infokobuleti.com` — see comments in `install/database.sql`).

Generate a new bcrypt hash:

```php
<?php
echo password_hash('your_new_password', PASSWORD_BCRYPT, ['cost' => 12]);
```

Then:

```sql
UPDATE users SET password = 'PASTE_HASH_HERE' WHERE email = 'admin@infokobuleti.com';
```

---

## 4. URLs, HTTPS, and `RewriteBase`

- **`BASE_URL`**, **`PUBLIC_BASE_URL`**, **`UPLOAD_URL`**, and **`APP_URL`** are computed from `$_SERVER` in `config/config.php`. You normally **do not** hardcode the site URL.
- If the site lives in a **subdirectory** (e.g. `https://example.com/demo/`), set Apache **`RewriteBase`** in `.htaccess` to that path (e.g. `RewriteBase /demo/`).

Enable HTTPS and force redirect in the hosting panel when available.

---

## 5. `.htaccess` (Apache)

The repo ships a minimal ruleset:

- Forbids direct web access to `config/`, `app/`, `lang/`, `views/`, `install/`, `storage/`, and `bootstrap.php`.
- Routes **`/admin`…** to `admin/index.php`.
- Routes everything else to `index.php` when the request is not an existing file or directory.

**Note:** On some shared hosts, extra directives (e.g. `php_value` in `.htaccess`) are ignored or cause **500** errors. Prefer changing PHP limits in the panel (upload size, memory, timeout).

**Upload limits:** listing photos are validated to **5 MB** per file in code (`IMAGE_MAX_BYTES`). Ensure host `upload_max_filesize` and `post_max_size` allow your form (multiple images).

---

## 6. Writable directories

Ensure the web server user can write:

- **`uploads/`** (and subfolders `uploads/properties/original|medium|thumb` — created automatically on first upload when possible)
- **`storage/cache/`** (homepage cache, if used)

Typical permissions: **755** on folders; if uploads fail, try **775** only if your host requires it.

---

## 7. Admin panel

- **URL:** `https://your-domain.com/admin` (or `…/subdir/admin` if in a subdirectory).
- **There is no separate `/admin/login`.** Admins sign in on the public site: **`/login`** with a user row where `role = 'admin'`.
- After login, open `/admin` again; the same session cookie is used (`SESSION_NAME` = `infokobuleti`).
- Non-admin users who open `/admin` get **404** (by design).

Features implemented: dashboard stats, listings moderation (approve / reject with note), featured toggle (active listings), users list (enable/disable **role `user` only**), site settings (names, contact, featured duration/price, maps key, social URLs).

---

## 8. Google Maps

If you use maps on the public site, set the key in **Admin → Settings** (`settings` table / `google_maps_key`). No need to duplicate it in `config.php`.

Restrict the key by HTTP referrer to your production domain in Google Cloud Console.

---

## 9. Post-deployment checks

**Public**

- [ ] `/` homepage, language switcher `/lang/{ka|ru|en}`
- [ ] `/listings` and `/listings/{slug}`
- [ ] `/kobuleti`, `/contact`
- [ ] Register / login / logout
- [ ] Logged-in user: `/my/dashboard`, create listing, edit, images

**Admin** (as `admin` user)

- [ ] `/admin` dashboard
- [ ] Listings: filter pending, open listing, approve or reject
- [ ] Featured toggle on an **active** listing
- [ ] Users: disable/enable a normal **user** account
- [ ] Settings save

**Security**

- [ ] `https://…/config/database.php` → **403** (or not served)
- [ ] `https://…/app/` → **403**
- [ ] `https://…/views/` → **403**
- [ ] Default admin password changed

---

## 10. Updates after launch

- Deploy changed files over FTP/Git; avoid replacing `config/db.local.php` and `uploads/`.
- **Never** re-import full `install/database.sql` on production (wipes data). Use small SQL migration files if schema changes.

---

## 11. GitHub deployment

```bash
git clone https://github.com/Murvanidz3/infokob.git .
cp config/db.local.php.example config/db.local.php
# edit db.local.php, import install/database.sql, set permissions
```

On **shared hosting without shell**, use ZIP upload + File Manager instead of `git clone`.

---

## Common issues

| Symptom | What to check |
|--------|----------------|
| 500 on every page | `.htaccess` syntax; host compatibility; PHP error log |
| 404 on all pretty URLs | `mod_rewrite` off; wrong `RewriteBase` in subdirectory installs |
| DB connection error | `config/db.local.php`; host often uses `127.0.0.1` not `localhost` |
| CSS/JS 404 | `public/assets/` present; open `/public/assets/css/style.css` directly |
| Images 404 | `uploads/` permissions; `PUBLIC_BASE_URL` wrong only if rewriting/domain is wrong |
| Admin redirects to `/login` | Not logged in or wrong account; need `role = admin` |
| Session lost between `/` and `/admin` | Cookie path/domain; HTTPS mixed content; same site URL for both |

---

## Performance

- Optional homepage cache: `storage/cache/home.cache` (TTL in `config/config.php`).
- Consider host CDN / caching plugins; keep uploads reasonably sized.
