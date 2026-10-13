import en from "./en.json";
import ar from "./ar.json";
export const locales = ["ar", "en"] as const;
export type Locale = (typeof locales)[number];
export type Dictionary = typeof en;
const dictionaries: Record<Locale, Dictionary> = { ar, en };
export function isLocale(value: string): value is Locale {
  return value === "ar" || value === "en";
}
export function dictionary(locale: Locale): Dictionary {
  return dictionaries[locale];
}
export function switchLocalePath(path: string, locale: Locale): string {
  return path.replace(/^\/(ar|en)(?=\/|$)/, `/${locale}`);
}
export function formatWeight(weight: string, locale: Locale): string {
  return new Intl.NumberFormat(locale === "ar" ? "ar-EG" : "en", {
    maximumFractionDigits: 2,
  }).format(Number(weight));
}
