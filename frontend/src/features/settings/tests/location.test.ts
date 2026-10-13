import { describe, expect, it } from "vitest";
import { coordinates, mapUrl } from "../services/location";
describe("configured location", () => {
  it("prefers the approved map URL and supports valid zero coordinates", () => {
    expect(
      mapUrl({
        map_url: "https://example.test/map",
        latitude: "30",
        longitude: "31",
      }),
    ).toBe("https://example.test/map");
    expect(mapUrl({ map_url: null, latitude: "0", longitude: "0" })).toContain(
      "query=0,0",
    );
  });
  it("does not invent a map from missing or invalid coordinates", () => {
    for (const [latitude, longitude] of [
      [null, null],
      ["", "0"],
      ["NaN", "31"],
      ["91", "31"],
      ["30", "181"],
    ]) {
      expect(coordinates({ latitude, longitude })).toBeNull();
      expect(
        mapUrl({ map_url: "javascript:alert(1)", latitude, longitude }),
      ).toBeUndefined();
    }
  });
});
