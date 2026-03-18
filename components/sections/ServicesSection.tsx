import type { ServicesSection as ServicesSectionType } from '@/types/wordpress';

interface ServicesSectionProps {
  data?: ServicesSectionType | null;
}

export default function ServicesSection({ data }: ServicesSectionProps) {
  if (!data) return null;

  return (
    <section className="section service border-top">
      <div className="container">
        <div className="row justify-content-center">
          <div className="col-lg-7 text-center">
            <div className="section-title">
              <span className="h6 text-color">{data.subtitle}</span>
              <h2 className="mt-3 content-title">{data.heading}</h2>
            </div>
          </div>
        </div>
        <div className="row justify-content-center">
          {data.services.map((service, i) => (
            <div key={i} className="col-lg-4 col-md-6 col-sm-6">
              <div className={`service-item${i < data.services.length - 3 ? ' mb-5' : i < data.services.length ? ' mb-5 mb-lg-0' : ''}`}>
                <i className={service.icon}></i>
                <h4 className="mb-3">{service.title}</h4>
                <p>{service.description}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
