function normalizeLegacyContent(text: string): string {
  if (!/<\/?(?:p|h[1-6]|div|li|br)(?:\s|>)/i.test(text)) return text;
  // Older seed data used HTML. Convert it to text; never insert API HTML into the DOM.
  return text
    .replace(/<\/(?:p|h[1-6]|div|li)>/gi, "\n\n")
    .replace(/<br\s*\/?\s*>/gi, "\n")
    .replace(/<[^>]*>/g, "")
    .replace(/&(?:amp|lt|gt|quot|#39|nbsp);/g, (entity) => {
      const entities: Record<string, string> = {
        "&amp;": "&",
        "&lt;": "<",
        "&gt;": ">",
        "&quot;": '"',
        "&#39;": "'",
        "&nbsp;": " ",
      };
      return entities[entity];
    });
}

export function contentParagraphs(text: string): string[] {
  return normalizeLegacyContent(text)
    .split(/\n\s*\n/)
    .map((paragraph) => paragraph.trim())
    .filter(Boolean);
}
