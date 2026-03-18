import FlexibleSections from '@/components/sections/FlexibleSections';
import WordPressError from '@/components/ui/WordPressError';
import { getPage, getBlogPosts } from '@/lib/wordpress';
import type { FlexSection, BlogPost } from '@/types/wordpress';

export const metadata = {
  title: 'Megakit – Creative Digital Agency',
  description: 'We are a team of passionate designers and developers who create exceptional digital experiences.',
};

export default async function HomePage() {
  const [pageResult, blogResult] = await Promise.all([
    getPage('home'),
    getBlogPosts(3),
  ]);

  if (pageResult.error) {
    return (
      <div className="main-wrapper">
        <WordPressError error={pageResult.error} pageName="Home" />
      </div>
    );
  }

  const sections = (pageResult.data?.pageFields?.sections ?? []) as FlexSection[];
  const blogData = blogResult.data as { posts?: { nodes: BlogPost[] } } | null;
  const blogPosts = blogData?.posts?.nodes ?? [];

  return (
    <div className="main-wrapper">
      <FlexibleSections sections={sections} blogPosts={blogPosts} />
    </div>
  );
}
