import Image from 'next/image';
import Link from 'next/link';
import type { BlogPost } from '@/types/wordpress';

interface BlogSingleProps {
  post?: BlogPost | null;
  recentPosts?: BlogPost[];
}

function formatDate(dateStr: string) {
  if (!dateStr) return '';
  try {
    return new Date(dateStr).toLocaleDateString('en-US', {
      year: 'numeric', month: 'long', day: 'numeric',
    });
  } catch {
    return dateStr;
  }
}

export default function BlogSingle({ post, recentPosts }: BlogSingleProps) {
  if (!post) return null;

  return (
    <section className="section blog-wrap">
      <div className="container">
        <div className="row">
          {/* Main Content */}
          <div className="col-lg-8">
            <div className="single-blog">
              {post.featuredImage && (
                <Image
                  src={post.featuredImage.sourceUrl}
                  alt={post.featuredImage.altText || post.title}
                  width={800}
                  height={450}
                  className="img-fluid rounded mb-4"
                  style={{ width: '100%', objectFit: 'cover' }}
                />
              )}

              <div className="blog-item-meta bg-gray py-1 px-2 mb-4">
                {post.categories?.map((cat, i) => (
                  <span key={i} className="text-muted text-capitalize mr-3">
                    <i className="ti-pencil-alt mr-2"></i>{cat.name}
                  </span>
                ))}
                <span className="text-muted text-capitalize mr-3">
                  <i className="ti-time mr-1"></i> {formatDate(post.date)}
                </span>
                {post.author?.name && (
                  <span className="text-muted text-capitalize">
                    <i className="fa fa-user mr-2"></i>{post.author.name}
                  </span>
                )}
              </div>

              <h2 className="mb-4">{post.title}</h2>

              <div
                className="post-content"
                dangerouslySetInnerHTML={{ __html: post.content || '' }}
              />

              {post.tags && post.tags.length > 0 && (
                <div className="tags mt-4">
                  {post.tags.map((tag, i) => (
                    <Link key={i} href={`/blog?tag=${tag.name}`}>
                      {tag.name}
                    </Link>
                  ))}
                </div>
              )}

              <div className="next-prev mt-5">
                <div className="row">
                  <div className="col-6 prev-post">
                    <Link href="/blog">
                      <i className="ti-arrow-left mr-2"></i>Prev Post
                    </Link>
                  </div>
                  <div className="col-6 next-post text-right">
                    <Link href="/blog">
                      Next Post<i className="ti-arrow-right ml-2"></i>
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Sidebar */}
          <div className="col-lg-4">
            {recentPosts && recentPosts.length > 0 && (
              <div className="widget widget-latest-post mb-5">
                <h4 className="widget-title">Latest Posts</h4>
                {recentPosts.map((rp) => (
                  <div key={rp.id} className="media mb-3">
                    {rp.featuredImage && (
                      <Image
                        src={rp.featuredImage.sourceUrl}
                        alt={rp.featuredImage.altText || rp.title}
                        width={100}
                        height={70}
                        className="media-object mr-3 rounded"
                        style={{ objectFit: 'cover' }}
                      />
                    )}
                    <div className="media-body">
                      <h5 className="media-heading">
                        <Link href={`/blog/${rp.slug}`}>{rp.title}</Link>
                      </h5>
                      <p>{formatDate(rp.date)}</p>
                    </div>
                  </div>
                ))}
              </div>
            )}

            {post.categories && post.categories.length > 0 && (
              <div className="widget widget-category mb-5">
                <h4 className="widget-title">Categories</h4>
                <ul>
                  {post.categories.map((cat, i) => (
                    <li key={i}>
                      <Link href={`/blog?category=${cat.name}`}>{cat.name}</Link>
                    </li>
                  ))}
                </ul>
              </div>
            )}

            {post.tags && post.tags.length > 0 && (
              <div className="widget widget-tag mb-5">
                <h4 className="widget-title">Tags</h4>
                <ul className="list-inline">
                  {post.tags.map((tag, i) => (
                    <li key={i} className="list-inline-item">
                      <Link href={`/blog?tag=${tag.name}`}>{tag.name}</Link>
                    </li>
                  ))}
                </ul>
              </div>
            )}
          </div>
        </div>
      </div>
    </section>
  );
}
