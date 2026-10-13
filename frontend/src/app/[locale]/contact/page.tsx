import { getProduct } from "@/features/catalog/services/catalog";
import { parseProductId } from "@/features/catalog/services/product-id";
import { ProductInquiry } from "@/features/catalog/components/product-inquiry";
import { ContentText } from "@/features/content/components/content-text";
import { PageGallery } from "@/features/content/components/page-gallery";
import { ContentPhoto } from "@/components/content-photo";
import { mapUrl } from "@/features/settings/services/location";
import { notFound } from "next/navigation";
import { ArrowUpRight, Mail, MapPin, Phone, Clock, Wheat } from "lucide-react";
import { dictionary, isLocale } from "@/i18n/config";
import { getSettings } from "@/features/settings/services/settings";
import { getPage } from "@/features/content/services/content";
import { pageMetadata } from "@/lib/seo";
import { phoneUrl, safeExternalUrl, whatsappUrl } from "@/lib/links";
export async function generateMetadata({
  params,
}: {
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  if (!isLocale(locale)) return {};
  const t = dictionary(locale),
    [page, settings] = await Promise.all([
      getPage(locale, "contact"),
      getSettings(locale),
    ]);
  return pageMetadata(
    locale,
    "/contact",
    page?.meta_title || t.contact,
    page?.meta_description || t.contactPageIntro,
    settings.brand_name,
  );
}
export default async function Contact({
  params,
  searchParams,
}: {
  params: Promise<{ locale: string }>;
  searchParams?: Promise<Record<string, string | string[] | undefined>>;
}) {
  const { locale } = await params;
  if (!isLocale(locale)) notFound();
  const query = (await searchParams) ?? {};
  const productId = parseProductId(query.product);
  const t = dictionary(locale),
    [settings, page, product] = await Promise.all([
      getSettings(locale),
      getPage(locale, "contact"),
      productId === null ? null : getProduct(locale, productId),
    ]);
  const hero =
    page?.media.find((image) => image.collection === "hero") ?? page?.media[0];
  const map = mapUrl(settings),
    facebook = safeExternalUrl(settings.facebook_url);
  return (
    <>
      <section className="container page-heading">
        <p className="eyebrow">{t.contactEyebrow}</p>
        <h1>
          {page?.title || (
            <>
              {t.contactPageTitle}
              <br />
              <em>{t.contactPageAccent}</em>
            </>
          )}
        </h1>
        <p>{page?.intro || t.contactPageIntro}</p>
      </section>
      <section className="container contact-page-grid">
        <div className="contact-list">
          {product && (
            <ProductInquiry
              locale={locale}
              product={product}
              packId={typeof query.pack === "string" ? query.pack : undefined}
            />
          )}
          {settings.contact_numbers.length > 0 && (
            <div className="contact-item" data-scroll-reveal>
              <Phone aria-hidden="true" size={25} strokeWidth={1.4} />
              <div>
                <h2>{t.call}</h2>
                {settings.contact_numbers.map((n) => (
                  <a key={n.phone} href={phoneUrl(n.phone)} dir="ltr">
                    {n.phone}
                  </a>
                ))}
              </div>
            </div>
          )}
          {settings.email && (
            <div className="contact-item" data-scroll-reveal>
              <Mail aria-hidden="true" size={25} strokeWidth={1.4} />
              <div>
                <h2>{t.email}</h2>
                <a href={`mailto:${settings.email}`}>{settings.email}</a>
              </div>
            </div>
          )}
          {settings.address && (
            <div className="contact-item" data-scroll-reveal>
              <MapPin aria-hidden="true" size={25} strokeWidth={1.4} />
              <div>
                <h2>{t.visit}</h2>
                <p>{settings.address}</p>
                {map && (
                  <a
                    className="text-link"
                    href={map}
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    {t.map}
                    <ArrowUpRight size={18} aria-hidden="true" />
                  </a>
                )}
              </div>
            </div>
          )}
          {settings.opening_hours && (
            <div className="contact-item" data-scroll-reveal>
              <Clock aria-hidden="true" size={25} strokeWidth={1.4} />
              <div>
                <h2>{t.hours}</h2>
                <p className="prose-text">{settings.opening_hours}</p>
              </div>
            </div>
          )}
          {!settings.contact_numbers.length &&
            !settings.email &&
            !settings.address && <p>{t.contactPending}</p>}
          <div className="contact-social">
            {settings.whatsapp_number && (
              <a
                className="button"
                href={whatsappUrl(settings.whatsapp_number)}
                target="_blank"
                rel="noopener noreferrer"
              >
                {t.whatsapp}
                <ArrowUpRight size={18} aria-hidden="true" />
              </a>
            )}
            {facebook && (
              <a
                className="text-link"
                href={facebook}
                target="_blank"
                rel="noopener noreferrer"
              >
                {t.facebook}
                <ArrowUpRight size={18} aria-hidden="true" />
              </a>
            )}
          </div>
          {page?.body && <ContentText text={page.body} />}
        </div>
        <div>
          {hero && (
            <ContentPhoto
              locale={locale}
              media={hero}
              className="contact-hero"
            />
          )}
          <div className="location-card" data-scroll-reveal>
            <Wheat size={90} strokeWidth={0.8} aria-hidden="true" />
            <p className="eyebrow">{t.aboutLocation}</p>
            <h2>{settings.brand_name}</h2>
            {settings.address && <p>{settings.address}</p>}
            {map && (
              <a
                className="button button-light"
                href={map}
                target="_blank"
                rel="noopener noreferrer"
              >
                {t.map}
                <ArrowUpRight size={18} aria-hidden="true" />
              </a>
            )}
            <span className="location-rings" aria-hidden="true" />
          </div>
        </div>
      </section>
      <PageGallery
        locale={locale}
        media={page?.media ?? []}
        primaryId={hero?.id}
      />
    </>
  );
}
