# Tech Stack

- **Laravel 13**, PHP 8.3+, Blade views, Tailwind CSS 4 via Vite, self-hosted Inter font
- **MariaDB** in production (Hetzner Webhosting); **SQLite** locally and in tests
- Queue: `database` driver, processed by `schedule:run` from cron (no workers on shared hosting)

## Conventions
- Roles live in `App\Enums\Role` (admin, editor, member); authorisation in `ArticlePolicy` and the
  `access-admin` / `manage-users` gates (`AppServiceProvider`).
- User content is Markdown, rendered only via `App\Support\Markdown::toHtml()` (HTML escaped).
- Slugs via `App\Support\Slug::unique()`. Route model binding uses slugs for articles, posts and categories.
- Tests use `Tests\RefreshDatabase` (wraps Laravel's trait). Add a feature test for every new route.
- Never commit `.env`. Production config is documented in `docs/HOSTING.md`.

## Commands
```bash
composer install && npm install
php artisan migrate --seed && php artisan db:seed --class=DemoSeeder
npm run dev & php artisan serve
php artisan test
SSH_TARGET=user@host ./scripts/deploy.sh
```
