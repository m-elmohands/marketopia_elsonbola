import type { Media } from "@/features/catalog/types";
export interface BusinessSettings {
  brand_name: string;
  tagline: string | null;
  address: string | null;
  opening_hours: string | null;
  quality_highlights: string[];
  email: string | null;
  facebook_url: string | null;
  whatsapp_number: string | null;
  map_url: string | null;
  latitude: string | null;
  longitude: string | null;
  contact_numbers: { phone: string; is_primary: boolean }[];
  media: Media[];
}
