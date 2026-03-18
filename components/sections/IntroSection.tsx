import type { IntroSection as IntroSectionType } from '@/types/wordpress';

interface IntroSectionProps {
  data?: IntroSectionType | null;
}

export default function IntroSection({ data }: IntroSectionProps) {
  if (!data) return null;

  return (
    <section className="section intro">
      <div className="container">
        <div className="row">
          <div className="col-lg-8">
            <div className="section-title">
              <span className="h6 text-color">{data.subtitle}</span>
              <h2 className="mt-3 content-title">{data.heading}</h2>
            </div>
          </div>
        </div>
        <div className="row justify-content-center">
          {data.features.map((feature, i) => (
            <div key={i} className="col-lg-4 col-md-6 col-12">
              <div className={`intro-item${i < data.features.length - 1 ? ' mb-5 mb-lg-0' : ''}`}>
                <i className={`${feature.icon} color-one`}></i>
                <h4 className="mt-4 mb-3">{feature.title}</h4>
                <p>{feature.description}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
