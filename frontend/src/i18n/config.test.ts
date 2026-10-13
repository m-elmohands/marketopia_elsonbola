import { describe, expect, it } from "vitest";
import en from "./en.json";
import ar from "./ar.json";
import { isLocale, switchLocalePath, formatWeight } from "./config";
describe("bilingual routes", () => {
  it("has matching translation keys and nonempty values", () => {
    expect(Object.keys(ar).sort()).toEqual(Object.keys(en).sort());
    for (const value of [...Object.values(ar), ...Object.values(en)])
      expect(value.trim()).not.toBe("");
  });
  it("switches only the locale segment and retains detail routes", () => {
    expect(switchLocalePath("/ar/products/details?id=2&pack=10", "en")).toBe(
      "/en/products/details?id=2&pack=10",
    );
    expect(switchLocalePath("/en/about", "ar")).toBe("/ar/about");
    expect(switchLocalePath("/archive", "en")).toBe("/archive");
  });
  it("rejects unsupported locales", () => {
    expect(isLocale("fr")).toBe(false);
    expect(isLocale("ar")).toBe(true);
  });
  it("formats pack sizes without redundant zeros", () => {
    expect(formatWeight("5.00", "en")).toBe("5");
    expect(formatWeight("5.00", "ar")).toBe("٥");
  });
});
