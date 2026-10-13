# Golden Spike Backend

Laravel 13 backend and Filament 4 admin for **السنبلة الذهبية**, with Arabic and English content, Astrotomic translation tables, and Spatie Media Library. The public API is read-only. This folder does not contain the Next.js website.

## Local setup

Requires PHP 8.3 or newer, Composer, MySQL 8, and PHP extensions including PDO MySQL, GD, EXIF, fileinfo, mbstring, intl, and zip. PHPUnit uses isolated in-memory SQLite, so its PHP CLI also needs PDO SQLite. Filament ships compiled assets; this backend does not require an npm build or Docker.

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Create an empty MySQL database named `elsonbola`, then configure `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`. Use a database dedicated to this application.

```bash
php artisan migrate --seed
php artisan storage:link
php artisan app:create-admin admin@example.com --name="Administrator"
php artisan serve
```

The administrator command securely prompts for a password of at least 12 characters. No default account or password is seeded. Open `http://localhost:8000/admin`. In a separate terminal, run media conversions and optional frontend revalidation:

```bash
php artisan queue:work --tries=3
```

The default seeder creates a published Egyptian rice product with active 1, 5, 10, and 25 kg packs, plus published Home, About, and Contact pages. All product and page fields, including SEO, contain Arabic and English content. Business settings include the source-confirmed location, email, primary telephone, and the supplied logo. Unconfirmed opening hours, coordinates, WhatsApp numbers, certifications, and company history remain unset.

Run `php artisan db:seed` after migrations to install this initial content. Seeders fill empty translated fields, preserve nonempty admin edits, publication choices, pack activation/order, uploaded media, and soft-deleted records. Only untouched title-only pages from the original seeder are completed and published automatically. New records are published after translations and pack sizes satisfy the normal publication rules. No administrator account is created.

Seed media is bundled under `database/seeders/assets`; Spatie imports it into the public disk and queues conversions. Run `php artisan storage:link` and a queue worker. Arabic promotional pack artwork is restricted to Arabic responses; the illustrative rice cover is language-neutral. Asset provenance is documented in `database/seeders/assets/README.md`.

To view database content in Next.js, set `WEBSITE_DEMO_MODE=false` in the frontend environment and configure `API_BASE_URL` (`http://127.0.0.1:8000/api/v1` for local development) for the backend. If frontend revalidation is configured, seed changes trigger the existing cache invalidation workflow; otherwise Next.js content refreshes after its cache interval.

## Admin workflow

1. Create a draft product with a stable slug and Arabic and English text.
2. Add pack sizes from **Catalog → Pack sizes**. A pack belongs to one product and cannot be moved. Weights must be positive and unique within that product, including deleted packs.
3. Upload approved images. The upload supplies initial alternatives from the content title; refine alternatives and artwork language in the related **Media** table. File deletion requires an administrator and explicit confirmation.
4. Publish from the product edit page. Both language versions and at least one active pack are required. The last active pack cannot be removed until the product is unpublished.
5. Edit the fixed pages and publish when translated content is complete. Page bodies and product descriptions are plain text.
6. Set business details and international phone numbers. The first number becomes primary; choose another primary before removing the current primary.

Editors can manage products, packs, pages, contacts, and content media. Administrators additionally manage business settings and accounts, view audit history, and delete or restore recoverable records. Permanent product/pack/contact deletion is a separate confirmed action on deleted records. Fixed pages and administrator accounts cannot be permanently deleted in the panel. Keep at least one active administrator.

Open the profile dropdown and select English or العربية to switch the panel between Arabic RTL and English LTR. Dynamic content has separate language tabs. Labels live in `lang/ar.json` and `lang/en.json`.

## API

The complete [OpenAPI specification](docs/openapi.json) can be imported into Swagger Editor or Postman.

| Endpoint | Purpose |
| --- | --- |
| `GET /api/v1/products` | Published products with active pack sizes. |
| `GET /api/v1/products/details?id=2` | One published product by positive integer ID. |
| `GET /api/v1/products/{slug}` | Existing slug lookup, retained for API compatibility. |
| `GET /api/v1/pages/{key}` | Published `home`, `about`, or `contact` content. |
| `GET /api/v1/settings` | Public business details and contact numbers. |

Use `locale=ar` or `locale=en`; Arabic is the default. Unsupported locales return 422. Product lists accept `page`, `per_page` (1–50), `search`, `featured=0|1`, and `sort=sort_order|name|-name|newest`. Responses contain `data`, `message`, `errors`, and `meta`; pagination adds `current_page`, `per_page`, `total`, and `last_page`. Missing, draft, or deleted resources return 404 with the same envelope. Writes are available only through authenticated Filament sessions, not public API endpoints.

```bash
curl -H 'Accept: application/json' 'http://localhost:8000/api/v1/products?locale=en&featured=1'
```

Media responses provide original, thumbnail, and web URLs with localized alternatives. Pending conversions fall back to the original image. Language-specific artwork is excluded from the other locale. Frontend text should render as plain text, with preserved paragraph spacing, rather than using raw HTML.

## Architecture

Domain code lives under `app/Domain`: Catalog, Content, Settings, Administration, Audit, and Shared. Each domain groups its models, actions/services, requests, resources, policies, and observers where applicable. Filament resources keep forms and tables in separate classes. Domain actions perform business operations; observers protect model invariants, audit changes, and invalidate public content caches. Do not use bulk query updates for domain mutations because they bypass these observers.

Public resources eagerly load translations, variants, and media. Versioned cache keys prevent stale responses after content changes. Changes invalidate only after database commit. The Laravel cache expires old versions after `CONTENT_CACHE_TTL` seconds. Optional queued revalidation invalidates Next.js content separately. Soft deletion preserves translations and media for recovery. Explicit force deletion removes dependent variant records and their media; file removal requires backups for recovery.

The framework versions are resolved in `composer.lock`. Filament 4.14 is compatible with this application and the installed media plugin; the selected cached releases have no advisories reported by the available Composer metadata. Refresh the dependency audit with normal network access before deployment.

## Configuration

| Variable | Purpose |
| --- | --- |
| `APP_ENV`, `APP_DEBUG`, `APP_URL` | Environment identity, errors, and absolute media URLs. |
| `APP_LOCALE` | Default admin locale; public content defaults to Arabic independently. |
| `DB_*` | MySQL connection; never share a development database with tests. |
| `FRONTEND_URL` | Exact allowed browser origin for public APIs. |
| `CONTENT_CACHE_TTL` | Cached public-response lifetime; default 600 seconds. |
| `CACHE_STORE`, `SESSION_DRIVER`, `QUEUE_CONNECTION` | Default database-backed infrastructure. |
| `MEDIA_DISK` | Media disk; use `public` with the storage link for local development. |
| `NEXT_REVALIDATION_URL` | Optional trusted Next.js revalidation endpoint. |
| `NEXT_REVALIDATION_TOKEN` | Bearer token used by the queued revalidation request. |

The revalidation endpoint should verify its bearer token and invalidate the `website-content` cache tag. The Next.js implementation is outside this backend task. Use `.env.example`, `.env.staging.example`, and `.env.production.example` as templates for separate environments. Replace example URLs and credentials, and store real values in uncommitted environment files or deployment secret stores.

## Verification and deployment

```bash
composer validate --strict
composer format:check
composer lint
composer test
composer build
```

Tests use factories, isolated databases, fake queues/storage, and mocked HTTP. They cover admin rendering in both languages, Livewire forms and actions, publication rules, permissions, cache invalidation, media metadata, contacts, translations, revalidation, and audit redaction. The CI workflow runs formatting, PHP syntax lint, the suite against MySQL, and Laravel/Filament production-cache builds. PHPStan is not installed; standalone static type analysis remains a tooling follow-up. The Next.js website is documented in [the frontend README](../frontend/README.md). Its separate CI workflow runs formatting, linting, type checks, unit/integration tests, the production build, and isolated browser tests.

The default PHP binary in the current workspace lacks PDO SQLite. An installed alternative can run the tests:

```bash
/Applications/MAMP/bin/php/php8.3.14/bin/php vendor/bin/phpunit
```

For production, configure MySQL, public storage, HTTPS, `APP_DEBUG=false`, queue workers, and process supervision. Install dependencies with `composer install --no-dev --optimize-autoloader`, run incremental migrations, publish Filament assets with `php artisan filament:upgrade`, and then run `composer build`. Rebuild caches after configuration or deployment changes. Static media must be served from `public/storage` or the configured media host.

## Package references

- [Filament installation](https://filamentphp.com/docs/4.x/introduction/installation)
- [Astrotomic translation tables](https://docs.astrotomic.info/laravel-translatable/installation)
- [Spatie media migration](https://github.com/spatie/laravel-medialibrary/blob/main/database/migrations/create_media_table.php.stub)

## Frontend contract fixtures

`php scripts/export-frontend-fixtures.php` exports 16 bilingual API responses to `frontend/src/features/website/tests/fixtures/laravel-content.json`. It uses the real HTTP kernel, API resources, migrations, and seeders with a disposable in-memory SQLite database and fake storage/queues. It never migrates or seeds the configured development database. PDO SQLite is required; the MAMP PHP binary documented above works locally. Clear cached application configuration before exporting. The frontend CI workflow regenerates these responses and tests their rendering through the centralized API client.

The seeders now store page bodies and product descriptions as plain text, matching Filament textareas and the OpenAPI contract. Reruns preserve existing nonempty content; the frontend safely displays legacy seeded HTML as text.
