import { cache } from "react";
import { apiGet, ApiError } from "@/lib/api/client";
import { demoSettings, isDemoMode } from "@/lib/demo";
import { dictionary, type Locale } from "@/i18n/config";
import type { BusinessSettings } from "../types";
export const getSettings = cache(
  async (locale: Locale): Promise<BusinessSettings> => {
    if (isDemoMode()) return demoSettings(locale);
    try {
      return (await apiGet<BusinessSettings>("settings", { locale })).data;
    } catch (error) {
      if (!(error instanceof ApiError) || error.status !== 404) throw error;
      return {
        brand_name: dictionary(locale).brand,
        tagline: null,
        address: null,
        opening_hours: null,
        quality_highlights: [],
        email: null,
        facebook_url: null,
        whatsapp_number: null,
        map_url: null,
        latitude: null,
        longitude: null,
        contact_numbers: [],
        media: [],
      };
    }
  },
);
