export interface ApiEnvelope<T, M = Record<string, never>> {
  data: T;
  message: string;
  errors: Record<string, string[]> | null;
  meta: M;
}
export class ApiError extends Error {
  constructor(
    public readonly status: number,
    message = "Content is temporarily unavailable",
  ) {
    super(message);
    this.name = "ApiError";
  }
}
interface RequestOptions {
  fetcher?: typeof fetch;
  timeoutMs?: number;
  retries?: number;
}
export async function apiGet<T, M = Record<string, never>>(
  path: string,
  query: Record<string, string>,
  options: RequestOptions = {},
): Promise<ApiEnvelope<T, M>> {
  const base = process.env.API_BASE_URL ?? "http://127.0.0.1:8000/api/v1";
  const url = new URL(`${base.replace(/\/$/, "")}/${path.replace(/^\//, "")}`);
  Object.entries(query).forEach(([key, value]) =>
    url.searchParams.set(key, value),
  );
  const fetcher = options.fetcher ?? fetch;
  for (let attempt = 0; attempt <= (options.retries ?? 1); attempt++) {
    try {
      const response = await fetcher(url, {
        headers: { Accept: "application/json" },
        signal: AbortSignal.timeout(options.timeoutMs ?? 5000),
        next: { revalidate: 300, tags: ["website-content"] },
      });
      if (!response.ok) throw new ApiError(response.status);
      const payload: unknown = await response.json();
      if (
        !payload ||
        typeof payload !== "object" ||
        !("data" in payload) ||
        !("meta" in payload)
      )
        throw new ApiError(502, "Invalid content response");
      return payload as ApiEnvelope<T, M>;
    } catch (error) {
      if (error instanceof ApiError && error.status < 500) throw error;
      if (attempt === (options.retries ?? 1))
        throw error instanceof ApiError ? error : new ApiError(503);
      await new Promise<void>((resolve) =>
        setTimeout(resolve, 150 * (attempt + 1)),
      );
    }
  }
  throw new ApiError(503);
}
