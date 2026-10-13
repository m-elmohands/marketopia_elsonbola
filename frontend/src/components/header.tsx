import type { Media } from "@/features/catalog/types";
import { Brand } from "./brand";
import { Navigation } from "./navigation";
import type { Locale } from "@/i18n/config";
export function Header({
  locale,
  name,
  logo,
}: {
  locale: Locale;
  name: string;
  logo?: Media;
}) {
  return (
    <header className="site-header">
      <div className="container header-inner">
        <Brand locale={locale} name={name} logo={logo} />
        <Navigation locale={locale} />
      </div>
    </header>
  );
}
