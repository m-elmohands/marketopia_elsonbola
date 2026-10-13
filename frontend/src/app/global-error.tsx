"use client";
import "./globals.css";
export default function GlobalError({
  reset,
}: {
  error: Error;
  reset: () => void;
}) {
  return (
    <html lang="en">
      <body>
        <main className="container error-state">
          <h1>We could not load this page.</h1>
          <p>Please try again in a moment.</p>
          <p lang="ar" dir="rtl">
            تعذر تحميل الصفحة. يرجى المحاولة مرة أخرى.
          </p>
          <button className="button" onClick={() => reset()}>
            Try again / حاول مرة أخرى
          </button>
        </main>
      </body>
    </html>
  );
}
