import { safeExternalUrl } from "@/lib/links";
import type { BusinessSettings } from "../types";

type Coordinates = Pick<BusinessSettings, "latitude" | "longitude">;
export function coordinates(
  settings: Coordinates,
): { latitude: number; longitude: number } | null {
  if (!settings.latitude?.trim() || !settings.longitude?.trim()) return null;
  const latitude = Number(settings.latitude),
    longitude = Number(settings.longitude);
  return Number.isFinite(latitude) &&
    Number.isFinite(longitude) &&
    Math.abs(latitude) <= 90 &&
    Math.abs(longitude) <= 180
    ? { latitude, longitude }
    : null;
}
export function mapUrl(
  settings: Coordinates & Pick<BusinessSettings, "map_url">,
): string | undefined {
  const configured = safeExternalUrl(settings.map_url);
  if (configured) return configured;
  const point = coordinates(settings);
  return point
    ? `https://www.google.com/maps/search/?api=1&query=${point.latitude},${point.longitude}`
    : undefined;
}
