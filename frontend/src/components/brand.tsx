import Image from "next/image";
import brand from "@/config/brand.json";
import Link from "next/link";
import type { Media } from "@/features/catalog/types";
import { dictionary, type Locale } from "@/i18n/config";
export function Brand({
  locale,
  name,
  logo,
}: {
  locale: Locale;
  name?: string;
  logo?: Media;
}) {
  const t = dictionary(locale);
  return (
    <Link className="brand" href={`/${locale}`} aria-label={name ?? t.brand}>
      <Image
        src={logo?.web_url || brand.logo}
        alt=""
        width={64}
        height={64}
        className="brand-logo"
      />
      <span>
        <span className="brand-name">{name ?? t.brand}</span>
        <span className="brand-subtitle">{t.brandCategory}</span>
      </span>
    </Link>
  );
}
