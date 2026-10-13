import Link from "next/link";
import { notFound } from "next/navigation";
import { Search, ArrowLeft, ArrowRight } from "lucide-react";
import { dictionary, isLocale } from "@/i18n/config";
import { getSettings } from "@/features/settings/services/settings";
import { getProducts } from "@/features/catalog/services/catalog";
import {
  catalogQuery,
  catalogPageUrl,
  type SearchParams,
} from "@/features/catalog/services/query";
import { ProductCard } from "@/features/catalog/components/product-card";
import { EmptyState } from "@/components/empty-state";
import { ContactBanner } from "@/components/contact-banner";
import { pageMetadata } from "@/lib/seo";
export async function generateMetadata({
  params,
}: {
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  if (!isLocale(locale)) return {};
  const t = dictionary(locale);
  const settings = await getSettings(locale);
  return pageMetadata(
    locale,
    "/products",
    t.products,
    t.catalogIntro,
    settings.brand_name,
  );
}
export default async function Products({
  params,
  searchParams,
}: {
  params: Promise<{ locale: string }>;
  searchParams: Promise<SearchParams>;
}) {
  const { locale } = await params;
  if (!isLocale(locale)) notFound();
  const t = dictionary(locale),
    query = catalogQuery(await searchParams),
    { data, meta } = await getProducts(locale, query);
  return (
    <>
      <section className="container page-heading">
        <p className="eyebrow">{t.catalogEyebrow}</p>
        <h1>
          {t.catalogTitle}
          <br />
          <em>{t.catalogAccent}</em>
        </h1>
        <p>{t.catalogIntro}</p>
      </section>
      <section className="container catalog-section">
        <form
          method="GET"
          action={`/${locale}/products`}
          className="catalog-filters"
        >
          <div className="search-control">
            <label htmlFor="search">{t.search}</label>
            <div>
              <Search size={18} aria-hidden="true" />
              <input
                id="search"
                name="search"
                type="search"
                placeholder={t.searchPlaceholder}
                defaultValue={query.search}
                maxLength={100}
              />
            </div>
          </div>
          <div className="sort-control">
            <label htmlFor="sort">{t.sort}</label>
            <select id="sort" name="sort" defaultValue={query.sort}>
              <option value="sort_order">{t.sortDefault}</option>
              <option value="name">{t.sortName}</option>
              <option value="-name">{t.sortNameDesc}</option>
              <option value="newest">{t.sortNewest}</option>
            </select>
          </div>
          <button type="submit" className="button">
            {t.searchButton}
          </button>
          {query.search && (
            <Link className="text-link" href={`/${locale}/products`}>
              {t.clear}
            </Link>
          )}
        </form>
        <p className="results-count">
          {new Intl.NumberFormat(locale).format(meta.total)} {t.results}
        </p>
        {data.length ? (
          <div className="product-grid">
            {data.map((product, index) => (
              <ProductCard
                locale={locale}
                product={product}
                key={product.id}
                index={index}
              />
            ))}
          </div>
        ) : (
          <EmptyState
            locale={locale}
            filtered={Boolean(query.search) || (query.page ?? 1) > 1}
          />
        )}
        <nav className="pagination" aria-label={t.page}>
          {meta.current_page > 1 && (
            <Link
              className="text-link"
              href={catalogPageUrl(locale, query, meta.current_page - 1)}
            >
              <ArrowLeft
                className="direction-icon"
                size={18}
                aria-hidden="true"
              />
              {t.prev}
            </Link>
          )}
          <span>
            {t.page} {meta.current_page} {t.of} {meta.last_page}
          </span>
          {meta.current_page < meta.last_page && (
            <Link
              className="text-link"
              href={catalogPageUrl(locale, query, meta.current_page + 1)}
            >
              {t.next}
              <ArrowRight
                className="direction-icon"
                size={18}
                aria-hidden="true"
              />
            </Link>
          )}
        </nav>
      </section>
      <ContactBanner locale={locale} />
    </>
  );
}
