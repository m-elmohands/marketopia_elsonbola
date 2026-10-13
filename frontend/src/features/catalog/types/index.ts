export interface Media {
  id: number;
  collection: string;
  url: string;
  web_url: string;
  thumbnail_url: string;
  alt: string;
  locale: string | null;
}
export interface ProductVariant {
  id: number;
  weight_kg: string;
  media: Media[];
}
export interface Product {
  id: number;
  slug: string;
  name: string;
  short_description: string | null;
  description: string | null;
  meta_title: string | null;
  meta_description: string | null;
  is_featured: boolean;
  variants: ProductVariant[];
  media: Media[];
}
export interface Pagination {
  current_page: number;
  per_page: number;
  total: number;
  last_page: number;
}
export interface CatalogQuery {
  search?: string;
  sort?: "sort_order" | "name" | "-name" | "newest";
  page?: number;
  featured?: boolean;
  per_page?: number;
}
