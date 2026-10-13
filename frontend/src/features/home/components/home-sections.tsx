import Link from "next/link";
import { ContentText } from "@/features/content/components/content-text";
import { ArrowUpRight, Wheat, Package, Utensils } from "lucide-react";
import { dictionary, type Locale } from "@/i18n/config";
import type { Product } from "@/features/catalog/types";
import type { ContentPage } from "@/features/content/types";
import type { BusinessSettings } from "@/features/settings/types";
import { SectionHeading } from "@/components/section-heading";
import { ProductCard } from "@/features/catalog/components/product-card";
import { EmptyState } from "@/components/empty-state";
import { ContentPhoto } from "@/components/content-photo";
export function OriginStrip({ locale }: { locale: Locale }) {
  const t = dictionary(locale);
  return (
    <div className="origin-strip">
      <div className="container">
        {[
          { Icon: Wheat, label: t.grain },
          { Icon: Package, label: t.sizes },
          { Icon: Utensils, label: t.table },
        ].map(({ Icon, label }) => (
          <span key={label}>
            <Icon size={23} strokeWidth={1.2} aria-hidden="true" />
            {label}
          </span>
        ))}
      </div>
    </div>
  );
}
export function StoryPreview({
  locale,
  page,
}: {
  locale: Locale;
  page: ContentPage | null;
}) {
  const t = dictionary(locale);
  return (
    <section className="container story-preview" data-scroll-reveal>
      <SectionHeading
        eyebrow={t.storyEyebrow}
        title={page?.title || t.storyTitle}
        accent={page?.title ? undefined : t.storyAccent}
      />
      <div className="story-copy">
        <ContentText text={page?.intro || t.storyBody} />
        <Link className="text-link" href={`/${locale}/about`}>
          {t.storyLink}
          <ArrowUpRight
            className="direction-icon"
            size={20}
            aria-hidden="true"
          />
        </Link>
      </div>
      <Wheat
        className="story-wheat"
        size={120}
        strokeWidth={0.7}
        aria-hidden="true"
      />
    </section>
  );
}
export function Collection({
  locale,
  products,
}: {
  locale: Locale;
  products: Product[];
}) {
  const t = dictionary(locale);
  const cards =
    products.length === 1
      ? products[0].variants
          .slice(0, 3)
          .map((variant) => ({ product: products[0], variant }))
      : products
          .slice(0, 3)
          .map((product) => ({ product, variant: undefined }));
  return (
    <section id="collection" className="collection">
      <div className="container">
        <div className="section-top" data-scroll-reveal>
          <SectionHeading
            eyebrow={t.collectionEyebrow}
            title={t.collectionTitle}
            accent={t.collectionAccent}
          />
          <div>
            <p>{t.collectionBody}</p>
            <Link className="text-link" href={`/${locale}/products`}>
              {t.viewAll}
              <ArrowUpRight
                className="direction-icon"
                size={20}
                aria-hidden="true"
              />
            </Link>
          </div>
        </div>
        {cards.length ? (
          <div className="product-grid">
            {cards.map(({ product, variant }, index) => (
              <ProductCard
                key={`${product.id}-${variant?.id}`}
                locale={locale}
                product={product}
                variant={variant}
                index={index}
              />
            ))}
          </div>
        ) : (
          <EmptyState locale={locale} />
        )}
      </div>
    </section>
  );
}
export function TableStory({
  locale,
  settings,
}: {
  locale: Locale;
  settings: BusinessSettings;
}) {
  const t = dictionary(locale);
  const points = settings.quality_highlights.length
    ? settings.quality_highlights
    : [t.qualityItem1, t.qualityItem2, t.qualityItem3];
  return (
    <section className="table-story">
      <div className="table-story-visual" data-scroll-reveal>
        <ContentPhoto locale={locale} />
        <span className="table-photo-label">{t.table}</span>
      </div>
      <div className="table-story-copy" data-scroll-reveal>
        <SectionHeading
          eyebrow={t.qualityEyebrow}
          title={t.qualityTitle}
          accent={t.qualityAccent}
        />
        <p>{t.qualityBody}</p>
        <ol>
          {points.map((point, index) => (
            <li key={point}>
              <span>{String(index + 1).padStart(2, "0")}</span>
              {point}
            </li>
          ))}
        </ol>
        <Link className="button button-light" href={`/${locale}/products`}>
          {t.qualityLink}
          <ArrowUpRight
            className="direction-icon"
            size={20}
            aria-hidden="true"
          />
        </Link>
      </div>
    </section>
  );
}
