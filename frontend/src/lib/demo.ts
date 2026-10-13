import type { Locale } from "@/i18n/config";
import type {
  Product,
  Media,
  CatalogQuery,
  Pagination,
} from "@/features/catalog/types";
import type { BusinessSettings } from "@/features/settings/types";
export function isDemoMode(): boolean {
  return process.env.WEBSITE_DEMO_MODE === "true";
}
const artwork: Record<number, string> = {
  1: "/images/rice-1kg.jpg",
  5: "/images/rice-5kg.jpg",
  10: "/images/rice-10kg.jpg",
  25: "/images/rice-25kg.jpg",
};
function media(weight: number, locale: Locale): Media {
  return {
    id: weight,
    collection: "cover",
    url: artwork[weight],
    web_url: artwork[weight],
    thumbnail_url: artwork[weight],
    alt:
      locale === "ar"
        ? `تصميم ترويجي لعبوة أرز ${weight} كجم`
        : `Promotional artwork for a ${weight} kg rice pack`,
    locale: null,
  };
}
export function demoProducts(locale: Locale): Product[] {
  return [
    {
      id: 1,
      slug: "egyptian-rice",
      name:
        locale === "ar"
          ? "أرز مصري — السنبلة الذهبية"
          : "Golden Spike Egyptian Rice",
      short_description:
        locale === "ar"
          ? "أرز مصري بأحجام متعددة تناسب مائدتك."
          : "Egyptian rice in a range of pack sizes for your table.",
      description:
        locale === "ar"
          ? "اكتشف عبوات ١ و٥ و١٠ و٢٥ كجم. هذه معاينة مستوحاة من الصفحة المحفوظة؛ تواصل مع الفريق لتأكيد التوفر الحالي."
          : "Explore 1, 5, 10, and 25 kg packs. This preview is based on the saved Facebook page; speak with the team to confirm current availability.",
      meta_title: null,
      meta_description: null,
      is_featured: true,
      media: [media(5, locale)],
      variants: [1, 5, 10, 25].map((weight) => ({
        id: weight,
        weight_kg: String(weight),
        media: [media(weight, locale)],
      })),
    },
  ];
}
export function demoCatalog(
  locale: Locale,
  query: CatalogQuery,
): { data: Product[]; meta: Pagination } {
  const filtered = demoProducts(locale).filter(
    (p) =>
      (!query.featured || p.is_featured) &&
      (!query.search ||
        p.name.toLocaleLowerCase().includes(query.search.toLocaleLowerCase())),
  );
  const page = query.page ?? 1,
    perPage = query.per_page ?? 12;
  return {
    data: filtered.slice((page - 1) * perPage, page * perPage),
    meta: {
      current_page: page,
      per_page: perPage,
      total: filtered.length,
      last_page: Math.max(1, Math.ceil(filtered.length / perPage)),
    },
  };
}
export function demoSettings(locale: Locale): BusinessSettings {
  return {
    brand_name: locale === "ar" ? "السنبلة الذهبية" : "Golden Spike",
    tagline: null,
    address:
      locale === "ar"
        ? "قرية دنشال، دمنهور، البحيرة، مصر"
        : "Denshal, Damanhour, Beheira, Egypt",
    opening_hours: null,
    quality_highlights: [],
    email: "zikry0100@gmail.com",
    facebook_url: null,
    whatsapp_number: null,
    map_url: null,
    latitude: null,
    longitude: null,
    contact_numbers: [{ phone: "+201005263014", is_primary: true }],
    media: [],
  };
}
