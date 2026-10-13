import Link from "next/link";
import { dictionary, formatWeight, type Locale } from "@/i18n/config";
import type { Product } from "../types";

export function ProductInquiry({
  locale,
  product,
  packId,
}: {
  locale: Locale;
  product: Product;
  packId?: string;
}) {
  const t = dictionary(locale);
  const pack =
    product.variants.find((variant) => String(variant.id) === packId) ??
    product.variants[0];
  const href = `/${locale}/products/details?id=${product.id}${pack ? `&pack=${pack.id}` : ""}`;
  return (
    <aside className="inquiry-context">
      <p className="eyebrow">{t.inquiry}</p>
      <p>
        {product.name}
        {pack && (
          <>
            {" "}
            · {formatWeight(pack.weight_kg, locale)} {t.kg}
          </>
        )}
      </p>
      <Link className="text-link" href={href}>
        {t.viewProduct}
      </Link>
    </aside>
  );
}
