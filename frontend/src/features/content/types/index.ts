import type { Media } from "@/features/catalog/types";
export interface ContentPage {
  key: string;
  title: string;
  intro: string | null;
  body: string | null;
  meta_title: string | null;
  meta_description: string | null;
  media: Media[];
}
