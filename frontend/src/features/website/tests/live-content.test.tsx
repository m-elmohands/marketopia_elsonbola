import nextConfig from "../../../../next.config";
import { readFileSync } from "node:fs";
import { afterEach, beforeEach, describe, expect, it, vi } from "vitest";
import { renderToStaticMarkup } from "react-dom/server";
import Home, { generateMetadata as homeMetadata } from "@/app/[locale]/page";
import About, {
  generateMetadata as aboutMetadata,
} from "@/app/[locale]/about/page";
import Contact, {
  generateMetadata as contactMetadata,
} from "@/app/[locale]/contact/page";
import Products from "@/app/[locale]/products/page";
import ProductDetail from "@/app/[locale]/products/details/page";
import WebsiteLayout, {
  generateMetadata as layoutMetadata,
} from "@/app/[locale]/layout";
import sitemap from "@/app/sitemap";
import type { ApiEnvelope } from "@/lib/api/client";
import type { BusinessSettings } from "@/features/settings/types";
import type { ContentPage } from "@/features/content/types";
import type { Product } from "@/features/catalog/types";
import { contentParagraphs } from "@/features/content/services/content-text";

const route = vi.hoisted(() => ({ locale: "en" }));
vi.mock("next/navigation", () => ({
  usePathname: () => `/${route.locale}`,
  useRouter: () => ({ push: vi.fn() }),
  notFound: () => {
    throw new Error("NEXT_NOT_FOUND");
  },
}));

type Fixture = ApiEnvelope<
  BusinessSettings | ContentPage | Product | Product[]
>;
const original = JSON.parse(
  readFileSync(
    new URL("./fixtures/laravel-content.json", import.meta.url),
    "utf8",
  ),
) as Record<string, Fixture>;
let fixtures: Record<string, Fixture>;
let fetcher: ReturnType<typeof vi.fn<typeof fetch>>;
function requestKey(path: string): string {
  const url = new URL(path, "http://localhost:8000");
  url.searchParams.sort();
  return `${url.pathname}?${url.searchParams}`;
}
function data<T>(path: string): T {
  return fixtures[requestKey(path)].data as T;
}
function assertText(html: string, text: string) {
  const escaped = renderToStaticMarkup(<span>{text}</span>).replace(
    /^<span>|<\/span>$/g,
    "",
  );
  expect(html).toContain(escaped);
}

function assertImageUrl(html: string, url: string) {
  const images = [...html.matchAll(/<img\b[^>]*>/g)];
  expect(
    images.some(
      ([image]) =>
        image.includes(url) || image.includes(encodeURIComponent(url)),
    ),
  ).toBe(true);
}

beforeEach(() => {
  fixtures = Object.fromEntries(
    Object.entries(structuredClone(original)).map(([path, response]) => [
      requestKey(path),
      response,
    ]),
  );
  vi.stubEnv("WEBSITE_DEMO_MODE", "false");
  vi.stubEnv("API_BASE_URL", "http://localhost:8000/api/v1");
  fetcher = vi.fn<typeof fetch>(async (input) => {
    const fixture = fixtures[requestKey(String(input))];
    if (!fixture) throw new Error(`Uncovered API request: ${String(input)}`);
    return Response.json(fixture);
  });
  vi.stubGlobal("fetch", fetcher);
});
afterEach(() => {
  vi.unstubAllGlobals();
  vi.unstubAllEnvs();
});

for (const locale of ["ar", "en"] as const) {
  describe(`${locale} real Laravel content`, () => {
    const params = Promise.resolve({ locale });
    beforeEach(() => {
      route.locale = locale;
    });
    it("renders the complete home data, quality points, and featured product links", async () => {
      const home = data<ContentPage>(`/api/v1/pages/home?locale=${locale}`);
      const about = data<ContentPage>(`/api/v1/pages/about?locale=${locale}`);
      const settings = data<BusinessSettings>(
        `/api/v1/settings?locale=${locale}`,
      );
      const html = renderToStaticMarkup(await Home({ params }));
      for (const text of [
        home.title,
        home.intro!,
        about.title,
        about.intro!,
        ...contentParagraphs(home.body!),
        ...settings.quality_highlights,
      ])
        assertText(html, text);
      assertImageUrl(html, home.media[0].web_url);
      expect(html).toContain(`/${locale}/products/details?id=1`);
      expect(
        fetcher.mock.calls.some(([url]) => String(url).includes("featured=1")),
      ).toBe(true);
    });
    it("renders About and Contact from Laravel, including their bodies and contact details", async () => {
      const settings = data<BusinessSettings>(
        `/api/v1/settings?locale=${locale}`,
      );
      for (const [key, render] of [
        ["about", About],
        ["contact", Contact],
      ] as const) {
        const page = data<ContentPage>(`/api/v1/pages/${key}?locale=${locale}`);
        const html = renderToStaticMarkup(await render({ params }));
        for (const text of [
          page.title,
          page.intro!,
          ...contentParagraphs(page.body!),
          settings.address!,
        ])
          assertText(html, text);
        if (key === "contact") {
          expect(html).toContain(`href="mailto:${settings.email}"`);
          expect(html).toContain(
            `href="tel:${settings.contact_numbers[0].phone}"`,
          );
        }
      }
    });
    it("renders the catalog and every active pack from the ID detail response", async () => {
      const catalog = renderToStaticMarkup(
        await Products({ params, searchParams: Promise.resolve({}) }),
      );
      const product = data<Product>(
        `/api/v1/products/details?locale=${locale}&id=1`,
      );
      assertText(catalog, product.name);
      expect(catalog).toContain(`/${locale}/products/details?id=${product.id}`);
      for (const pack of product.variants) {
        const html = renderToStaticMarkup(
          await ProductDetail({
            params,
            searchParams: Promise.resolve({
              id: String(product.id),
              pack: String(pack.id),
            }),
          }),
        );
        assertText(html, product.name);
        assertText(html, product.short_description!);
        for (const paragraph of contentParagraphs(product.description!))
          assertText(html, paragraph);
        expect(html).toMatch(new RegExp(`checked=""[^>]*value="${pack.id}"`));
        const image =
          pack.media[0] ??
          product.media.find((item) => item.collection === "cover");
        assertImageUrl(html, image!.web_url);
      }
    });
    it("retains the selected product and pack on the Contact page", async () => {
      const product = data<Product>(
        `/api/v1/products/details?locale=${locale}&id=1`,
      );
      const pack = product.variants[1];
      const html = renderToStaticMarkup(
        await Contact({
          params,
          searchParams: Promise.resolve({
            product: String(product.id),
            pack: String(pack.id),
          }),
        }),
      );
      assertText(html, product.name);
      expect(html).toContain(
        `/${locale}/products/details?id=1&amp;pack=${pack.id}`,
      );
    });
    it("uses the admin logo in header, footer, favicon, and organization metadata", async () => {
      const settings = data<BusinessSettings>(
        `/api/v1/settings?locale=${locale}`,
      );
      const logo = settings.media.find((image) => image.collection === "logo")!;
      const html = renderToStaticMarkup(
        await WebsiteLayout({ params, children: <h1>Content</h1> }),
      );
      assertImageUrl(html, logo.web_url);
      assertText(html, settings.brand_name);
      expect(html).toContain('type="application/ld+json"');
      expect(html).not.toContain('class="demo-note"');
      expect(await layoutMetadata({ params })).toMatchObject({
        icons: { icon: logo.web_url },
      });
    });
    it("uses each page's SEO fields and the configured brand", async () => {
      const settings = data<BusinessSettings>(
        `/api/v1/settings?locale=${locale}`,
      );
      settings.brand_name = `Configured brand ${locale}`;
      for (const [key, metadata] of [
        ["home", homeMetadata],
        ["about", aboutMetadata],
        ["contact", contactMetadata],
      ] as const) {
        const page = data<ContentPage>(`/api/v1/pages/${key}?locale=${locale}`);
        const result = await metadata({ params });
        expect(result.title).toBe(
          `${page.meta_title} | ${settings.brand_name}`,
        );
        expect(result.description).toBe(page.meta_description);
      }
    });
    it("uses page hero/gallery media and optional settings without hardcoded replacements", async () => {
      const settings = data<BusinessSettings>(
        `/api/v1/settings?locale=${locale}`,
      );
      settings.opening_hours = `Hours ${locale}`;
      settings.whatsapp_number = "+201000000001";
      settings.facebook_url = "https://example.test/facebook";
      settings.latitude = "0";
      settings.longitude = "0";
      const page = data<ContentPage>(`/api/v1/pages/contact?locale=${locale}`);
      const media = settings.media[0];
      page.media = [
        { ...media, id: 90, collection: "hero", alt: `Hero ${locale}` },
        { ...media, id: 91, collection: "gallery", alt: `Gallery ${locale}` },
      ];
      const html = renderToStaticMarkup(await Contact({ params }));
      expect(html).toContain(`alt="Hero ${locale}"`);
      expect(html).toContain(`alt="Gallery ${locale}"`);
      assertText(html, settings.opening_hours);
      expect(html).toContain("https://wa.me/201000000001");
      expect(html).toContain("https://example.test/facebook");
      expect(html).toContain("query=0,0");
      const layout = renderToStaticMarkup(
        await WebsiteLayout({ params, children: null }),
      );
      expect(layout).toContain('"latitude":0,"longitude":0');
    });
  });
}
it("indexes published ID routes in both languages using Laravel pagination", async () => {
  const entries = await sitemap();
  const details = entries.filter((entry) =>
    entry.url.includes("/products/details?id="),
  );
  expect(details).toHaveLength(2);
  expect(details.map((entry) => entry.url)).toEqual(
    expect.arrayContaining([
      expect.stringContaining("/ar/products/details?id=1"),
      expect.stringContaining("/en/products/details?id=1"),
    ]),
  );
});

it("allows Laravel media origins in the Next image configuration", () => {
  const patterns = nextConfig.images?.remotePatterns ?? [];
  for (const response of Object.values(original)) {
    const records = Array.isArray(response.data)
      ? response.data
      : [response.data];
    for (const record of records) {
      const media = [
        ...record.media,
        ...("variants" in record
          ? record.variants.flatMap((variant) => variant.media)
          : []),
      ];
      for (const image of media) {
        const url = new URL(image.web_url);
        expect(url.pathname).toMatch(/^\/storage\//);
        expect(
          patterns.some(
            (pattern) =>
              pattern.hostname === url.hostname &&
              pattern.port === url.port &&
              pattern.protocol === url.protocol.replace(":", ""),
          ),
        ).toBe(true);
      }
    }
  }
});
