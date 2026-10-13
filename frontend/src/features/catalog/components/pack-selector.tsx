"use client";
import Image from "next/image";
import { ContentText } from "@/features/content/components/content-text";
import { useState } from "react";
import { ArrowUpRight, Wheat } from "lucide-react";
import { dictionary, formatWeight, type Locale } from "@/i18n/config";
import type { Product } from "../types";
import type { BusinessSettings } from "@/features/settings/types";
import { whatsappUrl } from "@/lib/links";
export function PackSelector({
  product,
  locale,
  initialPack,
  settings,
}: {
  product: Product;
  locale: Locale;
  initialPack?: string;
  settings: Pick<BusinessSettings, "whatsapp_number">;
}) {
  const t = dictionary(locale);
  const [packId, setPackId] = useState(
    product.variants.find((v) => String(v.id) === initialPack)?.id ??
      product.variants[0]?.id,
  );
  const [galleryId, setGalleryId] = useState<number | null>(null);
  const pack = product.variants.find((v) => v.id === packId);
  const media =
    galleryId !== null
      ? product.media.find((m) => m.id === galleryId)
      : (pack?.media[0] ??
        product.media.find((m) => m.collection === "cover") ??
        product.media[0]);
  const weight = pack ? `${formatWeight(pack.weight_kg, locale)} ${t.kg}` : "";
  const href = settings.whatsapp_number
    ? whatsappUrl(
        settings.whatsapp_number,
        `${t.packInquiry} ${product.name} (${weight})`,
      )
    : `/${locale}/contact?product=${product.id}${pack ? `&pack=${pack.id}` : ""}`;
  function selectPack(id: number) {
    setPackId(id);
    setGalleryId(null);
    const url = new URL(window.location.href);
    url.searchParams.set("pack", String(id));
    window.history.replaceState(null, "", url);
  }
  return (
    <div className="product-detail-grid">
      <div>
        <div className="detail-image">
          {media ? (
            <Image
              key={media.id}
              src={media.web_url}
              alt={media.alt || product.name}
              fill
              sizes="(max-width: 767px) 90vw, 50vw"
              className="object-contain"
              preload
            />
          ) : (
            <div className="image-placeholder">
              <Wheat size={60} aria-hidden="true" />
              <p>{t.imageMissing}</p>
            </div>
          )}
          <span className="detail-weight">{weight}</span>
        </div>
        {product.media.length > 1 && (
          <div className="gallery-thumbnails" aria-label={t.gallery}>
            {product.media.map((image) => (
              <button
                type="button"
                key={image.id}
                aria-label={image.alt || product.name}
                aria-pressed={media?.id === image.id}
                onClick={() => setGalleryId(image.id)}
              >
                <Image
                  src={image.thumbnail_url}
                  alt=""
                  width={80}
                  height={80}
                />
              </button>
            ))}
          </div>
        )}
      </div>
      <div className="detail-copy">
        <p className="eyebrow">{t.detailEyebrow}</p>
        <h1>{product.name}</h1>
        <p className="detail-summary">
          {product.short_description || t.detailBody}
        </p>
        <fieldset className="pack-selector">
          <legend>{t.choosePack}</legend>
          <div>
            {product.variants.map((variant) => (
              <label
                className={variant.id === packId ? "selected" : ""}
                key={variant.id}
              >
                <input
                  type="radio"
                  name="pack"
                  value={variant.id}
                  checked={variant.id === packId}
                  onChange={() => selectPack(variant.id)}
                />
                <span>
                  {formatWeight(variant.weight_kg, locale)} {t.kg}
                </span>
              </label>
            ))}
          </div>
        </fieldset>
        <p className="selected-pack" role="status" aria-live="polite">
          {t.selectedPack}: <strong>{weight}</strong>
        </p>
        <a
          className="button"
          href={href}
          {...(settings.whatsapp_number
            ? { target: "_blank", rel: "noopener noreferrer" }
            : {})}
        >
          {t.inquiry}
          <ArrowUpRight
            className="direction-icon"
            size={20}
            aria-hidden="true"
          />
        </a>
        <div className="product-description">
          <h2>{t.aboutProduct}</h2>
          <ContentText text={product.description || t.detailBody} />
        </div>
      </div>
    </div>
  );
}
