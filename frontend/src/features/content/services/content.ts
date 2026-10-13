import { cache } from "react";
import { apiGet, ApiError } from "@/lib/api/client";
import { isDemoMode } from "@/lib/demo";
import type { Locale } from "@/i18n/config";
import type { ContentPage } from "../types";
export const getPage = cache(
  async (
    locale: Locale,
    key: "home" | "about" | "contact",
  ): Promise<ContentPage | null> => {
    if (isDemoMode()) return null;
    try {
      return (await apiGet<ContentPage>(`pages/${key}`, { locale })).data;
    } catch (error) {
      if (error instanceof ApiError && error.status === 404) return null;
      throw error;
    }
  },
);
