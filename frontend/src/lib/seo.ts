import type { Metadata } from "next";
import { dictionary, type Locale } from "@/i18n/config";
import { isDemoMode } from "./demo";
export const siteUrl = process.env.SITE_URL ?? "http://localhost:3000";
export function pageMetadata(
  locale: Locale,
  path: string,
  title: string,
  description: string,
  brandName?: string,
): Metadata {
  const canonical = `${siteUrl}/${locale}${path}`;
  return {
    title: `${title} | ${brandName || dictionary(locale).brand}`,
    description,
    alternates: {
      canonical,
      languages: {
        ar: `${siteUrl}/ar${path}`,
        en: `${siteUrl}/en${path}`,
        "x-default": `${siteUrl}/ar${path}`,
      },
    },
    robots: isDemoMode()
      ? { index: false, follow: false }
      : { index: true, follow: true },
    openGraph: {
      title,
      description,
      url: canonical,
      siteName: brandName || dictionary(locale).brand,
      locale: locale === "ar" ? "ar_EG" : "en_US",
      type: "website",
    },
  };
}
