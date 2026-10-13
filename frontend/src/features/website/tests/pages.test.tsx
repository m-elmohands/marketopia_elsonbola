import { beforeEach, describe, expect, it, vi } from "vitest";
import { renderToStaticMarkup } from "react-dom/server";
import brand from "@/config/brand.json";
import Home from "@/app/[locale]/page";
import About from "@/app/[locale]/about/page";
import Contact from "@/app/[locale]/contact/page";
import Products from "@/app/[locale]/products/page";
import ProductDetail, {
  generateMetadata,
} from "@/app/[locale]/products/details/page";
import WebsiteLayout from "@/app/[locale]/layout";
import { dictionary, type Locale } from "@/i18n/config";
import { PackSelector } from "@/features/catalog/components/pack-selector";
import { demoProducts } from "@/lib/demo";
import { pageMetadata } from "@/lib/seo";

const route = vi.hoisted(() => ({ locale: "en" }));
vi.mock("next/navigation", () => ({
  usePathname: () => `/${route.locale}`,
  useRouter: () => ({ push: vi.fn() }),
  useParams: () => ({ locale: route.locale }),
  notFound: () => {
    throw new Error("NEXT_NOT_FOUND");
  },
}));
beforeEach(() => {
  vi.stubEnv("WEBSITE_DEMO_MODE", "true");
});
for (const locale of ["ar", "en"] as const) {
  describe(`${locale} server-rendered website`, () => {
    beforeEach(() => {
      route.locale = locale;
    });
    const params = Promise.resolve({ locale });
    const t = dictionary(locale);
    it("renders the home journey and links to real product detail routes", async () => {
      const html = renderToStaticMarkup(await Home({ params }));
      expect(html).toContain(t.heroLine1);
      expect(html).toContain(t.collectionTitle);
      expect(html).toContain(t.contactTitle);
      expect(html).toContain(`/${locale}/products/details?id=1&amp;pack=5`);
    });
    it("renders About and Contact with the localized content", async () => {
      expect(renderToStaticMarkup(await About({ params }))).toContain(
        t.aboutTitle,
      );
      const html = renderToStaticMarkup(await Contact({ params }));
      expect(html).toContain(t.contactPageTitle);
      expect(html).toContain('href="tel:+201005263014"');
      expect(html).not.toContain("wa.me");
    });
    it("renders GET search controls and a useful empty state", async () => {
      const html = renderToStaticMarkup(
        await Products({
          params,
          searchParams: Promise.resolve({ search: "missing" }),
        }),
      );
      expect(html).toContain('method="GET"');
      expect(html).toContain(`action="/${locale}/products"`);
      expect(html).toContain(t.emptyTitle);
      expect(html).toContain('for="search"');
    });
    it("renders the requested pack and labels it accessibly", async () => {
      const html = renderToStaticMarkup(
        await ProductDetail({
          params: Promise.resolve({ locale }),
          searchParams: Promise.resolve({ id: "1", pack: "10" }),
        }),
      );
      expect(html).toMatch(/checked=""[^>]*value="10"/);
      expect(html).toContain('role="status"');
      expect(html).toContain(t.choosePack);
      expect(html).toContain("rice-10kg");
    });
    it("sets document direction and imports the Facebook logo", async () => {
      const html = renderToStaticMarkup(
        await WebsiteLayout({ children: <h1>Test</h1>, params }),
      );
      expect(html).toContain(`lang="${locale}"`);
      expect(html).toContain(`dir="${locale === "ar" ? "rtl" : "ltr"}"`);
      expect(decodeURIComponent(html)).toContain(brand.logo);
      expect(html).toContain(t.demo);
      expect(html).not.toContain('type="application/ld+json"');
    });
    it("sets localized canonical links and excludes demos from indexing", () => {
      const meta = pageMetadata(
        locale,
        "/products",
        t.products,
        t.catalogIntro,
      );
      expect(meta.alternates?.canonical).toContain(`/${locale}/products`);
      expect(meta.robots).toMatchObject({ index: false });
    });
  });
}
it("rejects unpublished products and unsupported locales", async () => {
  await expect(
    ProductDetail({
      params: Promise.resolve({ locale: "en" }),
      searchParams: Promise.resolve({ id: "999999" }),
    }),
  ).rejects.toThrow("NEXT_NOT_FOUND");
  await expect(
    Home({ params: Promise.resolve({ locale: "fr" }) }),
  ).rejects.toThrow("NEXT_NOT_FOUND");
});
it("an invalid initial pack falls back to the first valid pack", () => {
  const locale: Locale = "en";
  const html = renderToStaticMarkup(
    <PackSelector
      product={demoProducts(locale)[0]}
      locale={locale}
      initialPack="999"
      settings={{ whatsapp_number: null }}
    />,
  );
  expect(html).toMatch(/checked=""[^>]*value="1"/);
  expect(html).toContain("/en/contact?product=1&amp;pack=1");
});

for (const id of [
  undefined,
  "",
  "0",
  "-1",
  "1.5",
  "abc",
  "01",
  "9007199254740992",
  ["1", "2"],
]) {
  it(`rejects an invalid product ID: ${JSON.stringify(id)}`, async () => {
    await expect(
      ProductDetail({
        params: Promise.resolve({ locale: "en" }),
        searchParams: Promise.resolve({ id }),
      }),
    ).rejects.toThrow("NEXT_NOT_FOUND");
  });
}
it("detail metadata preserves the product ID and excludes the pack query", async () => {
  const metadata = await generateMetadata({
    params: Promise.resolve({ locale: "ar" }),
    searchParams: Promise.resolve({ id: "1", pack: "10" }),
  });
  expect(metadata.alternates?.canonical).toContain("/ar/products/details?id=1");
  expect(metadata.alternates?.languages?.en).toContain(
    "/en/products/details?id=1",
  );
  expect(metadata.alternates?.canonical).not.toContain("pack=");
});
