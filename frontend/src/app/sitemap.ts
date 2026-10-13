export const dynamic = "force-dynamic";
import type { MetadataRoute } from "next";
import { locales } from "@/i18n/config";
import { getProducts } from "@/features/catalog/services/catalog";
import { siteUrl } from "@/lib/seo";
import { isDemoMode } from "@/lib/demo";
export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  if (isDemoMode()) return [];
  const paths = ["", "/about", "/products", "/contact"];
  const first = await getProducts("ar", { per_page: 50 });
  const pages = await Promise.all(
    Array.from({ length: Math.max(0, first.meta.last_page - 1) }, (_, i) =>
      getProducts("ar", { per_page: 50, page: i + 2 }),
    ),
  );
  const products = [...first.data, ...pages.flatMap((p) => p.data)];
  const routes = [
    ...paths,
    ...products.map((p) => `/products/details?id=${p.id}`),
  ];
  return locales.flatMap((locale) =>
    routes.map((path) => ({
      url: `${siteUrl}/${locale}${path}`,
      alternates: {
        languages: { ar: `${siteUrl}/ar${path}`, en: `${siteUrl}/en${path}` },
      },
    })),
  );
}
