import Link from "next/link";
import Image from "next/image";
import { ArrowDown, ArrowUpRight, Wheat } from "lucide-react";
import { dictionary, type Locale } from "@/i18n/config";
import type { ContentPage } from "@/features/content/types";
import type { BusinessSettings } from "@/features/settings/types";
import { ContentPhoto } from "@/components/content-photo";
export function Hero({
  locale,
  page,
  settings,
  demo,
}: {
  locale: Locale;
  page: ContentPage | null;
  settings: BusinessSettings;
  demo: boolean;
}) {
  const t = dictionary(locale),
    media = page?.media.find((m) => m.collection === "hero") ?? page?.media[0];
  return (
    <section className="hero container">
      <div className="hero-copy">
        <p className="eyebrow">
          <span className="small-grain" />
          {settings.tagline || t.origin}
        </p>
        <h1>
          {page?.title ? (
            page.title
          ) : (
            <>
              {t.heroLine1}
              <br />
              {t.heroLine2}
              <br />
              <em>{t.heroLine3}</em>
            </>
          )}
        </h1>
        <p className="hero-description">
          {page?.intro || settings.tagline || t.heroBody}
        </p>
        <div className="hero-actions">
          <Link className="button" href={`/${locale}/products`}>
            {t.explore}
            <ArrowUpRight
              className="direction-icon"
              size={20}
              aria-hidden="true"
            />
          </Link>
          <Link className="text-link" href={`/${locale}/about`}>
            {t.heroSecondary}
          </Link>
        </div>
        <a className="scroll-note" href="#collection">
          <span>
            <ArrowDown size={17} aria-hidden="true" />
          </span>
          {t.scroll}
        </a>
      </div>
      <div className="hero-visual">
        <ContentPhoto
          locale={locale}
          media={media}
          preload
          className="hero-photo"
        />
        <div className="grain-seal">
          <Wheat size={28} strokeWidth={1} aria-hidden="true" />
          <span>{t.grain}</span>
          <span className="seal-dots" aria-hidden="true">
            • • •
          </span>
        </div>
        <div className="photo-note">
          <span className="eyebrow">{settings.address || t.heroNote}</span>
          <p>{t.heroCaption}</p>
        </div>
        {demo && (
          <div className="hero-packs">
            <Image
              src="/images/pack-collection.png"
              alt={
                locale === "ar"
                  ? "عبوات السنبلة الذهبية"
                  : "Golden Spike rice packaging"
              }
              width={640}
              height={640}
              sizes="(max-width: 767px) 50vw, 23vw"
            />
          </div>
        )}
      </div>
    </section>
  );
}
