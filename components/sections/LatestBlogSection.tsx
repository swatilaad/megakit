import Image from 'next/image';
import Link from 'next/link';
import type { BlogPost } from '@/types/wordpress';

interface LatestBlogSectionProps {
  posts?: BlogPost[];
  subtitle?: string;
  heading?: string;
}

export default function LatestBlogSection({ posts, subtitle, heading }: LatestBlogSectionProps) {
  if (!posts?.length) return null;

  return (
    <section className="section latest-blog bg-2">
      <div className="container">
        <div className="row justify-content-center">
          <div className="col-lg-7 text-center">
            <div className="section-title">
              <span className="h6 text-color">{subtitle}</span>
              <h2 className="mt-3 content-title text-white">{heading}</h2>
            </div>
          </div>
        </div>

        <div className="row justify-content-center">
          {posts.slice(0, 3).map((post) => (
            <div key={post.id} className="col-lg-4 col-md-6 mb-5">
              <div className="card bg-transparent border-0">
                {post.featuredImage && (
                  <div className="position-relative" style={{ height: 220 }}>
                    <Image
                      src={post.featuredImage.sourceUrl}
                      alt={post.featuredImage.altText || post.title}
                      fill
                      className="img-fluid rounded"
                      style={{ objectFit: 'cover', borderRadius: 4 }}
                      sizes="(max-width: 768px) 100vw, 33vw"
                    />
                  </div>
                )}
                <div className="card-body mt-2">
                  <div className="blog-item-meta">
                    {post.categories?.slice(0, 2).map((cat, i) => (
                      <a key={i} href="#" className="text-white-50">
                        {cat.name}
                        <span className="ml-2 mr-2">/</span>
                      </a>
                    ))}
                    <a href="#" className="text-white-50 ml-2">
                      <i className="fa fa-user mr-2"></i>
                      {post.author?.name || 'admin'}
                    </a>
                  </div>
                  <h3 className="mt-3 mb-5 lh-36">
                    <Link href={`/blog/${post.slug}`} className="text-white">
                      {post.title}
                    </Link>
                  </h3>
                  <Link
                    href={`/blog/${post.slug}`}
                    className="btn btn-small btn-solid-border btn-round-full text-white"
                  >
                    Learn More
                  </Link>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
