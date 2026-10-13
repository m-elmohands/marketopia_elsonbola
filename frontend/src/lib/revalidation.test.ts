import { describe, expect, it } from "vitest";
import {
  authorizedRevalidation,
  validRevalidationPayload,
} from "./revalidation";
import { safeExternalUrl, whatsappUrl, phoneUrl } from "./links";
describe("content revalidation", () => {
  it("requires a configured matching bearer token", () => {
    expect(authorizedRevalidation("Bearer secret", "secret")).toBe(true);
    expect(authorizedRevalidation("Bearer wrong", "secret")).toBe(false);
    expect(authorizedRevalidation("Bearer secret", undefined)).toBe(false);
    expect(authorizedRevalidation(null, "secret")).toBe(false);
  });
  it("limits invalidation to the Laravel website-content contract", () => {
    expect(validRevalidationPayload({ tags: ["website-content"] })).toBe(true);
    expect(validRevalidationPayload({ tags: ["other"] })).toBe(false);
    expect(
      validRevalidationPayload({ tags: ["website-content", "admin"] }),
    ).toBe(false);
    expect(validRevalidationPayload(null)).toBe(false);
  });
});
describe("contact links", () => {
  it("rejects executable and malformed external URLs", () => {
    expect(safeExternalUrl("javascript:alert(1)")).toBeUndefined();
    expect(safeExternalUrl("/path")).toBeUndefined();
    expect(safeExternalUrl("https://example.test/map")).toBe(
      "https://example.test/map",
    );
  });
  it("normalizes numbers and encodes WhatsApp messages", () => {
    expect(phoneUrl("+20 (100) 526-3014")).toBe("tel:+201005263014");
    expect(whatsappUrl("+20 100", "Hello & rice")).toBe(
      "https://wa.me/20100?text=Hello%20%26%20rice",
    );
  });
});
