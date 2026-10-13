import Image from "next/image";
import Link from "next/link";
import { ArrowUpRight, Wheat } from "lucide-react";
import { dictionary, formatWeight, type Locale } from "@/i18n/config";
import type { Product, ProductVariant } from "../types";
export function ProductCard({
  product,
  locale,
  variant,
  index = 0,
}: {
  product: Product;
  locale: Locale;
  variant?: ProductVariant;
  index?: number;
}) {
  const t = dictionary(locale),
    media =
      variant?.media[0] ??
      product.media.find((m) => m.collection === "cover") ??
      product.media[0];
  const href = `/${locale}/products/details?id=${product.id}${variant ? `&pack=${variant.id}` : ""}`;
  return (
    <article
      className="product-card"
      data-scroll-reveal
      data-scroll-delay={index % 3}
    >
      <Link
        className={`product-card-image tone-${index % 3}`}
        href={href}
        aria-label={`${t.viewProduct}: ${product.name}${variant ? `, ${formatWeight(variant.weight_kg, locale)} ${t.kg}` : ""}`}
      >
        {media ? (
          <Image
            src={media.web_url}
            alt={media.alt || product.name}
            fill
            sizes="(max-width: 767px) 90vw, (max-width: 1100px) 45vw, 30vw"
            className="object-contain"
          />
        ) : (
          <div className="image-placeholder">
            <Wheat size={50} strokeWidth={1} aria-hidden="true" />
            <span>{t.imageMissing}</span>
          </div>
        )}
        <span className="image-arrow">
          <ArrowUpRight
            className="direction-icon"
            aria-hidden="true"
            size={22}
          />
        </span>
        {variant && (
          <span className="pack-badge">
            {formatWeight(variant.weight_kg, locale)} {t.kg}
          </span>
        )}
      </Link>
      <div className="product-card-body">
        <p className="eyebrow">{variant ? t.packLabel : t.grain}</p>
        <h3>
          <Link href={href}>{product.name}</Link>
        </h3>
        {variant ? <p>{t.packCaption}</p> : <p>{product.short_description}</p>}
        <div className="weight-chips" aria-label={t.sizes}>
          {(variant ? [variant] : product.variants).map((pack) => (
            <span key={pack.id}>
              {formatWeight(pack.weight_kg, locale)} {t.kg}
            </span>
          ))}
        </div>
      </div>
    </article>
  );
}
