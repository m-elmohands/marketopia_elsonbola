import Link from "next/link";
import { Wheat } from "lucide-react";
import { dictionary, type Locale } from "@/i18n/config";
export function EmptyState({
  locale,
  filtered = false,
}: {
  locale: Locale;
  filtered?: boolean;
}) {
  const t = dictionary(locale);
  return (
    <div className="empty-state">
      <Wheat size={44} strokeWidth={1} aria-hidden="true" />
      <h2>{t.emptyTitle}</h2>
      <p>{t.emptyBody}</p>
      {filtered && (
        <Link className="text-link" href={`/${locale}/products`}>
          {t.clear}
        </Link>
      )}
    </div>
  );
}
