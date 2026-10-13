import type { CatalogQuery } from "../types";
export type SearchParams = Record<string, string | string[] | undefined>;
export function catalogQuery(params: SearchParams): CatalogQuery {
  const page = Number(params.page);
  const sort =
    typeof params.sort === "string" &&
    ["sort_order", "name", "-name", "newest"].includes(params.sort)
      ? (params.sort as CatalogQuery["sort"])
      : "sort_order";
  return {
    search:
      typeof params.search === "string"
        ? params.search.trim().slice(0, 100)
        : undefined,
    sort,
    page: Number.isInteger(page) && page > 0 && page <= 10000 ? page : 1,
    per_page: 12,
  };
}
export function catalogPageUrl(
  locale: string,
  query: CatalogQuery,
  page: number,
): string {
  const params = new URLSearchParams({
    page: String(page),
    sort: query.sort ?? "sort_order",
  });
  if (query.search) params.set("search", query.search);
  return `/${locale}/products?${params}`;
}
