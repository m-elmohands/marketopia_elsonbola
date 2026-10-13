import { createServer } from "node:http";
import { readFileSync } from "node:fs";
import { spawn } from "node:child_process";
const content = JSON.parse(
  readFileSync(
    new URL(
      "../../src/features/website/tests/fixtures/laravel-content.json",
      import.meta.url,
    ),
    "utf8",
  ),
);
function localMedia(record) {
  return {
    ...record,
    media: record.media.map((image) => {
      const original = new URL(image.url).pathname.split("/").at(-1);
      const file = original === "logo.jpg" ? "facebook-logo.jpg" : original;
      const url = `/images/${file}`;
      return { ...image, url, web_url: url, thumbnail_url: url };
    }),
  };
}
const fixture = createServer((request, response) => {
  const url = new URL(request.url, "http://127.0.0.1:4011"),
    locale = url.searchParams.get("locale") === "ar" ? "ar" : "en";
  const arabic = locale === "ar";
  const media = {
    id: 1,
    collection: "cover",
    url: "/images/rice-5kg.jpg",
    web_url: "/images/rice-5kg.jpg",
    thumbnail_url: "/images/rice-5kg.jpg",
    alt: arabic ? "عبوة أرز" : "Rice pack",
    locale: null,
  };
  const products = Array.from({ length: 14 }, (_, i) => ({
    id: i + 1,
    slug: i === 0 ? "egyptian-rice" : `rice-${i + 1}`,
    name: arabic ? `أرز مصري ${i + 1}` : `Egyptian Rice ${i + 1}`,
    short_description: arabic
      ? "أرز مصري لمائدتك"
      : "Egyptian rice for your table.",
    description: arabic
      ? "أرز مصري بأحجام متعددة."
      : "Egyptian rice in a range of pack sizes.",
    meta_title: null,
    meta_description: null,
    is_featured: i < 3,
    media: [media, { ...media, id: 2 }],
    variants: [1, 5, 10, 25].map((weight) => ({
      id: weight,
      weight_kg: String(weight),
      media: [
        {
          ...media,
          id: weight,
          web_url: `/images/rice-${weight}kg.jpg`,
          thumbnail_url: `/images/rice-${weight}kg.jpg`,
        },
      ],
    })),
  }));
  let status = 200,
    data = null,
    meta = {};
  if (
    url.pathname === "/api/v1/settings" ||
    url.pathname.startsWith("/api/v1/pages/")
  ) {
    const response = content[`${url.pathname}?locale=${locale}`];
    if (response) data = localMedia(response.data);
    else status = 404;
  } else if (url.pathname === "/api/v1/products") {
    const search = url.searchParams.get("search")?.toLowerCase() ?? "";
    if (search === "server-error") status = 503;
    let filtered = products.filter(
      (p) =>
        (!url.searchParams.get("featured") || p.is_featured) &&
        p.name.toLowerCase().includes(search),
    );
    if (url.searchParams.get("sort") === "-name") filtered = filtered.reverse();
    const page = Number(url.searchParams.get("page") ?? 1),
      perPage = Number(url.searchParams.get("per_page") ?? 12);
    data = filtered.slice((page - 1) * perPage, page * perPage);
    meta = {
      current_page: page,
      per_page: perPage,
      total: filtered.length,
      last_page: Math.max(1, Math.ceil(filtered.length / perPage)),
    };
  } else if (url.pathname === "/api/v1/products/details") {
    data = products.find((p) => String(p.id) === url.searchParams.get("id"));
    if (!data) status = 404;
  } else status = 404;
  response.writeHead(status, { "Content-Type": "application/json" });
  response.end(
    JSON.stringify({
      data,
      message: status === 200 ? "Success" : "Unavailable",
      errors: null,
      meta,
    }),
  );
});
await new Promise((resolve) => fixture.listen(4011, "127.0.0.1", resolve));
const child = spawn(
  process.execPath,
  [
    "node_modules/next/dist/bin/next",
    "dev",
    "--webpack",
    "--hostname",
    "127.0.0.1",
    "--port",
    "3100",
  ],
  {
    stdio: "inherit",
    env: {
      ...process.env,
      WEBSITE_DEMO_MODE: "false",
      API_BASE_URL: "http://127.0.0.1:4011/api/v1",
      SITE_URL: "http://127.0.0.1:3100",
      REVALIDATION_TOKEN: "isolated-test-token",
    },
  },
);
function close() {
  child.kill("SIGTERM");
  fixture.close();
}
process.on("SIGTERM", close);
process.on("SIGINT", close);
child.on("exit", (code) => {
  fixture.close();
  process.exit(code ?? 0);
});
