import PageTitle from '@/components/sections/PageTitle';
import BlogGrid from '@/components/sections/BlogGrid';
import WordPressError from '@/components/ui/WordPressError';
import { getBlogPosts } from '@/lib/wordpress';
import type { BlogPost } from '@/types/wordpress';

export const metadata = {
  title: 'Blog | Megakit',
  description: 'Read the latest news and articles.',
};

interface BlogPageProps {
  searchParams?: Promise<{ page?: string }>;
}

export default async function BlogPage({ searchParams }: BlogPageProps) {
  const params = await searchParams;
  const currentPage = parseInt(params?.page || '1', 10);
  const result = await getBlogPosts(6);

  if (result.error) {
    return (
      <div className="main-wrapper">
        <PageTitle
          subtitle="Our blog"
          heading="Blog articles"
          breadcrumbs={[{ label: 'Home', href: '/' }, { label: 'Our blog' }]}
        />
        <WordPressError error={result.error} pageName="Blog" />
      </div>
    );
  }

  const postsData = result.data?.posts;
  const posts = (postsData?.nodes ?? []) as BlogPost[];
  const pageInfo = postsData?.pageInfo as { hasNextPage?: boolean } | undefined;

  return (
    <div className="main-wrapper">
      <PageTitle
        subtitle="Our blog"
        heading="Blog articles"
        breadcrumbs={[{ label: 'Home', href: '/' }, { label: 'Our blog' }]}
      />

      <BlogGrid
        posts={posts}
        hasNextPage={pageInfo?.hasNextPage}
        hasPrevPage={currentPage > 1}
        currentPage={currentPage}
      />
    </div>
  );
}
