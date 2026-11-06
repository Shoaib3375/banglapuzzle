# VPS Deployment Guide — BanglaPuzzle

This guide deploys the BanglaPuzzle Laravel 13 application on an Ubuntu 22.04 / 24.04 VPS
with Nginx, PHP 8.4-FPM, and MySQL 8.0.

> **Note:** This document is **documentation only**. A live deployment was **not** performed
> while writing this guide — no server was provisioned and no public URL is provided. The
> commands below are the steps you would follow to deploy to your own VPS. Adjust domain
> names, paths, and credentials to your environment.

---

## 1. Prerequisites

- Ubuntu 22.04 or 24.04 LTS
- Nginx 1.24 or newer
- PHP 8.4-FPM with extensions: `mbstring`, `xml`, `curl`, `zip`, `mysql`, `gd`, `bcmath`,
  `intl`, `pdo`, `tokenizer`, `fileinfo`, `openssl`, `ctype`, `json`, `pcre`, `phar`, `dom`,
  `filter`, `hash`, `session`, `simplexml`, `spl`, `xmlreader`, `xmlwriter`, `iconv`
- MySQL 8.0
- Composer 2.x
- Node.js 20+ and npm
- A non-root `sudo` user
- A domain name pointed at the server (for HTTPS)

---

## 2. Server preparation

```bash
# Update packages
sudo apt update && sudo apt upgrade -y

# Configure the firewall (allow SSH, HTTP, HTTPS)
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable
```

---

## 3. Install Nginx

```bash
sudo apt install -y nginx
sudo systemctl enable nginx
```

---

## 4. Install PHP 8.4-FPM

Add the `ppa:ondrej/php` PPA, which provides PHP 8.4 packages for Ubuntu.

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update

sudo apt install -y php8.4-fpm \
    php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-mysql \
    php8.4-gd php8.4-bcmath php8.4-intl php8.4-pdo php8.4-tokenizer \
    php8.4-fileinfo php8.4-opcache php8.4-readline
```

Verify the PHP-FPM version and socket:

```bash
php -v                  # should report 8.4.x
systemctl status php8.4-fpm
ls -l /run/php/php8.4-fpm.sock   # the fastcgi socket used by Nginx below
```

A couple of recommended PHP-FPM/php.ini production tweaks:

```bash
# /etc/php/8.4/fpm/php.ini  (production-friendly values)
cgi.fix_pathinfo=0
upload_max_filesize=8M
post_max_size=8M
memory_limit=256M
```

Restart PHP-FPM after any change:

```bash
sudo systemctl restart php8.4-fpm
```

---

## 5. Install Composer 2

Use the official installer procedure:

```bash
# Download the installer signature-checked, then install globally
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
composer --version
```

---

## 6. Install and secure MySQL 8.0

```bash
sudo apt install -y mysql-server
sudo systemctl enable mysql
sudo mysql_secure_installation
```

Create the database and a dedicated application user (replace placeholders with your own
strong values):

```bash
sudo mysql
```

```sql
CREATE DATABASE banglapuzzle CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'banglapuzzle'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON banglapuzzle.* TO 'banglapuzzle'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 7. Deploy the application

Assume the site will live at `/var/www/banglapuzzle`. Clone the repository and set ownership so
the web server can read and write the right directories.

```bash
sudo mkdir -p /var/www
sudo chown -R $USER:$USER /var/www
cd /var/www
git clone <repo-url> banglapuzzle
cd banglapuzzle
```

Install PHP dependencies **without** dev dependencies and with an optimized autoloader:

```bash
composer install --no-dev --optimize-autoloader
```

Environment configuration:

```bash
cp .env.example .env

# Edit .env for production:
#   APP_ENV=production
#   APP_DEBUG=false
#   APP_URL=https://your-domain.tld
#   DB_CONNECTION=mysql
#   DB_HOST=127.0.0.1
#   DB_PORT=3306
#   DB_DATABASE=banglapuzzle
#   DB_USERNAME=banglapuzzle
#   DB_PASSWORD=your_strong_password

php artisan key:generate
```

Run migrations (use `migrate --force` — **never** `migrate:fresh` on production, which would
drop your data):

```bash
php artisan migrate --force
```

Link the public storage disk so product images are web-accessible at `/storage/...`:

```bash
php artisan storage:link
```

Build the frontend assets (Tailwind CSS v4 + Vite):

```bash
npm install
npm run build
```

Set ownership and permissions so Nginx (running as `www-data`) can serve the app and write to
`storage` and `bootstrap/cache`:

```bash
sudo chown -R www-data:www-data /var/www/banglapuzzle
sudo chown -R $USER:www-data /var/www/banglapuzzle
sudo chmod -R 775 /var/www/banglapuzzle/storage
sudo chmod -R 775 /var/www/banglapuzzle/bootstrap/cache
```

---

## 8. Nginx virtual host

Create `/etc/nginx/sites-available/banglapuzzle`:

```nginx
server {
    listen 80;
    server_name your-domain.tld www.your-domain.tld;
    root /var/www/banglapuzzle/public;
    index index.php index.html;

    # Logs
    access_log /var/log/nginx/banglapuzzle-access.log;
    error_log  /var/log/nginx/banglapuzzle-error.log;

    # Front-controller pattern: serve the file if it exists, otherwise hand off to index.php
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Never expose hidden files or sensitive paths
    location ~ /\.(?!wellknown).* {
        deny all;
    }

    # Block access to sensitive source folders
    location ~ ^/(app|bootstrap|config|database|resources|routes|storage|tests)/ {
        deny all;
    }

    # Pass PHP files to PHP 8.4-FPM via its socket
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Static asset caching
    location ~* \.(css|js|jpg|jpeg|png|webp|gif|ico|svg|woff2?)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    client_max_body_size 8M;
}
```

> The `fastcgi_pass` target is the actual PHP 8.4-FPM socket path — confirm it exists with
> `ls -l /run/php/php8.4-fpm.sock`. On some systems the socket may be at a slightly different
> path; adjust to match your installation.

Enable the site and reload Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/banglapuzzle /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx php8.4-fpm
```

---

## 9. DNS and HTTPS with Let's Encrypt

Point your domain's A/AAAA records at the server IP, then issue a certificate with certbot:

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.tld -d www.your-domain.tld
```

Certbot automatically edits the Nginx server block to enable HTTPS and sets up automatic
renewal via `systemctl`. Test renewal with:

```bash
sudo certbot renew --dry-run
```

---

## 10. Production optimization

Cache the config, routes, and views for faster bootstrapping (re-run after any deploy/config
change):

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> When these caches are active, remember that `.env` changes require re-running
> `config:cache`, and any new routes/views require re-running the corresponding cache command.

---

## 11. Backup and restore

### Backup

Back up the database (mysqldump) and the uploaded product images in `storage/app/public`:

```bash
# Database
mysqldump -u banglapuzzle -p banglapuzzle > banglapuzzle_$(date +%F).sql

# Files (images, logs)
tar -czf banglapuzzle_storage_$(date +%F).tar.gz /var/www/banglapuzzle/storage/app/public
```

Schedule regular backups via cron as needed.

### Restore

```bash
# Database
mysql -u banglapuzzle -p banglapuzzle < banglapuzzle_YYYY-MM-DD.sql

# Files
sudo tar -xzf banglapuzzle_storage_YYYY-MM-DD.tar.gz -C /
sudo chown -R www-data:www-data /var/www/banglapuzzle/storage
```

---

## 12. Log inspection

- **Application log:** `/var/www/banglapuzzle/storage/logs/laravel.log`
- **Nginx access log:** `/var/log/nginx/banglapuzzle-access.log`
- **Nginx error log:** `/var/log/nginx/banglapuzzle-error.log`
- **PHP-FPM log:** `/var/log/php8.4-fpm.log`

Tail logs in real time:

```bash
tail -f /var/www/banglapuzzle/storage/logs/laravel.log
sudo journalctl -u php8.4-fpm -f
sudo journalctl -u nginx -f
```

---

## 13. Troubleshooting

| Symptom | Check |
| ------- | ----- |
| 500 / blank page | `storage/logs/laravel.log`; ensure `APP_DEBUG=false` but logs are still written. |
| `500 no application encryption key` | Run `php artisan key:generate`; confirm `APP_KEY` in `.env`. |
| 404 for all routes | `root` must point to `/var/www/banglapuzzle/public`; confirm `try_files` line; `sudo nginx -t`. |
| `502 Bad Gateway` | PHP-FPM not running or wrong socket path; `systemctl status php8.4-fpm` and verify `fastcgi_pass unix:/run/php/php8.4-fpm.sock`. |
| Images not loading | `php artisan storage:link` run; `storage/app/public` exists and is readable; `/storage/` maps to the public disk. |
| Permission denied writing logs/cache | `chown -R www-data:www-data storage bootstrap/cache` and `chmod -R 775`. |
| `migrate` fails | DB credentials in `.env` match the MySQL user/database; the user can access `banglapuzzle.*` from `localhost`. |
| Stale config after `.env` edit | Re-run `php artisan config:cache`. |
| Assets 404 | `npm run build` completed and `public/build/manifest.json` exists. |

---

## Quick checklist

1. Update server + firewall
2. Install Nginx, PHP 8.4-FPM (+ extensions), Composer, MySQL
3. Create database + user
4. `git clone` → `composer install --no-dev --optimize-autoloader`
5. `cp .env.example .env` → set `APP_ENV=production`, `APP_DEBUG=false`, DB creds → `php artisan key:generate`
6. `php artisan migrate --force` → `php artisan storage:link`
7. `npm install` → `npm run build`
8. `chown`/`chmod` storage and bootstrap/cache
9. Nginx vhost → `systemctl restart nginx php8.4-fpm`
10. certbot for HTTPS
11. `config:cache` + `route:cache` + `view:cache`
