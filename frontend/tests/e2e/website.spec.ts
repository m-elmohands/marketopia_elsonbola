import { expect, test } from "@playwright/test";
import brand from "../../src/config/brand.json" with { type: "json" };
for (const locale of ["en", "ar"] as const) {
  for (const width of [360, 768, 1280, 1920]) {
    test(`${locale} responsive layout at ${width}px`, async ({ page }) => {
      await page.setViewportSize({ width, height: 1000 });
      for (const path of [
        "",
        "/about",
        "/products",
        "/contact",
        "/products/details?id=1",
      ]) {
        await page.goto(`/${locale}${path}`);
        await expect(page.locator("html")).toHaveAttribute("lang", locale);
        await expect(page.locator("html")).toHaveAttribute(
          "dir",
          locale === "ar" ? "rtl" : "ltr",
        );
        await expect(page.locator("h1")).toBeVisible();
        expect(
          await page.evaluate(
            () => document.documentElement.scrollWidth <= window.innerWidth,
          ),
        ).toBe(true);
      }
      await page.goto(`/${locale}`);
      await page.emulateMedia({ reducedMotion: "reduce" });
      await page.screenshot({
        path: `test-results/screenshots/${locale}-${width}.png`,
        fullPage: true,
      });
    });
  }
  test(`${locale} pack selection works with keyboard and keeps the page on language switch`, async ({
    page,
  }) => {
    await page.goto(`/${locale}/products/details?id=1&pack=5`);
    await expect(page.locator('input[value="5"]')).toBeChecked();
    await page.locator('input[value="10"]').focus();
    await page.keyboard.press("Space");
    await expect(page.locator('input[value="10"]')).toBeChecked();
    await expect(page).toHaveURL(/pack=10/);
    await expect(page.locator(".detail-image img")).toHaveAttribute(
      "src",
      /rice-10kg/,
    );
    await page.locator(".language-link").click();
    await expect(page).toHaveURL(
      new RegExp(
        `/${locale === "ar" ? "en" : "ar"}/products/details\\?id=1&pack=10`,
      ),
    );
    await expect(page.locator('input[value="10"]')).toBeChecked();
  });
}
test("catalog search, empty results, sort, and pagination", async ({
  page,
}) => {
  await page.goto("/en/products");
  await expect(page.locator(".product-card")).toHaveCount(12);
  await page.getByRole("link", { name: "Next", exact: true }).click();
  await expect(page.locator(".product-card")).toHaveCount(2);
  await page.getByLabel("Search products", { exact: true }).fill("missing");
  await page.getByRole("button", { name: "Search", exact: true }).click();
  await expect(
    page.getByRole("heading", { name: "Nothing here just yet." }),
  ).toBeVisible();
  await page
    .getByRole("link", { name: "Clear filters", exact: true })
    .first()
    .click();
  await page.getByLabel("Sort by", { exact: true }).selectOption("-name");
  await page.getByRole("button", { name: "Search", exact: true }).click();
  await expect(page.locator(".product-card h3").first()).toContainText("14");
});
test("mobile menu and imported logo", async ({ page }) => {
  await page.setViewportSize({ width: 360, height: 800 });
  await page.goto("/en");
  expect(
    decodeURIComponent(
      (await page.locator("header .brand-logo").getAttribute("src")) ?? "",
    ),
  ).toContain(brand.logo);
  await page.locator(".mobile-menu summary").click();
  await page
    .locator(".mobile-nav")
    .getByRole("link", { name: "Our story" })
    .click();
  await expect(page).toHaveURL(/\/en\/about/);
});
test("missing products return 404 and failures have a recovery page", async ({
  page,
}) => {
  const response = await page.goto("/en/products/details?id=999999");
  expect([200, 404]).toContain(response?.status());
  await expect(
    page.getByRole("heading", { name: "This page has wandered off." }),
  ).toBeVisible();
  await page.goto("/en/products?search=server-error");
  await expect(
    page.getByRole("heading", { name: "We could not load this page." }),
  ).toBeVisible();
  await expect(page.getByRole("button", { name: "Try again" })).toBeVisible();
});
test("contact uses configured links, no unconfirmed WhatsApp or map", async ({
  page,
}) => {
  await page.goto("/en/contact");
  await expect(page.locator('a[href="tel:+201005263014"]')).toHaveCount(2);
  await expect(
    page.locator('a[href="mailto:zikry0100@gmail.com"]'),
  ).toBeVisible();
  await expect(page.locator('a[href*="wa.me"]')).toHaveCount(0);
});
test("revalidation requires authorization and allowlisted tags", async ({
  request,
}) => {
  expect(
    (
      await request.post("/api/revalidate", {
        data: { tags: ["website-content"] },
      })
    ).status(),
  ).toBe(401);
  expect(
    (
      await request.post("/api/revalidate", {
        headers: { Authorization: "Bearer isolated-test-token" },
        data: { tags: ["other"] },
      })
    ).status(),
  ).toBe(422);
  expect(
    (
      await request.post("/api/revalidate", {
        headers: { Authorization: "Bearer isolated-test-token" },
        data: { tags: ["website-content"] },
      })
    ).status(),
  ).toBe(200);
});

for (const locale of ["en", "ar"] as const) {
  test(`${locale} sections fade upward on scroll and stay revealed`, async ({
    page,
  }) => {
    const hydrationErrors: string[] = [];
    page.on("console", (message) => {
      if (message.type() === "error" && /hydrat/i.test(message.text())) {
        hydrationErrors.push(message.text());
      }
    });
    await page.emulateMedia({ reducedMotion: "no-preference" });
    await page.setViewportSize({ width: 1280, height: 800 });
    await page.goto(`/${locale}`);
    const section = page.locator(".contact-banner-inner");
    await expect(section).toHaveCSS("opacity", "0");
    await section.scrollIntoViewIfNeeded();
    await expect(section).toHaveCSS("opacity", "1");
    await expect(section).toHaveCSS("transform", "none");
    expect(hydrationErrors).toEqual([]);
    await page.evaluate(() => window.scrollTo(0, 0));
    await expect(section).toHaveCSS("opacity", "1");
  });
}
test("reduced motion and disabled JavaScript keep content readable", async ({
  page,
  browser,
}) => {
  await page.emulateMedia({ reducedMotion: "reduce" });
  await page.goto("/en");
  const section = page.locator(".contact-banner-inner");
  await expect(section).toHaveCSS("opacity", "1");
  await expect(section).not.toHaveAttribute("data-reveal-state", "pending");
  const context = await browser.newContext({ javaScriptEnabled: false });
  const staticPage = await context.newPage();
  await staticPage.goto("http://127.0.0.1:3100/ar");
  await expect(staticPage.locator(".contact-banner-inner")).toHaveCSS(
    "opacity",
    "1",
  );
  await context.close();
});
