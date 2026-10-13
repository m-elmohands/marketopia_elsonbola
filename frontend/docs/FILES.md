# Frontend source files

Implementation manifest. Generated caches, dependencies, actual environment files, and test reports are excluded.

- `frontend/.env.example`
- `frontend/.env.production.example`
- `frontend/.env.staging.example`
- `frontend/.gitignore`
- `frontend/.prettierignore`
- `frontend/.prettierrc.json`
- `frontend/README.md`
- `frontend/docs/ASSETS.md`
- `frontend/docs/FILES.md`
- `frontend/eslint.config.mjs`
- `frontend/next-env.d.ts`
- `frontend/next.config.ts`
- `frontend/package-lock.json`
- `frontend/package.json`
- `frontend/playwright.config.ts`
- `frontend/postcss.config.mjs`
- `frontend/public/images/facebook-logo.jpg`
- `frontend/public/images/pack-collection.png`
- `frontend/public/images/rice-10kg.jpg`
- `frontend/public/images/rice-1kg.jpg`
- `frontend/public/images/rice-25kg.jpg`
- `frontend/public/images/rice-5kg.jpg`
- `frontend/public/images/rice-still-life.png`
- `frontend/scripts/import-logo.mjs`
- `frontend/src/app/(entry)/layout.tsx`
- `frontend/src/app/(entry)/page.tsx`
- `frontend/src/app/[locale]/about/page.tsx`
- `frontend/src/app/[locale]/contact/page.tsx`
- `frontend/src/app/[locale]/error.tsx`
- `frontend/src/app/[locale]/layout.tsx`
- `frontend/src/app/[locale]/loading.tsx`
- `frontend/src/app/[locale]/not-found.tsx`
- `frontend/src/app/[locale]/page.tsx`
- `frontend/src/app/[locale]/products/details/page.tsx`
- `frontend/src/app/[locale]/products/page.tsx`
- `frontend/src/app/api/revalidate/route.ts`
- `frontend/src/app/global-error.tsx`
- `frontend/src/app/globals.css`
- `frontend/src/app/robots.ts`
- `frontend/src/app/sitemap.ts`
- `frontend/src/components/brand.tsx`
- `frontend/src/components/contact-banner.tsx`
- `frontend/src/components/content-photo.tsx`
- `frontend/src/components/empty-state.tsx`
- `frontend/src/components/footer.tsx`
- `frontend/src/components/header.tsx`
- `frontend/src/components/navigation.tsx`
- `frontend/src/components/section-heading.tsx`
- `frontend/src/config/brand.json`
- `frontend/src/features/catalog/components/pack-selector.tsx`
- `frontend/src/features/catalog/components/product-card.tsx`
- `frontend/src/features/catalog/services/catalog.ts`
- `frontend/src/features/catalog/services/query.ts`
- `frontend/src/features/catalog/tests/query.test.ts`
- `frontend/src/features/catalog/types/index.ts`
- `frontend/src/features/content/services/content.ts`
- `frontend/src/features/content/types/index.ts`
- `frontend/src/features/home/components/hero.tsx`
- `frontend/src/features/home/components/home-sections.tsx`
- `frontend/src/features/settings/services/settings.ts`
- `frontend/src/features/settings/types/index.ts`
- `frontend/src/features/website/tests/pages.test.tsx`
- `frontend/src/i18n/ar.json`
- `frontend/src/i18n/config.test.ts`
- `frontend/src/i18n/config.ts`
- `frontend/src/i18n/en.json`
- `frontend/src/lib/api/client.test.ts`
- `frontend/src/lib/api/client.ts`
- `frontend/src/lib/demo.ts`
- `frontend/src/lib/links.ts`
- `frontend/src/lib/revalidation.test.ts`
- `frontend/src/lib/revalidation.ts`
- `frontend/src/lib/seo.ts`
- `frontend/tests/e2e/server.mjs`
- `frontend/tests/e2e/website.spec.ts`
- `frontend/tsconfig.json`
- `frontend/vitest.config.ts`
- `.github/workflows/frontend.yml`
- `backend/README.md` (frontend setup and checks reference)

- `frontend/src/features/animation/components/scroll-reveal.tsx`
- `frontend/src/features/animation/services/observe-scroll-reveal.ts`
- `frontend/src/features/animation/tests/observe-scroll-reveal.test.ts`

## Scroll reveal update

Changed files for fade-in-from-bottom on scroll:

- `frontend/src/features/animation/components/scroll-reveal.tsx`
- `frontend/src/features/animation/services/observe-scroll-reveal.ts`
- `frontend/src/features/animation/tests/observe-scroll-reveal.test.ts`
- `frontend/src/app/globals.css`
- `frontend/src/app/[locale]/layout.tsx`
- `frontend/src/app/[locale]/about/page.tsx`
- `frontend/src/app/[locale]/contact/page.tsx`
- `frontend/src/components/contact-banner.tsx`
- `frontend/src/features/home/components/home-sections.tsx`
- `frontend/src/features/catalog/components/product-card.tsx`
- `frontend/tests/e2e/website.spec.ts`
- `frontend/README.md`
- `frontend/docs/FILES.md`

## Scroll hydration correction

- `src/features/animation/services/observe-scroll-reveal.ts`: Web Animations replace mutations to React-rendered attributes.
- `src/features/animation/tests/observe-scroll-reveal.test.ts`: hydration-safe animation lifecycle checks.
- `src/app/globals.css`: removes attribute-driven reveal transitions.
- `tests/e2e/website.spec.ts`: asserts visual reveal behavior and no hydration console errors.
- `README.md`: documents animation behavior.

## Product details query route

- Removed `src/app/[locale]/products/[slug]/page.tsx`; replaced with `src/app/[locale]/products/details/page.tsx`.
- `src/features/catalog/services/product-id.ts`: strict query ID parsing.
- `src/features/catalog/services/catalog.ts`: requests Laravel details by ID.
- `src/features/catalog/components/product-card.tsx`: ID query links with optional pack.
- `src/app/sitemap.ts`: product ID detail URLs.
- `src/features/website/tests/pages.test.tsx`: query detail, validation, and metadata tests.
- `src/i18n/config.test.ts`: query preservation when changing languages.
- `tests/e2e/server.mjs`, `tests/e2e/website.spec.ts`: ID API fixture and browser routes.
- `README.md`, `docs/FILES.md`: routing documentation and file manifest.

- `src/features/catalog/tests/product-details.test.ts`: centralized API ID lookup and missing content tests.

## Dynamic data audit and bindings

- `.env.example`, local ignored `.env.local`: live mode and media origin aligned with Laravel.
- `next.config.ts`: default storage hostname matches Laravel.
- `src/app/[locale]/layout.tsx`: dynamic header logo, favicon, and organization metadata.
- `src/app/[locale]/page.tsx`: Home body/gallery and dynamic SEO brand.
- `src/app/[locale]/about/page.tsx`: body, hero/gallery, and dynamic SEO brand.
- `src/app/[locale]/contact/page.tsx`: body, hero/gallery, maps, SEO brand, and selected enquiry context.
- `src/app/[locale]/products/page.tsx`: search limit and dynamic SEO brand.
- `src/app/[locale]/products/details/page.tsx`: dynamic SEO brand.
- `src/components/brand.tsx`, `header.tsx`, `footer.tsx`: uploaded logo and dynamic copyright brand.
- `src/features/home/components/hero.tsx`, `home-sections.tsx`: dynamic tagline/address and About preview.
- `src/features/content/components/content-text.tsx`, `page-gallery.tsx`: safe text and page media rendering.
- `src/features/content/services/content-text.ts`, `tests/content-text.test.tsx`: plain/legacy text normalization and safety tests.
- `src/features/settings/services/location.ts`, `tests/location.test.ts`: validated coordinate/map links.
- `src/features/catalog/components/pack-selector.tsx`, `product-inquiry.tsx`: description text and selected product/pack context.
- `src/features/catalog/services/query.ts`, `tests/query.test.ts`: 100-character Laravel search limit.
- `src/features/website/tests/live-content.test.tsx`, `fixtures/laravel-content.json`: real Laravel response integration coverage.
- `src/features/website/tests/pages.test.tsx`: updated enquiry ID link.
- `src/lib/seo.ts`, `src/i18n/ar.json`, `src/i18n/en.json`: configurable SEO/copyright brand.
- `src/app/globals.css`: content, gallery, hero, and enquiry styles.
- `tests/e2e/server.mjs`, `website.spec.ts`: Laravel content fixture and contact checks.
- `README.md`, `docs/FILES.md`: data-binding matrix, setup, verification, and manifest.
- Project `.github/workflows/frontend.yml`: regenerates Laravel contract responses before frontend checks.
