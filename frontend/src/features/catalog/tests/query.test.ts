import { describe, expect, it } from "vitest";
import { catalogQuery, catalogPageUrl } from "../services/query";
import { demoCatalog } from "@/lib/demo";
describe("catalog filters", () => {
  it("bounds invalid page, sort and search input", () => {
    expect(
      catalogQuery({ page: "-3", sort: "DROP", search: ["rice"] }),
    ).toMatchObject({ page: 1, sort: "sort_order", search: undefined });
    expect(catalogQuery({ page: "Infinity" }).page).toBe(1);
    expect(catalogQuery({ search: "x".repeat(300) }).search?.length).toBe(100);
  });
  it("preserves search and sort while paginating", () => {
    expect(
      catalogPageUrl("ar", { search: "rice & grains", sort: "-name" }, 2),
    ).toBe("/ar/products?page=2&sort=-name&search=rice+%26+grains");
  });
  it("provides genuine empty results in demo search", () => {
    expect(demoCatalog("en", { search: "not-a-product" }).data).toHaveLength(0);
    expect(demoCatalog("ar", { search: "أرز" }).data[0].name).toContain("أرز");
  });
});
