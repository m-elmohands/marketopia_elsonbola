# Golden Spike website

A bilingual Next.js business website for **السنبلة الذهبية**, with an ivory, olive, and gold editorial design. It includes Home, About, product search, product details with keyboard-accessible pack selection and gallery, and direct contact links. Arabic uses RTL and English uses LTR. The logo is the original JPEG supplied by the owner, imported locally.

## Local development

Use locally installed Node.js 22.20+ or 24 and npm. Laravel/MySQL setup remains in [the backend README](../backend/README.md).

```sh
cd frontend
npm ci
cp .env.example .env.local
npm run dev
```

Visit `http://localhost:3000/ar` or `/en`. `/` redirects to Arabic. The example configuration uses published Laravel content by default. Start Laravel at `http://127.0.0.1:8000`, migrate and seed its database, create the storage link, and run the queue worker. Set `WEBSITE_DEMO_MODE=true` only for an explicit design preview; preview pages use `noindex` and an empty sitemap.

To use Laravel content, set `WEBSITE_DEMO_MODE=false`, run the Laravel server and queue worker, and publish translated products/pages/settings in Filament. No sample products or contacts are substituted for live API failures. Missing settings provide only a brand shell, missing pages provide translated introductory UI copy, and a missing product renders the 404 page. Next.js streaming can return HTTP 200 with `noindex` for that page; the Laravel API still returns 404. Operational API failures show a recovery page.

## Original logo

`public/images/facebook-logo.jpg` is an unchanged copy of the owner-supplied `../logo.jpg`. The header, footer, and favicon use the uploaded Laravel `logo` media when available, and fall back to its local path from `src/config/brand.json`. The previous poster-extracted logo was removed. No live Facebook request is needed. The centralized config retains the owner-supplied URL as source provenance.

To import the original JPEG locally and update the shared fallback:

```sh
npm run logo:import
```

If the CDN is unavailable, save the supplied JPEG and run:

```sh
npm run logo:import -- --file /absolute/path/logo.jpg
```

The importer validates the JPEG, saves it as `public/images/facebook-logo.jpg`, and changes the centralized logo path. On failure it keeps the current configuration. The original image is copied without editing.

## Environment variables

| Variable             | Purpose                                                                                                                                                                     |
| -------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `API_BASE_URL`       | Server-only Laravel public API root, including `/api/v1`. Defaults to `http://127.0.0.1:8000/api/v1`.                                                                       |
| `SITE_URL`           | Canonical public origin used for metadata, language alternatives, sitemap, and structured data.                                                                             |
| `WEBSITE_DEMO_MODE`  | Sample design preview only when exactly `true`; defaults to live content. Set `false` for launch.                                                                           |
| `MEDIA_BASE_URL`     | Exact Laravel storage origin, including port; defaults to `http://localhost:8000`, matching Laravel `APP_URL`. Only `/storage/**` images from this origin can be optimized. |
| `REVALIDATION_TOKEN` | Server-only secret shared with Laravel's revalidation job. No endpoint access when unset.                                                                                   |
| `PLAYWRIGHT_CHROME`  | Set `true` to use an already installed Google Chrome for browser tests. CI uses Playwright Chromium.                                                                        |

Development, staging, and production have separate `.env.*.example` templates. Actual environment files are ignored. During development, images bypass the remote optimizer so localhost Laravel media works directly. Deploy public media on the configured HTTPS origin for production image optimization; use development mode for localhost media because the production optimizer expects a publicly reachable media host.

## Backend integration

Server Components read the API directly through `src/lib/api/client.ts`. Requests have a five-second timeout, one retry for temporary read failures, and a five-minute cache tagged `website-content`. React request caching deduplicates shared settings/page/product reads. Frontend UI labels live in `src/i18n/{ar,en}.json`; editable content remains in Laravel translation tables.

| Laravel endpoint                                       | Website use                                                          |
| ------------------------------------------------------ | -------------------------------------------------------------------- |
| `GET /api/v1/settings?locale=ar\|en`                   | Branding, contacts, address, media, and approved quality highlights. |
| `GET /api/v1/pages/{home,about,contact}?locale=ar\|en` | Localized page headings, intro/body, media, and SEO overrides.       |
| `GET /api/v1/products?locale=ar\|en`                   | Search, featured content, sorting, pagination, and pack information. |
| `GET /api/v1/products/details?id=2&locale=ar\|en`      | Product detail, pack selection, and gallery.                         |

Set Laravel `NEXT_REVALIDATION_URL` to `http://localhost:3000/api/revalidate` locally, and `NEXT_REVALIDATION_TOKEN` to the same secret as `REVALIDATION_TOKEN`. Run the Laravel queue worker. Production uses the HTTPS frontend URL.

`POST /api/revalidate` requires `Authorization: Bearer <token>` and JSON `{"tags":["website-content"]}`. It returns 200 on successful invalidation, 401 without matching authorization, 400 for malformed JSON, or 422 for unsupported tags. It immediately expires the shared cache; new page requests fetch the published content. The bearer token is compared in constant time. This matches the existing Laravel queued job.

See [Laravel OpenAPI](../backend/docs/openapi.json) for the public content contract and [Next.js revalidation documentation](https://nextjs.org/docs/app/api-reference/functions/revalidateTag) for the two-argument tag API used here.

## Architecture and design

- `src/app`: localized routes, metadata, loading/error/404 pages, sitemap/robots, and revalidation endpoint.
- `src/features`: domain components, services, types, and tests. Pages use Server Components; client code handles navigation, gallery, and pack selection.
- `src/components`: shared brand, header, footer, section headings, photography, contact CTA, and empty states.
- `src/lib`: API client, contact links, preview fixtures, SEO, and revalidation authorization.
- `src/i18n`: typed dictionaries and route/number helpers. Language switching preserves the page, query, and selected pack.

Tailwind CSS 4 supplies the CSS pipeline alongside readable, centralized design tokens and component styles. System fonts avoid external font requests; English uses editorial serif headings and Arabic uses local Arabic-capable fallbacks. Layout uses logical properties, native mobile disclosure navigation, semantic headings and forms, radio controls, skip links, focus outlines, meaningful image alternatives, and reduced-motion support. Next Image reserves dimensions, prioritizes the hero, and lazy loads lower-page content. Below-viewport sections and product cards fade upward once as they enter the viewport, using one shared IntersectionObserver. First-screen content stays visible; newly streamed or filtered content is registered automatically. Reduced motion, keyboard focus, unavailable observers, and disabled JavaScript preserve readability. No ecommerce, contact form, accounts, fake statistics, testimonials, or certifications are added.

[Asset provenance and generation prompts](docs/ASSETS.md) document the original Facebook logo, saved promotional images, and generated visual assets. Replace sample packaging artwork with owner-approved cutouts through Filament before launch.

## Verification

```sh
npm run format:check
npm run lint
npm run typecheck
npm run test
npm run build
npx playwright install chromium
npm run test:e2e
```

Unit and server-rendered integration tests cover API retry/error behavior, translation parity, locale routes, catalog filters, page rendering, pack initialization, contact safety, SEO, and revalidation authorization. Browser tests use an isolated in-memory API fixture, not development seed data or external services. They cover all five pages in both languages at 360, 768, 1280, and 1920 px, search/sort/pagination, keyboard pack selection, language switching, the mobile menu, 404/error recovery, contacts, and revalidation. Screenshots are generated under `test-results/screenshots/`. The frontend CI workflow runs formatting, linting, type checks, tests, production build, and browser tests, then uploads browser reports.

In this workspace, local server startup is restricted (`listen EPERM`), so browser tests and viewport screenshots could not execute locally. Unit/integration tests and the production build run without a listening server. The CI browser workflow is configured but has not been executed here.

Scroll reveals use the Web Animations API with a shared intersection observer. Animation state stays outside HTML attributes, so streaming React hydration can complete without mismatches. Content remains readable with JavaScript disabled, unsupported browsers, or reduced motion enabled.

Product detail pages use `/{locale}/products/details?id={productId}`, for example `/ar/products/details?id=2`. Optional `pack` selects a pack: `/ar/products/details?id=2&pack=5`. Product cards, language switching, canonical/hreflang metadata, and sitemap entries retain the product ID. Missing, repeated, malformed, or unknown IDs show the not-found page. The former frontend `[slug]` route has been removed.

## Dynamic content verification

| Published Laravel data                                                        | Frontend binding                                                                                             |
| ----------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------ |
| Brand name, tagline, logo                                                     | Header, footer, hero, favicon, SEO brand suffix, organization metadata.                                      |
| Phone numbers, email, address, opening hours, Facebook, WhatsApp              | Contact page and appropriate footer/contact links; primary phone in organization metadata.                   |
| Map URL and coordinates                                                       | Approved map link, or a coordinate-based link when both coordinates are valid; structured location metadata. |
| Home title, intro, body, hero, gallery, SEO                                   | Home content, images, and metadata.                                                                          |
| About title, intro, body, hero, gallery, SEO                                  | About page and Home story preview.                                                                           |
| Contact title, intro, body, hero, gallery, SEO                                | Contact page and metadata.                                                                                   |
| Products, featured selection, text, cover/gallery, active packs and packaging | Home collection, catalog, ID detail page, selected-pack enquiry context, and localized metadata.             |
| Search, sorting, pagination, publication and locale                           | Laravel list queries and results; only published content and active packs are returned.                      |

UI labels remain in frontend translation JSON. Page bodies and product descriptions are plain text; the renderer also converts older seed HTML to safe text without inserting raw HTML.

Regenerate the integration fixtures from real Laravel API resources using PHP with PDO SQLite:

```sh
# From the project root; no development database or HTTP server is used.
php backend/scripts/export-frontend-fixtures.php
npm --prefix frontend test
```

On this Mac, `/Applications/MAMP/bin/php/php8.3.14/bin/php` provides PDO SQLite. The exporter forces a disposable in-memory database, fake storage and queues, runs the default seeders, and captures 16 successful localized API responses. `live-content.test.tsx` passes those responses through the centralized client and renders every page in both languages. Tests also exercise optional settings, page galleries, image-origin configuration, metadata, sitemap links, and selected product/pack enquiries. CI regenerates the fixtures before frontend checks, including when backend files change.

The browser API fixture uses the generated Laravel pages/settings and local copies of the media. Its extra products are synthetic test records used to verify pagination. Passing fixture tests verifies the application contract; it does not prove that a separately running development server, database, media server, or queue worker is reachable. Restart Next.js after environment/config changes. Match `MEDIA_BASE_URL` to Laravel `APP_URL`, including hostname and port.
