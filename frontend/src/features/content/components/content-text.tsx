import { contentParagraphs } from "../services/content-text";

export function ContentText({
  text,
  className = "",
}: {
  text: string;
  className?: string;
}) {
  return (
    <div className={`content-text ${className}`}>
      {contentParagraphs(text).map((paragraph, index) => (
        <p key={index}>{paragraph}</p>
      ))}
    </div>
  );
}
