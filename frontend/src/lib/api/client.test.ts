import { afterEach, describe, expect, it, vi } from "vitest";
import { apiGet, ApiError } from "./client";
afterEach(() => vi.unstubAllEnvs());
function response(status = 200) {
  return new Response(
    JSON.stringify({
      data: [],
      message: "Success",
      errors: null,
      meta: { total: 0 },
    }),
    { status },
  );
}
describe("public API client", () => {
  it("encodes filters and uses a bounded timeout and shared revalidation tag", async () => {
    vi.stubEnv("API_BASE_URL", "https://api.example.test/api/v1/");
    const fetcher = vi.fn<typeof fetch>().mockResolvedValue(response());
    const result = await apiGet(
      "products",
      { locale: "ar", search: "rice & grains" },
      { fetcher },
    );
    const [url, options] = fetcher.mock.calls[0];
    expect(String(url)).toContain(
      "/api/v1/products?locale=ar&search=rice+%26+grains",
    );
    expect(options?.next).toEqual({
      revalidate: 300,
      tags: ["website-content"],
    });
    expect(options?.signal).toBeInstanceOf(AbortSignal);
    expect(result.data).toEqual([]);
  });
  it("retries temporary server failures once", async () => {
    const fetcher = vi
      .fn<typeof fetch>()
      .mockResolvedValueOnce(response(503))
      .mockResolvedValue(response());
    await apiGet("products", { locale: "en" }, { fetcher });
    expect(fetcher).toHaveBeenCalledTimes(2);
  });
  it("does not retry missing or forbidden records", async () => {
    const fetcher = vi.fn<typeof fetch>().mockResolvedValue(response(404));
    await expect(
      apiGet("products/missing", {}, { fetcher }),
    ).rejects.toMatchObject({ status: 404 });
    expect(fetcher).toHaveBeenCalledTimes(1);
  });
  it("reports exhausted network failures without serving fake content", async () => {
    const fetcher = vi
      .fn<typeof fetch>()
      .mockRejectedValue(new TypeError("offline"));
    await expect(apiGet("products", {}, { fetcher })).rejects.toBeInstanceOf(
      ApiError,
    );
    expect(fetcher).toHaveBeenCalledTimes(2);
  });
  it("rejects malformed API envelopes", async () => {
    const fetcher = vi.fn<typeof fetch>().mockResolvedValue(new Response("{}"));
    await expect(
      apiGet("products", {}, { fetcher, retries: 0 }),
    ).rejects.toMatchObject({ status: 502 });
  });
});
