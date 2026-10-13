import Link from "next/link";
import { ArrowUpRight } from "lucide-react";
import { Brand } from "./brand";
import { dictionary, type Locale } from "@/i18n/config";
import type { BusinessSettings } from "@/features/settings/types";
import { phoneUrl, safeExternalUrl } from "@/lib/links";
export function Footer({
  locale,
  settings,
}: {
  locale: Locale;
  settings: BusinessSettings;
}) {
  const t = dictionary(locale),
    facebook = safeExternalUrl(settings.facebook_url);
  return (
    <footer className="site-footer">
      <div className="container footer-grid">
        <div>
          <Brand
            locale={locale}
            name={settings.brand_name}
            logo={settings.media.find((image) => image.collection === "logo")}
          />
          <p className="footer-description">
            {settings.tagline ?? t.footerNote}
          </p>
        </div>
        <nav aria-label={t.footerNav}>
          <span className="eyebrow">{t.footerNav}</span>
          <Link href={`/${locale}/about`}>{t.about}</Link>
          <Link href={`/${locale}/products`}>{t.products}</Link>
          <Link href={`/${locale}/contact`}>{t.contact}</Link>
        </nav>
        <div className="footer-contact">
          <span className="eyebrow">{t.footerContact}</span>
          {settings.address && <p>{settings.address}</p>}
          {settings.contact_numbers.map((n) => (
            <a dir="ltr" key={n.phone} href={phoneUrl(n.phone)}>
              {n.phone}
            </a>
          ))}
          {facebook && (
            <a href={facebook} target="_blank" rel="noopener noreferrer">
              {t.facebook}
              <ArrowUpRight size={16} aria-hidden="true" />
            </a>
          )}
        </div>
      </div>
      <div className="container footer-bottom">
        <span>
          © {new Date().getFullYear()} {settings.brand_name}. {t.copyright}
        </span>
        <span>{t.madeIn}</span>
      </div>
    </footer>
  );
}
