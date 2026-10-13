"use client";

import { useEffect } from "react";
import { usePathname } from "next/navigation";
import { observeScrollReveal } from "../services/observe-scroll-reveal";

export function ScrollReveal() {
  const pathname = usePathname();

  useEffect(() => {
    const root = document.getElementById("main");
    if (root) return observeScrollReveal(root);
  }, [pathname]);

  return null;
}
