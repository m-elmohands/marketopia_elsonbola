export const dynamic = "force-dynamic";
import type { MetadataRoute } from "next";
import { siteUrl } from "@/lib/seo";
import { isDemoMode } from "@/lib/demo";
export default function robots(): MetadataRoute.Robots {
  return {
    rules: {
      userAgent: "*",
      ...(isDemoMode() ? { disallow: "/" } : { allow: "/", disallow: "/api/" }),
    },
    sitemap: `${siteUrl}/sitemap.xml`,
  };
}
