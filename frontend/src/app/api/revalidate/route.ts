import { revalidateTag } from "next/cache";
import {
  authorizedRevalidation,
  validRevalidationPayload,
} from "@/lib/revalidation";
export async function POST(request: Request) {
  if (
    !authorizedRevalidation(
      request.headers.get("authorization"),
      process.env.REVALIDATION_TOKEN,
    )
  )
    return Response.json({ message: "Unauthorized" }, { status: 401 });
  try {
    if (!validRevalidationPayload(await request.json()))
      return Response.json({ message: "Invalid tags" }, { status: 422 });
  } catch {
    return Response.json({ message: "Invalid JSON" }, { status: 400 });
  }
  revalidateTag("website-content", { expire: 0 });
  return Response.json({ revalidated: true });
}
