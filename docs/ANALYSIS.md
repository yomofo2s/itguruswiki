# Why the site was rebuilt (Sep 2026)

## What happened
- `www.itgurusgermany.com` (WordPress) went down: its database was deleted, and the error page exposed the
  DB user, server IP and file path.
- The MediaWiki at `/w` also stopped working. There were **no backups**. Issue #19 "DR: DB Backup" had been
  open since July 2024.

## What was wrong with the old setup
1. **No backups** of either database.
2. **Secrets in public git history:** earlier commits of `LocalSettings.php` contain `$wgSecretKey` and `$wgUpgradeKey`.
   They're useless now that MediaWiki is gone, but the history is public. Optionally purge it with `git filter-repo`.
3. **End-of-life software:** MediaWiki 1.41 (no security fixes since Dec 2024).
4. **Config written for Docker, but production was shared hosting** (`$wgDBserver = "database"`, env vars that Apache never set).
5. Plain HTTP on a raw IP, anonymous editing without a captcha, and manual edits on the server that never made it back to git.

## Decision
Replace WordPress + MediaWiki with **one Laravel 13 application** backed by **MariaDB**:
- A wiki where anyone edits without review didn't fit "verified information". The new site has an editorial review workflow.
- One codebase in git is the single source of truth, with CI, tests and scripted deploys.
- It runs on the existing Hetzner Webhosting (PHP + MariaDB), with no new server to maintain.
- Scripted nightly backups with an off-site copy.

## Open issues addressed
| Issue | Status |
|---|---|
| #9 Content management | Admin area and editorial workflow |
| #13 Enable image upload | Cover images for guides and posts |
| #15 Social media presence | Social links (configurable in `.env`) and community page |
| #16 Test environment | `php artisan serve` + SQLite, or `compose.yaml` with MariaDB |
| #17 Make content more editable | Markdown editor with live preview |
| #19 DR: DB backup | `scripts/backup.sh` + cron + off-site copy |
| #20 Pipeline setup | `.github/workflows/ci.yml` (tests, MariaDB migrations, secret scan, deploy) |
