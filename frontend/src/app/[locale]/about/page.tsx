import { ContentText } from "@/features/content/components/content-text";
import { PageGallery } from "@/features/content/components/page-gallery";
import { notFound } from "next/navigation";
import Link from "next/link";
import { ArrowUpRight, MapPin } from "lucide-react";
import { dictionary, isLocale } from "@/i18n/config";
import { getPage } from "@/features/content/services/content";
import { getSettings } from "@/features/settings/services/settings";
import { ContentPhoto } from "@/components/content-photo";
import { ContactBanner } from "@/components/contact-banner";
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
      getPage(locale, "about"),
      getSettings(locale),
    ]);
  return pageMetadata(
    locale,
    "/about",
    page?.meta_title || t.about,
    page?.meta_description || t.aboutIntro,
    settings.brand_name,
  );
}
export default async function About({
  params,
}: {
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  if (!isLocale(locale)) notFound();
  const t = dictionary(locale),
    [page, settings] = await Promise.all([
      getPage(locale, "about"),
      getSettings(locale),
    ]);
  const hero =
    page?.media.find((image) => image.collection === "hero") ?? page?.media[0];
  return (
    <>
      <section className="container page-heading">
        <p className="eyebrow">{t.aboutEyebrow}</p>
        <h1>
          {page?.title || (
            <>
              {t.aboutTitle}
              <br />
              <em>{t.aboutAccent}</em>
            </>
          )}
        </h1>
        <p>{page?.intro || t.aboutIntro}</p>
      </section>
      <section className="container about-grid" data-scroll-reveal>
        <ContentPhoto locale={locale} media={hero} preload />
        <div className="about-copy">
          <p className="eyebrow">{t.storyEyebrow}</p>
          <h2>
            {t.storyTitle}
            <br />
            <em>{t.storyAccent}</em>
          </h2>
          <ContentText text={page?.body || t.storyBody} />
          {!page?.body && <p>{t.aboutBody}</p>}
          {settings.address && (
            <div className="location-note">
              <MapPin size={24} strokeWidth={1.4} aria-hidden="true" />
              <div>
                <span className="eyebrow">{t.aboutLocation}</span>
                <p>{settings.address}</p>
              </div>
            </div>
          )}
          <Link className="text-link" href={`/${locale}/products`}>
            {t.explore}
            <ArrowUpRight
              className="direction-icon"
              size={20}
              aria-hidden="true"
            />
          </Link>
        </div>
      </section>
      <PageGallery
        locale={locale}
        media={page?.media ?? []}
        primaryId={hero?.id}
      />
      <ContactBanner locale={locale} />
    </>
  );
}
