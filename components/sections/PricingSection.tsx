import Link from 'next/link';
import type { PricingPlan } from '@/types/wordpress';

interface PricingSectionProps {
  subtitle?: string;
  heading?: string;
  plans?: PricingPlan[];
  ctaTagline?: string;
  ctaHeading?: string;
  ctaButtonLabel?: string;
  ctaButtonUrl?: string;
}

export default function PricingSection({
  subtitle,
  heading,
  plans,
  ctaTagline,
  ctaHeading,
  ctaButtonLabel,
  ctaButtonUrl,
}: PricingSectionProps) {
  if (!plans?.length) return null;

  return (
    <section className="section pricing bg-gray position-relative">
      <div className="hero-img bg-overlay h70"></div>
      <div className="container">
        <div className="row justify-content-center">
          <div className="col-lg-7 text-center">
            <div className="section-title">
              <span className="h6 text-white">{subtitle}</span>
              <h2 className="mt-3 content-title text-white">{heading}</h2>
            </div>
          </div>
        </div>

        <div className="row justify-content-center">
          {plans.map((plan, i) => (
            <div key={i} className="col-md-4">
              <div className="card text-center mb-md-0 mb-3">
                <div className="card-body py-5">
                  <div className="pricing-header mb-5">
                    <h5 className="font-weight-normal mb-3">{plan.name}</h5>
                    <h1>{plan.price}</h1>
                    <p className="text-muted">{plan.period}</p>
                  </div>
                  <strong>Includes:</strong>
                  <ul className="list-unstyled lh-45 mt-3 text-black">
                    {plan.features.map((f, j) => (
                      <li key={j}>- {f}</li>
                    ))}
                  </ul>
                  <Link
                    href={plan.buttonUrl}
                    className={`btn btn-small mt-3 btn-round-full${plan.highlighted ? ' btn-main' : ' btn-solid-border'}`}
                  >
                    {plan.buttonLabel}
                  </Link>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {ctaHeading && (
        <div className="container">
          <div className="cta-block mt-5 p-5 rounded">
            <div className="row justify-content-center align-items-center">
              <div className="col-lg-7">
                {ctaTagline && <span className="text-color">{ctaTagline}</span>}
                <h2 className="mt-2 text-white">{ctaHeading}</h2>
              </div>
              {ctaButtonLabel && ctaButtonUrl && (
                <div className="col-lg-4">
                  <Link href={ctaButtonUrl} className="btn btn-main btn-round-full float-lg-right float-md-right float-sm-right">
                    {ctaButtonLabel}
                  </Link>
                </div>
              )}
            </div>
          </div>
        </div>
      )}
    </section>
  );
}
