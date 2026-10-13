import "../globals.css";
import brand from "@/config/brand.json";
import { notFound } from "next/navigation";
import type { Metadata } from "next";
import { dictionary, isLocale } from "@/i18n/config";
import { getSettings } from "@/features/settings/services/settings";
import { ScrollReveal } from "@/features/animation/components/scroll-reveal";
import { Header } from "@/components/header";
import { Footer } from "@/components/footer";
import { isDemoMode } from "@/lib/demo";
import { coordinates } from "@/features/settings/services/location";
import { safeExternalUrl } from "@/lib/links";
import { siteUrl } from "@/lib/seo";
export async function generateMetadata({
  params,
}: {
  params: Promise<{ locale: string }>;
}): Promise<Metadata> {
  const { locale } = await params;
  if (!isLocale(locale)) return {};
  const settings = await getSettings(locale);
  const logo = settings.media.find((image) => image.collection === "logo");
  return {
    metadataBase: new URL(siteUrl),
    icons: { icon: logo?.web_url || brand.logo },
  };
}
export default async function WebsiteLayout({
  children,
  params,
}: {
  children: React.ReactNode;
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  if (!isLocale(locale)) notFound();
  const t = dictionary(locale),
    settings = await getSettings(locale);
  const logo = settings.media.find((image) => image.collection === "logo");
  const point = coordinates(settings);
  const facebook = safeExternalUrl(settings.facebook_url);
  const structuredData = isDemoMode()
    ? null
    : {
        "@context": "https://schema.org",
        "@type": "Organization",
        name: settings.brand_name,
        url: `${siteUrl}/${locale}`,
        ...(logo ? { logo: logo.web_url } : {}),
        ...(settings.tagline ? { description: settings.tagline } : {}),
        ...(facebook ? { sameAs: [facebook] } : {}),
        ...(point
          ? {
              location: {
                "@type": "Place",
                geo: { "@type": "GeoCoordinates", ...point },
              },
            }
          : {}),
        ...(settings.email ? { email: settings.email } : {}),
        ...(settings.address
          ? {
              address: {
                "@type": "PostalAddress",
                streetAddress: settings.address,
              },
            }
          : {}),
        ...(settings.contact_numbers[0]
          ? {
              telephone:
                settings.contact_numbers.find((n) => n.is_primary)?.phone ??
                settings.contact_numbers[0].phone,
            }
          : {}),
      };
  return (
    <html lang={locale} dir={locale === "ar" ? "rtl" : "ltr"}>
      <body>
        <a className="skip-link" href="#main">
          {t.skip}
        </a>
        {isDemoMode() && <div className="demo-note">{t.demo}</div>}
        <Header locale={locale} name={settings.brand_name} logo={logo} />
        <main id="main" tabIndex={-1}>
          <ScrollReveal />
          {children}
        </main>
        <Footer locale={locale} settings={settings} />
        {structuredData && (
          <script
            type="application/ld+json"
            dangerouslySetInnerHTML={{
              __html: JSON.stringify(structuredData).replace(/</g, "\u003c"),
            }}
          />
        )}
      </body>
    </html>
  );
}
