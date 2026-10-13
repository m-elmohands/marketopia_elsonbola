"use client";
import Link from "next/link";
import { useParams } from "next/navigation";
import { Wheat } from "lucide-react";
import { dictionary } from "@/i18n/config";
export default function ErrorPage({
  reset,
}: {
  error: Error & { digest?: string };
  reset: () => void;
}) {
  const { locale } = useParams(),
    current = locale === "en" ? "en" : "ar",
    t = dictionary(current);
  return (
    <section className="container error-state" role="alert">
      <Wheat size={48} strokeWidth={1} aria-hidden="true" />
      <p className="eyebrow">{t.errorEyebrow}</p>
      <h1>{t.errorTitle}</h1>
      <p>{t.errorBody}</p>
      <div>
        <button className="button" onClick={() => reset()}>
          {t.retry}
        </button>
        <Link className="text-link" href={`/${current}`}>
          {t.homeLink}
        </Link>
      </div>
    </section>
  );
}
