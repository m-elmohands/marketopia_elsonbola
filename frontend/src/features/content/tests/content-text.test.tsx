import { describe, expect, it } from "vitest";
import { renderToStaticMarkup } from "react-dom/server";
import { ContentText } from "../components/content-text";
import { contentParagraphs } from "../services/content-text";
describe("dynamic plain text", () => {
  it("preserves ordinary comparison characters and literal entities in plain text", () => {
    expect(contentParagraphs("Rice < 10 kg, salt > 2 g &amp; grains")).toEqual([
      "Rice < 10 kg, salt > 2 g &amp; grains",
    ]);
  });
  it("preserves paragraph boundaries and converts legacy seed markup into text", () => {
    expect(
      contentParagraphs(
        "<h2>Our rice</h2><p>Rice &amp; grains</p><p>Next<br>line</p>",
      ),
    ).toEqual(["Our rice", "Rice & grains", "Next\nline"]);
  });
  it("never executes HTML from an API response", () => {
    const html = renderToStaticMarkup(
      <ContentText
        text={
          "<script>alert(1)</script><p>&lt;img src=x onerror=alert(1)&gt;</p>"
        }
      />,
    );
    expect(html).not.toContain("<script>");
    expect(html).not.toContain("<img");
    expect(html).toContain("&lt;img");
  });
});
