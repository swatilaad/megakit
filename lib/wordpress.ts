import { graphqlFetch } from './apollo';
import {
  GET_GLOBAL_SETTINGS,
  GET_PAGE_BY_SLUG,
  GET_PORTFOLIO_ITEMS,
  GET_BLOG_POSTS,
  GET_BLOG_POST,
  GET_ALL_POST_SLUGS,
} from './queries';
import type {
  GlobalSettings,
  PageData,
  BlogPost,
  PortfolioItem,
  WordPressApiResponse,
} from '@/types/wordpress';

// ─── Helper ──────────────────────────────────────────────────────────────────
async function fetchQuery<T>(
  query: string,
  variables?: Record<string, unknown>
): Promise<WordPressApiResponse<T>> {
  try {
    const result = await graphqlFetch<T>(query, variables);
    if (result.errors && result.errors.length > 0) {
      return { data: null, error: result.errors.map((e) => e.message).join(', ') };
    }
    return { data: result.data ?? null, error: null };
  } catch (err: unknown) {
    const message =
      err instanceof Error ? err.message : 'An unknown error occurred';
    console.error('[WordPress fetch error]', message);
    return { data: null, error: message };
  }
}

// ─── Normalize a raw WPGraphQL post node into a flat BlogPost ─────────────────
// WPGraphQL nests: featuredImage.node, categories.nodes, author.node, tags.nodes
function normalizePost(raw: Record<string, unknown>): BlogPost {
  const fi = raw.featuredImage as { node?: { sourceUrl?: string; altText?: string } } | null;
  const auth = raw.author as { node?: { name?: string; avatar?: { url?: string } } } | null;
  const cats = raw.categories as { nodes?: { name: string }[] } | null;
  const tags = raw.tags as { nodes?: { name: string }[] } | null;

  return {
    id:       String(raw.id ?? ''),
    slug:     String(raw.slug ?? ''),
    title:    String(raw.title ?? ''),
    excerpt:  String(raw.excerpt ?? ''),
    date:     String(raw.date ?? ''),
    content:  raw.content ? String(raw.content) : undefined,
    featuredImage: fi?.node?.sourceUrl
      ? { sourceUrl: fi.node.sourceUrl, altText: fi.node.altText ?? '' }
      : undefined,
    categories: cats?.nodes ?? [],
    author:     auth?.node ? { name: auth.node.name ?? '' } : undefined,
    tags:       tags?.nodes ?? [],
  };
}

// ─── Global Settings ─────────────────────────────────────────────────────────
export async function getGlobalSettings(): Promise<WordPressApiResponse<GlobalSettings>> {
  const result = await fetchQuery<{ globalSettings: { globalSettingsFields: GlobalSettings } }>(
    GET_GLOBAL_SETTINGS
  );
  if (result.error || !result.data) return { data: null, error: result.error };
  return {
    data: result.data.globalSettings?.globalSettingsFields ?? null,
    error: null,
  };
}

// ─── Generic Page by Slug (Flexible Content) ──────────────────────────────────
export async function getPage(slug: string): Promise<WordPressApiResponse<PageData>> {
  const result = await fetchQuery<{ page: PageData }>(GET_PAGE_BY_SLUG, { slug });
  if (result.error || !result.data) return { data: null, error: result.error };
  return {
    data: result.data.page ?? null,
    error: null,
  };
}

// ─── Portfolio Page (page data + CPT items) ───────────────────────────────────
export async function getPortfolioPageData(): Promise<
  WordPressApiResponse<{ page: PageData; portfolioItems: { nodes: PortfolioItem[] } }>
> {
  const [pageResult, itemsResult] = await Promise.all([
    fetchQuery<{ page: PageData }>(GET_PAGE_BY_SLUG, { slug: 'portfolio' }),
    fetchQuery<{
      portfolioItems: {
        nodes: Array<{
          id: string;
          title: string;
          portfolioFields?: {
            category?: string;
            image?: { node?: { sourceUrl?: string; altText?: string } };
          };
        }>;
      };
    }>(GET_PORTFOLIO_ITEMS),
  ]);

  if (pageResult.error) return { data: null, error: pageResult.error };
  if (itemsResult.error) return { data: null, error: itemsResult.error };

  const rawNodes = itemsResult.data?.portfolioItems?.nodes ?? [];
  const portfolioItems: PortfolioItem[] = rawNodes.map((n) => ({
    id:       n.id,
    title:    n.title,
    category: n.portfolioFields?.category ?? 'Uncategorized',
    image:    n.portfolioFields?.image?.node
      ? { sourceUrl: n.portfolioFields.image.node.sourceUrl ?? '', altText: n.portfolioFields.image.node.altText ?? '' }
      : undefined,
  }));

  return {
    data: {
      page: pageResult.data?.page ?? { title: 'Portfolio', slug: 'portfolio' },
      portfolioItems: { nodes: portfolioItems },
    },
    error: null,
  };
}

// ─── Blog Posts ───────────────────────────────────────────────────────────────
export async function getBlogPosts(
  first = 9,
  after?: string
): Promise<WordPressApiResponse<{ posts: { pageInfo: unknown; nodes: BlogPost[] } }>> {
  const result = await fetchQuery<{
    posts: { pageInfo: unknown; nodes: Record<string, unknown>[] };
  }>(GET_BLOG_POSTS, { first, after });

  if (result.error || !result.data) return { data: null, error: result.error };

  return {
    data: {
      posts: {
        pageInfo: result.data.posts.pageInfo,
        nodes:    result.data.posts.nodes.map(normalizePost),
      },
    },
    error: null,
  };
}

// ─── Single Blog Post ─────────────────────────────────────────────────────────
export async function getBlogPost(
  slug: string
): Promise<WordPressApiResponse<{ post: BlogPost; posts: { nodes: BlogPost[] } }>> {
  const result = await fetchQuery<{
    post: Record<string, unknown>;
    posts: { nodes: Record<string, unknown>[] };
  }>(GET_BLOG_POST, { slug });

  if (result.error || !result.data) return { data: null, error: result.error };

  return {
    data: {
      post:  normalizePost(result.data.post),
      posts: { nodes: result.data.posts.nodes.map(normalizePost) },
    },
    error: null,
  };
}

// ─── All Post Slugs ───────────────────────────────────────────────────────────
export async function getAllPostSlugs() {
  return fetchQuery<{ posts: { nodes: { slug: string }[] } }>(GET_ALL_POST_SLUGS);
}
