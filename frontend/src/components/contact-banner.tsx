import Link from "next/link";
import { ArrowUpRight, Wheat } from "lucide-react";
import { dictionary, type Locale } from "@/i18n/config";
export function ContactBanner({ locale }: { locale: Locale }) {
  const t = dictionary(locale);
  return (
    <section className="contact-banner">
      <div className="container contact-banner-inner" data-scroll-reveal>
        <div>
          <p className="eyebrow">{t.contactEyebrow}</p>
          <h2>
            {t.contactTitle}
            <br />
            <em>{t.contactAccent}</em>
          </h2>
          <p>{t.contactBody}</p>
        </div>
        <Link className="contact-circle" href={`/${locale}/contact`}>
          <ArrowUpRight
            className="direction-icon"
            aria-hidden="true"
            size={36}
          />
          <span>{t.contactLink}</span>
        </Link>
        <Wheat
          className="banner-wheat"
          size={210}
          strokeWidth={0.65}
          aria-hidden="true"
        />
      </div>
    </section>
  );
}
