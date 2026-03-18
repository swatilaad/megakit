import Image from 'next/image';
import Link from 'next/link';
import type { BlogPost } from '@/types/wordpress';

interface BlogGridProps {
  posts?: BlogPost[];
  hasNextPage?: boolean;
  hasPrevPage?: boolean;
  currentPage?: number;
}

function formatDate(dateStr: string) {
  if (!dateStr) return '';
  try {
    return new Date(dateStr).toLocaleDateString('en-US', { day: 'numeric', month: 'long' });
  } catch {
    return dateStr;
  }
}

export default function BlogGrid({ posts, hasNextPage, hasPrevPage, currentPage = 1 }: BlogGridProps) {
  if (!posts?.length) {
    return (
      <section className="section blog-wrap bg-gray">
        <div className="container">
          <div className="row justify-content-center">
            <div className="col-lg-8 text-center">
              <p className="text-muted">No blog posts found.</p>
            </div>
          </div>
        </div>
      </section>
    );
  }

  return (
    <section className="section blog-wrap bg-gray">
      <div className="container">
        <div className="row">
          {posts.map((post) => (
            <div key={post.id} className="col-lg-6 col-md-6 mb-5">
              <div className="blog-item">
                {post.featuredImage && (
                  <div className="position-relative" style={{ height: 250 }}>
                    <Image
                      src={post.featuredImage.sourceUrl}
                      alt={post.featuredImage.altText || post.title}
                      fill
                      className="img-fluid rounded"
                      style={{ objectFit: 'cover' }}
                      sizes="(max-width: 768px) 100vw, 50vw"
                    />
                  </div>
                )}
                <div className="blog-item-content bg-white p-5">
                  <div className="blog-item-meta bg-gray py-1 px-2">
                    {post.categories?.slice(0, 1).map((cat, i) => (
                      <span key={i} className="text-muted text-capitalize mr-3">
                        <i className="ti-pencil-alt mr-2"></i>{cat.name}
                      </span>
                    ))}
                    <span className="text-muted text-capitalize mr-3">
                      <i className="ti-comment mr-2"></i>5 Comments
                    </span>
                    <span className="text-black text-capitalize mr-3">
                      <i className="ti-time mr-1"></i> {formatDate(post.date)}
                    </span>
                  </div>
                  <h3 className="mt-3 mb-3">
                    <Link href={`/blog/${post.slug}`}>{post.title}</Link>
                  </h3>
                  <p
                    className="mb-4"
                    dangerouslySetInnerHTML={{ __html: post.excerpt || '' }}
                  />
                  <Link href={`/blog/${post.slug}`} className="btn btn-small btn-main btn-round-full">
                    Learn More
                  </Link>
                </div>
              </div>
            </div>
          ))}
        </div>

        <div className="row justify-content-center mt-5">
          <div className="col-lg-6 text-center">
            <nav className="navigation pagination d-inline-block">
              <div className="nav-links">
                {hasPrevPage && (
                  <Link className="prev page-numbers" href={`/blog?page=${currentPage - 1}`}>
                    Prev
                  </Link>
                )}
                <span aria-current="page" className="page-numbers current">
                  {currentPage}
                </span>
                {hasNextPage && (
                  <Link className="next page-numbers" href={`/blog?page=${currentPage + 1}`}>
                    Next
                  </Link>
                )}
              </div>
            </nav>
          </div>
        </div>
      </div>
    </section>
  );
}
