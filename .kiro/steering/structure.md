# Project Structure

```
app/
  Enums/            Role, ArticleStatus, PostType
  Http/Controllers/ public pages, Auth/, MyArticleController (member area), Admin/
  Http/Requests/    ArticleRequest, PostRequest
  Models/           User, Category, Article, Post, ContactMessage, Volunteer
  Notifications/    queued mails (review, contact, volunteer)
  Policies/         ArticlePolicy
  Support/          Markdown, Slug
config/itgurus.php  site settings (contact email, social links)
database/           migrations, factories, CategorySeeder, DemoSeeder
resources/views/    Blade; components/layouts/{app,auth,admin}
routes/web.php      all routes; routes/console.php: app:create-admin + scheduler
scripts/            deploy.sh, post-deploy.sh, backup.sh
docs/               HOSTING.md (runbook), ANALYSIS.md (history)
tests/Feature/      feature tests
```
