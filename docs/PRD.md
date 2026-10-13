# Golden Spike Business Website PRD

Build a creative bilingual business website for **السنبلة الذهبية** that introduces the rice mill, showcases its products and pack sizes, and helps visitors contact the business. The confirmed scope is a business website with a product showcase. Frontend: Next.js. Backend: Laravel. Administration: Filament. Languages: Arabic and English.

## Business analysis

Source: the locally saved [Facebook page](../elsonbola.html) and its [promotional images](../elsonbola_files/). This is a snapshot, not a live business verification.

| Finding | Website implication |
| --- | --- |
| Page name: مضرب أرز السنبلة الذهبية; artwork uses “Golden Spike” | Preserve the Arabic name; confirm the official English name before launch. |
| Egyptian rice promoted in 1 kg, 5 kg, 10 kg, and 25 kg packs | Show pack variants within each product; confirm current availability. |
| Location: Denshal village, Damanhour, Beheira, Egypt; artwork states there are no other branches | Use one business location rather than a branches directory. Confirm map coordinates. |
| Page phone: 01005263014; artwork also shows 01000521423 and 01029035139 | Allow multiple contact numbers and one primary number. Verify each before publishing. |
| Page email: zikry0100@gmail.com | Provide a direct email contact after confirmation. |
| Gold, dark green, black, rice imagery, and branded packaging | Build a consistent visual identity around these elements. |
| Promotional themes include quality, grain selection, purity, and Egyptian origin | Create a concise quality section using owner-approved claims. |

The likely audience includes households and businesses purchasing larger packs. Wholesale supply and restaurant targeting are assumptions to validate. Promotional factory illustrations do not establish actual facilities, certifications, history, or production capacity. Do not publish those claims without owner confirmation. Facebook interface elements, personal notifications, and comments are not website content.

## Goals and audience

- Help visitors understand the brand and available products quickly.
- Present packaging, weights, and descriptions clearly in both languages.
- Make calling, emailing, and locating the business easy.
- Let administrators maintain content and images without code changes.

Visitors browse without accounts. Administrators manage all content; editors manage products and pages but cannot manage administrator accounts or permanently delete records.

## Pages and requirements

| Page or feature | Requirements |
| --- | --- |
| Home | Product-led hero, short brand introduction, featured products, quality highlights, location summary, and contact CTA. |
| About | Business story, Egyptian rice positioning, approved quality information, and authentic photos when available. |
| Products | Published products with cover images, descriptions, available pack sizes, pagination, and search. Avoid categories until the catalog requires them. |
| Product detail | Product description, gallery, selectable pack sizes with corresponding imagery, and “Contact us about this product” CTA. Selecting a pack changes the displayed details without creating an order. |
| Contact | Verified phone numbers, email, address, opening-hours text, map link, and Facebook link. WhatsApp appears only if its number is confirmed. |
| Language switching | `/ar` and `/en` routes; preserve the current page when switching languages. Arabic is the default and uses RTL; English uses LTR. |
| Filament | Manage products, pack variants, fixed pages, business settings, translations, media, publication status, ordering, and featured products. |

Contact uses direct links. The first release has no contact form, quotation workflow, cart, checkout, payments, customer accounts, inventory, blog, or Facebook feed integration.

## Creative design direction

Use a warm cream background, deep green sections, restrained gold accents, and large product photography. A product pack should anchor the hero, with subtle grain or wheat decoration and a clear CTA. Use dark text on gold surfaces for contrast.

Give packaging images generous space and present weights as easy-to-scan choices. Use readable Arabic and English typography with comparable visual hierarchy. Keep text separate from promotional artwork so it can translate and resize naturally. Prefer approved packaging cutouts and authentic photography over repeating entire Facebook posters.

Use short entrance transitions and gentle hover effects. Respect reduced-motion preferences and keep core content visible without animation. All interactions must support keyboard navigation, visible focus, semantic headings, descriptive image alternatives, and adequate contrast.

## Content and translation

- Store interface labels in frontend `ar.json` and `en.json`; keep Filament labels in Laravel JSON language files.
- Store editable business content in translation tables through `astrotomic/laravel-translatable`.
- Require complete Arabic and English content before publishing. Preview missing translations in admin; do not silently mix languages on the public site.
- Store photographs and packaging files through `spatie/laravel-medialibrary`; keep editable names, descriptions, and SEO text in the database.
- Write localized image alternatives. Use locale-specific artwork where an image itself contains language-dependent text.

## Technical requirements

Use Next.js Server Components by default and Client Components for pack selection, navigation, and other browser interactions only. Use strict TypeScript and a centralized typed API client with timeouts, limited retries for safe reads, and consistent error handling. Provide dedicated loading, empty, and error states; use toasts for operational failures in interactive controls.

Expose read-only public endpoints under `/api/v1`: settings, pages by key, products, and product details by numeric ID (`/api/v1/products/details?id=2`). The website uses `/ar/products/details?id=2` and `/en/products/details?id=2`; the existing slug API lookup remains available. Accept `locale=ar|en`; reject unsupported locales. Product lists support pagination, search, featured filtering, and allowlisted sorting. Use a consistent `data`, `message`, `errors`, and pagination `meta` envelope. Document endpoints in OpenAPI.

Keep Laravel controllers thin with domain Actions or Services, Form Requests, API Resources, Eloquent scopes, and eager-loaded translations, variants, and media. Cache public settings, pages, and product responses; invalidate affected entries after Filament changes and invalidate or revalidate Next.js content too. Queue media conversions and other long-running work.

Enforce policies on every Filament action. Confirm destructive actions, show success/failure notifications, support recovery through soft deletion, and audit publication, deletion, account, and settings changes. Extract Filament forms and tables into focused classes.

Optimize responsive images and reserve image dimensions. Prioritize the hero image, lazy-load lower-page media, and keep animation dependencies small. Target Core Web Vitals of LCP ≤ 2.5 seconds, INP ≤ 200 ms, and CLS ≤ 0.1 at the 75th percentile once production traffic is sufficient. Add localized metadata, canonical URLs, `hreflang`, sitemap, and business structured data using verified information.

## Acceptance criteria

- Home, About, Products, product detail, and Contact work in Arabic and English with correct direction and language switching.
- Published product content includes both translations and at least one active pack variant; unpublished or deleted content is unavailable publicly.
- Administrators can create, translate, preview, publish, reorder, and restore products and pages; public content refreshes after updates.
- Pack selection shows the correct weight and imagery with keyboard support.
- Contact links use verified numbers and the correct address; no unconfirmed branch or certification claims appear.
- Layouts are verified at mobile, tablet, laptop, and large-desktop widths, including 360, 768, 1280, and 1920 px.
- Unit and integration tests cover publication, translations, policies, filtering, cache invalidation, and deletion. Frontend tests cover language switching, product browsing, and pack selection, including accessibility.
- Formatting, linting, type checking, backend and frontend tests, and production builds pass locally and in CI. Tests use factories and isolated databases without external services.

## Delivery and launch inputs

Implement in three stages: confirm content and visual direction; build Laravel models, APIs, and Filament; build and verify the Next.js website. Use locally installed PHP, MySQL, Node.js, and npm. Keep development, staging, and production configurations separate, with secrets in environment variables. Document setup, configuration, and architecture during implementation.

Before launch, obtain the official logo, approved English name, real product assets, confirmed pack availability, business story, contact numbers, opening hours, map coordinates, and owner-approved translations and quality claims. These are content dependencies rather than reasons to expand the website scope.
