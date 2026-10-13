import Image from "next/image";
import { dictionary, type Locale } from "@/i18n/config";
import type { Media } from "@/features/catalog/types";
export function ContentPhoto({
  locale,
  media,
  className = "",
  preload = false,
}: {
  locale: Locale;
  media?: Media;
  className?: string;
  preload?: boolean;
}) {
  const t = dictionary(locale);
  return (
    <div className={`content-photo ${className}`}>
      <Image
        src={media?.web_url ?? "/images/rice-still-life.png"}
        alt={media?.alt || t.photoAlt}
        fill
        sizes="(max-width: 767px) 100vw, 55vw"
        preload={preload}
        className="object-cover"
      />
    </div>
  );
}
