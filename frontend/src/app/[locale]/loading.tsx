import { Wheat } from "lucide-react";
export default function Loading() {
  return (
    <div className="loading-state" role="status">
      <Wheat size={40} strokeWidth={1} aria-hidden="true" />
      <p>Loading / جارٍ التحميل</p>
    </div>
  );
}
