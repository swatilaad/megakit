import Link from 'next/link';
import type { HeroSection as HeroSectionType } from '@/types/wordpress';

interface HeroSectionProps {
  data?: HeroSectionType | null;
}

export default function HeroSection({ data }: HeroSectionProps) {
  if (!data) return null;

  return (
    <section
      className="slider"
      style={
        data.backgroundImage
          ? { backgroundImage: `url(${data.backgroundImage.sourceUrl})` }
          : undefined
      }
    >
      <div className="container">
        <div className="row">
          <div className="col-lg-9 col-md-10">
            <div className="block">
              <span className="d-block mb-3 text-white text-capitalize">{data.tagline}</span>
              <h1 className="animated fadeInUp mb-5">
                {data.heading.split('\n').map((line, i, arr) => (
                  <span key={i}>
                    {line}
                    {i < arr.length - 1 && <br />}
                  </span>
                ))}
              </h1>
              <Link
                href={data.buttonUrl}
                className="btn btn-main animated fadeInUp btn-round-full"
              >
                {data.buttonLabel}
                <i className="btn-icon fa fa-angle-right ml-2"></i>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
