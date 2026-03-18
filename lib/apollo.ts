// Simple fetch-based GraphQL client for Next.js App Router (Server Components)
// This avoids the Apollo Client browser-only dependency issues

const WORDPRESS_API_URL = process.env.NEXT_PUBLIC_WORDPRESS_API_URL || '';

export interface GraphQLResponse<T = Record<string, unknown>> {
  data?: T;
  errors?: Array<{ message: string; locations?: unknown; path?: unknown }>;
}

export async function graphqlFetch<T = Record<string, unknown>>(
  query: string,
  variables?: Record<string, unknown>
): Promise<GraphQLResponse<T>> {
  if (!WORDPRESS_API_URL) {
    throw new Error(
      'NEXT_PUBLIC_WORDPRESS_API_URL is not set. Please create a .env.local file with this variable pointing to your WordPress GraphQL endpoint.'
    );
  }

  const response = await fetch(WORDPRESS_API_URL, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    },
    body: JSON.stringify({ query, variables }),
    // Next.js cache: revalidate every 60 seconds
    next: { revalidate: 60 },
  });

  if (!response.ok) {
    throw new Error(`WordPress API responded with status ${response.status}: ${response.statusText}`);
  }

  return response.json() as Promise<GraphQLResponse<T>>;
}

export { WORDPRESS_API_URL };
