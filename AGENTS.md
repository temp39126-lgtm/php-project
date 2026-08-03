# AGENTS.md

## Cursor Cloud specific instructions

### What this is
Grit Fit Nutri is a single PHP application (no framework, no package manager) backed by
MySQL/MariaDB. It serves a public storefront (home, about, contact, products, per-category
and per-product pages) and an admin panel at `/admin` for managing categories, products,
reviews, and viewing contact enquiries. Routing is a front controller: Apache's `.htaccess`
rewrites every non-file request to `index.php?route=...`.

### Runtime already provisioned
PHP 8.3 CLI (with `pdo_mysql`, `gd`, `mbstring`, `curl`, `fileinfo`) and MariaDB 10.11 are
installed in the VM snapshot. There are **no** application-level dependencies to install
(no `composer.json` / `package.json`), so the startup update script is effectively a no-op.

### Starting the services (do this each session)
MariaDB does not auto-start; start it, then run the built-in PHP dev server:

```
sudo service mariadb start
php -S 0.0.0.0:8000 -t /workspace /workspace/dev/router.php
```

- `dev/router.php` reproduces the `.htaccess` front-controller rewrite for the built-in
  server (the built-in server ignores `.htaccess`). Do not run `php -S` without it or every
  route except real static files will 404.
- Admin panel: `http://localhost:8000/admin/` — default login `admin` / `admin123`.

### Database
- App DB credentials live in `config.php` (`u582313683_gritfitnutri` DB + user). The local
  DB, user, and grants matching those values are baked into the snapshot.
- There was no committed schema; it is reconstructed in `db/init.sql` (schema + idempotent
  seed: default admin, one sample category, one sample review). Re-apply or reset with:
  ```
  sudo mysql u582313683_gritfitnutri < db/init.sql
  ```
  If the DB itself is missing (e.g. fresh volume), first:
  ```
  sudo mysql -e "CREATE DATABASE IF NOT EXISTS u582313683_gritfitnutri CHARACTER SET utf8mb4;
  CREATE USER IF NOT EXISTS 'u582313683_gritfitnutri'@'localhost' IDENTIFIED BY 'KiratveerGF!@#123';
  GRANT ALL ON u582313683_gritfitnutri.* TO 'u582313683_gritfitnutri'@'localhost'; FLUSH PRIVILEGES;"
  ```

### Non-obvious caveats
- `config.php` hardcodes `SITE_URL = https://gritfitnutri.com`. All storefront nav links and
  the public CSS/JS/logo (`<link>`/`<script>`/`<img>`) are emitted as absolute production
  URLs, so when browsing locally those links point at production and storefront styling loads
  from the live site. To exercise a specific local page, navigate to its `http://localhost:8000/...`
  URL directly rather than clicking nav links. All forms POST to the current URL (no `action`),
  so admin login, category/product create/edit, and the contact form all work locally. The
  admin panel is fully self-styled (inline `<style>`), so it renders correctly offline.
- Uploaded images are written to `uploads/` and served as static files.

### Lint / test / build
There is no build step, no linter config, and no automated test suite in this repo. Use
`php -l <file>` for syntax linting. "Testing" means manually exercising the storefront and
admin panel against the running dev server.
