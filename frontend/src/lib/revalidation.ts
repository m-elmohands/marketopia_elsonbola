import { timingSafeEqual } from "node:crypto";
export function authorizedRevalidation(
  header: string | null,
  token: string | undefined,
): boolean {
  if (!token || !header?.startsWith("Bearer ")) return false;
  const received = Buffer.from(header.slice(7)),
    expected = Buffer.from(token);
  return (
    received.length === expected.length && timingSafeEqual(received, expected)
  );
}
export function validRevalidationPayload(payload: unknown): boolean {
  return (
    !!payload &&
    typeof payload === "object" &&
    "tags" in payload &&
    Array.isArray(payload.tags) &&
    payload.tags.length === 1 &&
    payload.tags[0] === "website-content"
  );
}
