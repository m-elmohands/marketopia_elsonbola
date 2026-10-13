import Link from "next/link";
import { notFound } from "next/navigation";
import { ArrowLeft } from "lucide-react";
import { dictionary, isLocale } from "@/i18n/config";
import { parseProductId } from "@/features/catalog/services/product-id";
import { getProduct } from "@/features/catalog/services/catalog";
import { getSettings } from "@/features/settings/services/settings";
import { PackSelector } from "@/features/catalog/components/pack-selector";
import { ContactBanner } from "@/components/contact-banner";
import { pageMetadata } from "@/lib/seo";
type Props = {
  params: Promise<{ locale: string }>;
  searchParams: Promise<Record<string, string | string[] | undefined>>;
};
export async function generateMetadata({ params, searchParams }: Props) {
  const { locale } = await params;
  if (!isLocale(locale)) return {};
  const id = parseProductId((await searchParams).id);
  if (id === null) return {};
  const [product, settings] = await Promise.all([
    getProduct(locale, id),
    getSettings(locale),
  ]);
  if (!product) return {};
  return pageMetadata(
    locale,
    `/products/details?id=${product.id}`,
    product.meta_title || product.name,
    product.meta_description ||
      product.short_description ||
      dictionary(locale).detailBody,
    settings.brand_name,
  );
}
export default async function ProductDetail({ params, searchParams }: Props) {
  const { locale } = await params;
  if (!isLocale(locale)) notFound();
  const query = await searchParams;
  const id = parseProductId(query.id);
  if (id === null) notFound();
  const [product, settings] = await Promise.all([
    getProduct(locale, id),
    getSettings(locale),
  ]);
  if (!product) notFound();
  const t = dictionary(locale);
  return (
    <>
      <section className="container product-detail">
        <Link className="text-link back-link" href={`/${locale}/products`}>
          <ArrowLeft className="direction-icon" size={18} aria-hidden="true" />
          {t.backProducts}
        </Link>
        <PackSelector
          key={`${locale}-${product.id}-${query.pack}`}
          locale={locale}
          product={product}
          initialPack={typeof query.pack === "string" ? query.pack : undefined}
          settings={{ whatsapp_number: settings.whatsapp_number }}
        />
      </section>
      <ContactBanner locale={locale} />
    </>
  );
}
