import Link from 'next/link';
import type { AboutSection as AboutSectionType } from '@/types/wordpress';

interface AboutSectionProps {
  data?: AboutSectionType | null;
}

export default function AboutSection({ data }: AboutSectionProps) {
  if (!data) return null;

  return (
    <section className="section about position-relative">
      <div
        className="bg-about"
        style={
          data.backgroundImage
            ? { backgroundImage: `url(${data.backgroundImage.sourceUrl})` }
            : undefined
        }
      ></div>
      <div className="container">
        <div className="row">
          <div className="col-lg-6 offset-lg-6 offset-md-0">
            <div className="about-item">
              <span className="h6 text-color">{data.subtitle}</span>
              <h2 className="mt-3 mb-4 position-relative content-title">{data.heading}</h2>
              <div className="about-content">
                <h4 className="mb-3 position-relative">{data.subheading}</h4>
                <p className="mb-5" dangerouslySetInnerHTML={{ __html: data.description }} />
                <Link href={data.buttonUrl} className="btn btn-main btn-round-full">
                  {data.buttonLabel}
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
