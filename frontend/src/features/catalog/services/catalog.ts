import { cache } from "react";
import { apiGet, ApiError } from "@/lib/api/client";
import { demoCatalog, demoProducts, isDemoMode } from "@/lib/demo";
import type { Locale } from "@/i18n/config";
import type { CatalogQuery, Pagination, Product } from "../types";
export async function getProducts(locale: Locale, query: CatalogQuery = {}) {
  if (isDemoMode()) return demoCatalog(locale, query);
  const params: Record<string, string> = { locale };
  Object.entries(query).forEach(([key, value]) => {
    if (value !== undefined && value !== "")
      params[key] =
        typeof value === "boolean" ? (value ? "1" : "0") : String(value);
  });
  return apiGet<Product[], Pagination>("products", params);
}
export const getProduct = cache(
  async (locale: Locale, id: number): Promise<Product | null> => {
    if (isDemoMode())
      return demoProducts(locale).find((p) => p.id === id) ?? null;
    try {
      return (
        await apiGet<Product>("products/details", {
          locale,
          id: String(id),
        })
      ).data;
    } catch (error) {
      if (error instanceof ApiError && error.status === 404) return null;
      throw error;
    }
  },
);
