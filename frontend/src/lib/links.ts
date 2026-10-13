export function safeExternalUrl(
  value: string | null | undefined,
): string | undefined {
  if (!value) return undefined;
  try {
    const url = new URL(value);
    return ["http:", "https:"].includes(url.protocol) ? url.href : undefined;
  } catch {
    return undefined;
  }
}
export function phoneUrl(phone: string): string {
  return `tel:${phone.replace(/[^+0-9]/g, "")}`;
}
export function whatsappUrl(phone: string, message?: string): string {
  return `https://wa.me/${phone.replace(/[^0-9]/g, "")}${message ? `?text=${encodeURIComponent(message)}` : ""}`;
}
