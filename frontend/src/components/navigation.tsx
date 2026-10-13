"use client";
import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { ArrowUpRight, Globe2, Menu } from "lucide-react";
import { dictionary, switchLocalePath, type Locale } from "@/i18n/config";
export function Navigation({ locale }: { locale: Locale }) {
  const router = useRouter();
  const path = usePathname(),
    t = dictionary(locale);
  const links = [
    { href: `/${locale}`, label: t.home },
    { href: `/${locale}/about`, label: t.about },
    { href: `/${locale}/products`, label: t.products },
  ];
  const target = locale === "ar" ? "en" : "ar";
  function nav(className: string) {
    return (
      <nav className={className} aria-label={t.menu}>
        {links.map((link) => (
          <Link
            key={link.href}
            href={link.href}
            aria-current={
              (
                link.href === `/${locale}`
                  ? path === link.href
                  : path.startsWith(link.href)
              )
                ? "page"
                : undefined
            }
          >
            {link.label}
          </Link>
        ))}
      </nav>
    );
  }
  return (
    <>
      {nav("desktop-nav")}
      <div className="header-actions">
        <a
          className="language-link"
          href={switchLocalePath(path, target)}
          lang={target}
          hrefLang={target}
          onClick={(event) => {
            event.preventDefault();
            router.push(
              `${switchLocalePath(path, target)}${window.location.search}${window.location.hash}`,
            );
          }}
        >
          <Globe2 size={16} aria-hidden="true" />
          <span>{t.language}</span>
        </a>
        <Link
          className="button button-small header-contact"
          href={`/${locale}/contact`}
        >
          {t.contact}
          <ArrowUpRight
            className="direction-icon"
            size={17}
            aria-hidden="true"
          />
        </Link>
        <details className="mobile-menu">
          <summary aria-label={t.menu}>
            <Menu size={24} aria-hidden="true" />
          </summary>
          <div
            className="mobile-menu-panel"
            onClick={(event) => {
              if ((event.target as HTMLElement).closest("a"))
                event.currentTarget.closest("details")?.removeAttribute("open");
            }}
          >
            {nav("mobile-nav")}
            <Link className="button" href={`/${locale}/contact`}>
              {t.contact}
            </Link>
          </div>
        </details>
      </div>
    </>
  );
}
