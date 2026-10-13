import { ContentPhoto } from "@/components/content-photo";
import type { Locale } from "@/i18n/config";
import type { Media } from "@/features/catalog/types";

export function PageGallery({
  locale,
  media,
  primaryId,
}: {
  locale: Locale;
  media: Media[];
  primaryId?: number;
}) {
  const images = media.filter(
    (image) => image.collection === "gallery" && image.id !== primaryId,
  );
  if (!images.length) return null;
  return (
    <div className="container page-gallery">
      {images.map((image) => (
        <ContentPhoto key={image.id} locale={locale} media={image} />
      ))}
    </div>
  );
}
