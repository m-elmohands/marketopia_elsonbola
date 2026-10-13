import { afterEach, beforeEach, describe, expect, it, vi } from "vitest";
import { getProduct } from "../services/catalog";
import { demoProducts } from "@/lib/demo";

beforeEach(() => {
  vi.stubEnv("WEBSITE_DEMO_MODE", "false");
  vi.stubEnv("API_BASE_URL", "http://example.test/api/v1");
});
afterEach(() => {
  vi.unstubAllGlobals();
  vi.unstubAllEnvs();
});

describe("product detail API lookup", () => {
  it("loads an ID through the centralized client with the requested locale", async () => {
    const product = { ...demoProducts("en")[0], id: 2 };
    const fetcher = vi.fn().mockResolvedValue(
      Response.json({
        data: product,
        message: "Success",
        errors: null,
        meta: {},
      }),
    );
    vi.stubGlobal("fetch", fetcher);
    expect(await getProduct("en", 2)).toEqual(product);
    const url = fetcher.mock.calls[0][0] as URL;
    expect(url.pathname).toBe("/api/v1/products/details");
    expect(url.searchParams.get("id")).toBe("2");
    expect(url.searchParams.get("locale")).toBe("en");
    expect(fetcher).toHaveBeenCalledTimes(1);
  });
  it("returns null for missing or unpublished IDs without retrying", async () => {
    const fetcher = vi
      .fn()
      .mockResolvedValue(new Response(null, { status: 404 }));
    vi.stubGlobal("fetch", fetcher);
    expect(await getProduct("ar", 999999)).toBeNull();
    expect(fetcher).toHaveBeenCalledTimes(1);
  });
});
