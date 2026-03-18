import PageTitle from '@/components/sections/PageTitle';
import BlogSingle from '@/components/sections/BlogSingle';
import WordPressError from '@/components/ui/WordPressError';
import { getBlogPost } from '@/lib/wordpress';
import type { BlogPost } from '@/types/wordpress';

interface BlogPostPageProps {
  params: Promise<{ slug: string }>;
}

export async function generateMetadata({ params }: BlogPostPageProps) {
  const { slug } = await params;
  const result = await getBlogPost(slug);
  const post = result.data?.post as BlogPost | undefined;
  return {
    title: post ? `${post.title} | Megakit Blog` : 'Blog Post | Megakit',
    description: post?.excerpt?.replace(/<[^>]*>/g, '').slice(0, 160) || '',
  };
}

export default async function BlogPostPage({ params }: BlogPostPageProps) {
  const { slug } = await params;
  const result = await getBlogPost(slug);

  if (result.error) {
    return (
      <div className="main-wrapper">
        <PageTitle
          subtitle="Our blog"
          heading="Blog Post"
          breadcrumbs={[
            { label: 'Home', href: '/' },
            { label: 'Blog', href: '/blog' },
            { label: 'Post' },
          ]}
        />
        <WordPressError error={result.error} pageName="Blog Post" />
      </div>
    );
  }

  const post = result.data?.post as BlogPost | undefined;
  const recentPosts = (result.data?.posts as { nodes?: BlogPost[] } | undefined)?.nodes ?? [];

  return (
    <div className="main-wrapper">
      <PageTitle
        subtitle="Our blog"
        heading={post?.title || 'Blog Post'}
        breadcrumbs={[
          { label: 'Home', href: '/' },
          { label: 'Blog', href: '/blog' },
          { label: post?.title || 'Post' },
        ]}
      />

      <BlogSingle post={post} recentPosts={recentPosts} />
    </div>
  );
}
