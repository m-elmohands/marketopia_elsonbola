"use client";
import Link from "next/link";
import { useParams } from "next/navigation";
import { Wheat } from "lucide-react";
import { dictionary } from "@/i18n/config";
export default function NotFound() {
  const { locale } = useParams(),
    current = locale === "en" ? "en" : "ar",
    t = dictionary(current);
  return (
    <section className="container error-state">
      <Wheat size={48} strokeWidth={1} aria-hidden="true" />
      <span className="eyebrow">404</span>
      <h1>{t.notFoundTitle}</h1>
      <p>{t.notFoundBody}</p>
      <Link className="button" href={`/${current}/products`}>
        {t.explore}
      </Link>
    </section>
  );
}
