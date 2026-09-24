# Hosting on Hetzner Webhosting

Target: the Laravel app on **Hetzner Webhosting** (konsoleH, Apache + PHP), with a **new
MariaDB database**, served at `https://www.itgurusgermany.com`. It replaces both the old
WordPress site (`intern/`) and the MediaWiki at `/w`.

> **Plan:** Webhosting **L or XL** is strongly recommended. Those include SSH, which makes
> deploying and running migrations simple. On S/M everything still works, but you upload with SFTP
> and run the post-deploy script once through the cron manager (see §6b).

## Target layout

```
/usr/www/users/itguru/            <- account home
├── itguruswiki/                    <- the Laravel app (this repo, built)
│   ├── .env                      <- production secrets (chmod 600, never in git)
│   ├── public/                   <- DOCUMENT ROOT for www.itgurusgermany.com
│   └── storage/                  <- logs, cache, uploaded images (writable)
├── backups/                      <- nightly dumps + .my.cnf (chmod 700)
└── intern/                       <- old WordPress - remove after go-live
```

Only `public/` is reachable from the web. `.env`, the code and `storage/` sit outside the document root.

---

## 1. konsoleH: PHP, domain, SSL, mail

1. **Webserver → PHP configuration:** PHP **8.3** or newer (Laravel 13 requires 8.3+).
   - Extensions: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `curl`, `dom`, `gd` (default on Hetzner). Enable **OPcache**.
   - `memory_limit` ≥ 256M, `upload_max_filesize` and `post_max_size` = 10M.
2. **Domains:** set the document root of `www.itgurusgermany.com` **and** `itgurusgermany.com` to
   `/itguruswiki/public`. `.htaccess` redirects the bare domain to `www` and old `/w/…` wiki links to the new site.
3. **SSL → Let's Encrypt** for both names, with auto-renew on.
4. **Email:** check that the mailbox `info@itgurusgermany.com` exists. It's used as the sender and for SMTP.

## 2. Create the new database

konsoleH → **Databases → MySQL/MariaDB → new database**. Write down the host (e.g. `sqlXXX.your-server.de`),
database name, user and password. Use a new, generated password. Don't reuse the deleted `wp_itgur_2` / `itguru_1`.

## 3. Production `.env`

Create `/usr/www/users/itguru/itguruswiki/.env` (chmod 600):

```dotenv
APP_NAME="IT GURUs Germany"
APP_ENV=production
APP_KEY=                      # generate locally: php artisan key:generate --show
APP_DEBUG=false
APP_URL=https://www.itgurusgermany.com
APP_TIMEZONE=Europe/Berlin

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=warning

DB_CONNECTION=mariadb
DB_HOST=sqlXXX.your-server.de
DB_PORT=3306
DB_DATABASE=<database name>
DB_USERNAME=<database user>
DB_PASSWORD=<database password>

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=mail.your-server.de
MAIL_PORT=465
MAIL_USERNAME=info@itgurusgermany.com
MAIL_PASSWORD=<mailbox password>
MAIL_FROM_ADDRESS="info@itgurusgermany.com"
MAIL_FROM_NAME="${APP_NAME}"

ITG_CONTACT_EMAIL=info@itgurusgermany.com
ITG_NOTIFY_EMAIL=info@itgurusgermany.com
ITG_SOCIAL_WHATSAPP=
ITG_SOCIAL_LINKEDIN=
```

## 4. Build and upload (first deploy)

Hetzner Webhosting has no Node.js and often no Composer, so **build on your machine or in CI** and upload the result.

**With SSH (L/XL):** from your laptop, in the repo:
```bash
SSH_TARGET=itguru@www.itgurusgermany.com ./scripts/deploy.sh
```
It runs `composer install --no-dev`, `npm ci && npm run build`, uploads with rsync (never touching
`.env` or uploaded files), then `scripts/post-deploy.sh` on the server. That script runs migrations,
seeds the topics, links storage and caches config and routes.

If the server's default `php` is older, set `REMOTE_PHP` to the path of the PHP 8.3 binary shown in konsoleH, e.g.
`REMOTE_PHP=/usr/bin/php83`.

**Without SSH (S/M):**
1. Get a built release, either:
   - **from GitHub (no local PHP needed):** push to `main`, open *Actions → ci → latest run → Artifacts*, and download `itgurus-release`. Unzip it. It already contains `vendor/` and `public/build/`.
   - **or locally:** `composer install --no-dev -o && npm ci && npm run build`
2. Upload the whole folder with SFTP (FileZilla) to `/itguruswiki/`, **except** `node_modules/`, `.git/`, `tests/`, `.env`.
3. Upload the `.env` from step 3.
4. Continue with §6b.

## 5. First admin account

SSH: `cd ~/itguruswiki && php artisan app:create-admin you@example.com --name="Your Name"`.
Without SSH: register on the site, then promote yourself once through a one-off cron command:
`/usr/bin/php /usr/www/users/itguru/itguruswiki/artisan tinker --execute="App\Models\User::where('email','you@example.com')->update(['role'=>'admin','email_verified_at'=>now()]);"`

## 6. Cron jobs (konsoleH → Services → Cronjob manager)

| Schedule | Command | Why |
|---|---|---|
| every minute (or the shortest interval allowed) | `/usr/bin/php /usr/www/users/itguru/itguruswiki/artisan schedule:run` | sends queued mail (contact notices, review emails), cleanup |
| `30 3 * * *` | `DB_NAME=<db> /bin/sh /usr/www/users/itguru/itguruswiki/scripts/backup.sh >> /usr/www/users/itguru/backups/backup.log 2>&1` | nightly backup |

Use the PHP 8.3 binary path konsoleH shows.

### 6b. No SSH: run post-deploy through cron
After each upload, add a one-time cron entry
`/bin/sh /usr/www/users/itguru/itguruswiki/scripts/post-deploy.sh >> /usr/www/users/itguru/itguruswiki/storage/logs/deploy.log 2>&1`,
wait for it to run, check `deploy.log` shows `post-deploy ok`, then delete the entry.

### 6c. Plan without cron and without SSH
This is the current setup for itgurusgermany.com.
- **Mail:** set `QUEUE_CONNECTION=sync` in `.env`. Emails then go out during the request, so no scheduler is needed.
- **Setup and updates (migrations):** put `ITG_SETUP_TOKEN=<random, 32+ characters>` in `.env` and open
  `https://www.itgurusgermany.com/_setup/<token>`. It runs the migrations, seeds the topics and links storage, and shows the output.
  Add `?admin=you@example.com` to also make that registered account an administrator.
  **Remove the line from `.env` afterwards**; the URL then returns 404. Repeat after each upload that includes new migrations.
- **Backups:** see §7. Without cron, use Hetzner's nightly backups plus a manual export.

### 6d. Automatic deploys from GitHub (no SSH needed)
Every push to `main` runs the tests, builds the site (including `vendor/` and `public/build/`), uploads it by **SFTP**,
opens the setup link to run migrations, and checks that the site responds. `.env`, uploaded images, logs and sessions on
the server are never touched.

One-time setup in GitHub → repository **Settings**:
1. **Secrets and variables → Actions → Secrets:**
   - `SFTP_HOST`: the FTP server shown in konsoleH (*Access details → FTP*), e.g. `www123.your-server.de`
   - `SFTP_USER`: the FTP user (the login you use in FileZilla)
   - `SFTP_PASSWORD`: its password
   - `ITG_SETUP_TOKEN`: the same value as `ITG_SETUP_TOKEN` in the server's `.env`. Keep that line in `.env`, because deploys use it.
2. **Variables:** `DEPLOY_ENABLED` = `true`. Optionally set `SFTP_REMOTE_DIR` (default `itguruswiki`, relative to the FTP login folder)
   and `SITE_URL` (default `https://www.itgurusgermany.com`).
3. **Environments:** create `production`. Optionally add yourself as a required reviewer, so every deploy waits for your approval.

Watch deploys under **Actions**. The `?admin=` option of the setup link is disabled once an administrator exists,
so a leaked token can't create admin accounts. Change the token (server `.env` and GitHub secret) if you suspect a leak.

## 7. Backups — don't lose the database again

**Without cron:** Hetzner backs up the account nightly and keeps the backups for 14 days (konsoleH → *Backup*).
Also, **once a month** (and before every update) export the database: konsoleH → *Databases* → *phpMyAdmin* → select the
database → *Export* → *Quick*, SQL. Store the file outside Hetzner, for example in a password-protected cloud folder.

**With cron:**


- `scripts/backup.sh` writes `db-*.sql.gz` and `uploads-*.tar.gz` to `~/backups` and keeps 30 days.
  Put the DB credentials in `~/backups/.my.cnf` (see the script header, chmod 600).
- **Keep a copy outside the webhosting account.** Either set `OFFSITE=` to a Hetzner Storage Box (SSH plans), or download
  `~/backups` via SFTP every week. Hetzner's own 14-day snapshots are only a second line of defence.
- **Test a restore** every few months:
  `gzip -cd db-XXXX.sql.gz | mysql -h 127.0.0.1 -u itgurus -psecret itgurus` (local `docker compose` MariaDB).

## 8. Go-live checklist

- [ ] `https://www.itgurusgermany.com` loads. `http://` and `itgurusgermany.com` redirect to `https://www…`
- [ ] `https://www.itgurusgermany.com/up` returns 200
- [ ] `https://www.itgurusgermany.com/.env` returns 404 (the document root is `public/`)
- [ ] Register → the verification email arrives → you can write a guide → an editor gets the review email
- [ ] The contact form sends an email to `ITG_NOTIFY_EMAIL`
- [ ] Uploading a cover image works and the image shows (checks `storage:link`)
- [ ] `backup.log` shows `backup ok`, and a restore was tested
- [ ] The **Privacy** page is reviewed and an **Impressum** is added (required in Germany)
- [ ] The old WordPress (`intern/`) and its database user are removed
- [ ] An uptime monitor watches `/up`

## 9. Routine operations

| Task | How |
|---|---|
| Deploy a change | PR → CI green → merge → `./scripts/deploy.sh`, or the **ci** workflow with *deploy* ticked (needs secrets `HETZNER_SSH_KEY`, `HETZNER_KNOWN_HOSTS`, `HETZNER_SSH_TARGET`) |
| Maintenance mode | `php artisan down` / `php artisan up` |
| Logs | `storage/logs/laravel-*.log` |
| Update dependencies | `composer update && npm update` locally → tests → PR (Dependabot can automate this) |
| Framework upgrades | Laravel releases yearly with ~2 years of security fixes. Plan an upgrade PR every year |
