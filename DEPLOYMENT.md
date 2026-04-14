# 🚀 InfoKobuleti — Deployment Guide

## How Auto-Deploy Works

Every time you `git push` to the `main` branch, GitHub Actions automatically:

1. **Injects production database credentials** from GitHub Secrets
2. **Sets production mode** (debug off, errors hidden, BASE_URL to `infokobuleti.com`)
3. **Deploys all files** to Hostinger via FTP into `public_html/`

```
[Your PC] → git push → [GitHub] → Actions → FTP → [Hostinger public_html/]
```

---

## 🔑 Step 1: Set Up GitHub Secrets

Go to your GitHub repo → **Settings** → **Secrets and variables** → **Actions** → **New repository secret**

Add these **6 secrets**:

| Secret Name     | Where to Find It                          | Example Value                |
|-----------------|-------------------------------------------|------------------------------|
| `FTP_SERVER`    | Hostinger hPanel → Files → FTP Accounts   | `ftp.infokobuleti.com`       |
| `FTP_USERNAME`  | Hostinger hPanel → Files → FTP Accounts   | `u123456789`                 |
| `FTP_PASSWORD`  | The FTP password you set                  | `YourSecurePassword123!`     |
| `DB_HOST`       | Hostinger hPanel → Databases → MySQL      | `srv1234.hstgr.io`          |
| `DB_NAME`       | Hostinger hPanel → Databases → MySQL      | `u123456789_infokobuleti`    |
| `DB_USER`       | Hostinger hPanel → Databases → MySQL      | `u123456789_admin`           |
| `DB_PASS`       | The MySQL password you set                | `DatabasePassword456!`       |

### Finding FTP Credentials on Hostinger:
1. Log in to [hPanel](https://hpanel.hostinger.com)
2. Click **Websites** → your domain → **Manage**
3. Go to **Files** → **FTP Accounts**
4. Copy the hostname, username, and password

### Finding MySQL Credentials on Hostinger:
1. In hPanel, go to **Databases** → **MySQL Databases**
2. Create a new database (or use existing)
3. Copy the host, database name, username, and password

---

## 🗄️ Step 2: Import the Database

This needs to be done **once** (initial setup only):

1. In Hostinger hPanel → **Databases** → **phpMyAdmin** → **Open**
2. Select your database from the left sidebar
3. Click **Import** tab
4. Upload `install/database.sql`
5. Click **Go**

> ⚠️ **IMPORTANT:** After import, change the admin password immediately!
> The seed data uses `admin123` — log in at `/admin` and update via phpMyAdmin.

---

## 📂 Step 3: Create Server Directories

These directories need to exist on the server with write permissions. Create them via Hostinger **File Manager**:

```
public_html/
├── uploads/
│   └── properties/
│       ├── original/
│       ├── medium/
│       └── thumb/
└── cache/
```

### Set permissions:
1. In File Manager, right-click each folder → **Permissions**
2. Set `uploads/` and `cache/` to **755** or **775**

---

## 🏃 Step 4: Deploy!

Just push your code:

```bash
git add -A
git commit -m "your changes"
git push origin main
```

GitHub Actions will automatically deploy. Check progress at:
**GitHub repo → Actions tab**

---

## 🔄 Redeployment

Every `git push` to `main` triggers a fresh deploy. You can also:
- Go to **Actions** tab → **Deploy to Hostinger** → **Run workflow** (manual trigger)

---

## 🛑 What Gets Excluded from Deploy

The workflow **does NOT upload** these to the server:

| Path | Reason |
|------|--------|
| `.git/`, `.github/` | Git internals |
| `scripts/` | Build scripts only |
| `install/` | SQL only needed once |
| `cache/` | Server generates its own |
| `uploads/` | User content — must not be overwritten! |
| `*.md` files | Documentation only |

---

## 🐛 Troubleshooting

### Deploy fails with "FTP connection error"
- Double-check `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD` secrets
- Make sure FTP account is active on Hostinger

### Site shows "500 Internal Server Error"
- Check if `.htaccess` is uploaded correctly
- Verify PHP version is 8.0+ in Hostinger → **Advanced** → **PHP Configuration**
- Check error logs: Hostinger → **Advanced** → **Error Logs**

### Database connection error
- Verify `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` secrets match Hostinger
- Make sure the database was created and `database.sql` was imported

### Images not uploading
- Verify `uploads/` directory exists with **755** permissions
- Check PHP `upload_max_filesize` is at least `10M` in PHP configuration

---

## 📋 Production Checklist

- [ ] GitHub Secrets are configured (all 7 values)
- [ ] Database imported via phpMyAdmin
- [ ] Admin password changed from `admin123`
- [ ] `uploads/` and `cache/` directories created on server
- [ ] Directory permissions set to 755
- [ ] SSL/HTTPS enabled on Hostinger (free SSL)
- [ ] PHP version set to 8.0+ on Hostinger
- [ ] Domain DNS pointing to Hostinger nameservers
- [ ] First deploy triggered via `git push`
- [ ] Test: Homepage loads ✓
- [ ] Test: Registration works ✓
- [ ] Test: Image upload works ✓
- [ ] Test: Admin panel accessible ✓
