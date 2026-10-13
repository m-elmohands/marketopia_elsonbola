import { ContentText } from "@/features/content/components/content-text";
import { PageGallery } from "@/features/content/components/page-gallery";
import { notFound } from "next/navigation";
import { dictionary, isLocale } from "@/i18n/config";
import { getSettings } from "@/features/settings/services/settings";
import { getPage } from "@/features/content/services/content";
import { getProducts } from "@/features/catalog/services/catalog";
import { Hero } from "@/features/home/components/hero";
import {
  OriginStrip,
  StoryPreview,
  Collection,
  TableStory,
} from "@/features/home/components/home-sections";
import { ContactBanner } from "@/components/contact-banner";
import { isDemoMode } from "@/lib/demo";
import { pageMetadata } from "@/lib/seo";
export async function generateMetadata({
  params,
}: {
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  if (!isLocale(locale)) return {};
  const t = dictionary(locale),
    [page, settings] = await Promise.all([
      getPage(locale, "home"),
      getSettings(locale),
    ]);
  return pageMetadata(
    locale,
    "",
    page?.meta_title || settings.brand_name,
    page?.meta_description || t.heroBody,
    settings.brand_name,
  );
}
export default async function Home({
  params,
}: {
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  if (!isLocale(locale)) notFound();
  const [settings, page, about, products] = await Promise.all([
    getSettings(locale),
    getPage(locale, "home"),
    getPage(locale, "about"),
    getProducts(locale, { featured: true, per_page: 3 }),
  ]);
  return (
    <>
      <Hero
        locale={locale}
        page={page}
        settings={settings}
        demo={isDemoMode()}
      />
      {page?.body && (
        <section className="container page-body" data-scroll-reveal>
          <ContentText text={page.body} />
        </section>
      )}
      <PageGallery
        locale={locale}
        media={page?.media ?? []}
        primaryId={
          page?.media.find((image) => image.collection === "hero")?.id ??
          page?.media[0]?.id
        }
      />
      <OriginStrip locale={locale} />
      <StoryPreview locale={locale} page={about} />
      <Collection locale={locale} products={products.data} />
      <TableStory locale={locale} settings={settings} />
      <ContactBanner locale={locale} />
    </>
  );
}
