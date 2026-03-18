import type { CTASection as CTASectionType } from '@/types/wordpress';

interface CTASectionProps {
  data?: CTASectionType | null;
}

export default function CTASection({ data }: CTASectionProps) {
  if (!data) return null;

  return (
    <section
      className="section cta"
      style={
        data.backgroundImage
          ? { backgroundImage: `url(${data.backgroundImage.sourceUrl})` }
          : undefined
      }
    >
      <div className="container">
        <div className="row">
          <div className="col-lg-5">
            <div className="cta-item bg-white p-5 rounded">
              <span className="h6 text-color">{data.subtitle}</span>
              <h2 className="mt-2 mb-4">{data.heading}</h2>
              <p className="lead mb-4">{data.description}</p>
              <h3>
                <i className="ti-mobile mr-3 text-color"></i>
                {data.phone}
              </h3>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
