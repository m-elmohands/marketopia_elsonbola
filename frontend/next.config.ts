import type { NextConfig } from "next";
const media = new URL(process.env.MEDIA_BASE_URL ?? "http://localhost:8000");
const config: NextConfig = {
  poweredByHeader: false,
  images: {
    remotePatterns: [
      {
        protocol: media.protocol === "https:" ? "https" : "http",
        hostname: media.hostname,
        port: media.port,
        pathname: "/storage/**",
      },
    ],
    // Laravel storage is served directly locally; production retains optimized images.
    unoptimized: process.env.NODE_ENV === "development",
  },
};
export default config;
