import Link from 'next/link';
import type { CTABlock2 as CTABlock2Type } from '@/types/wordpress';

interface CTABlock2Props {
  data?: CTABlock2Type | null;
  dark?: boolean;
}

export default function CTABlock2({ data, dark = false }: CTABlock2Props) {
  if (!data) return null;

  if (dark) {
    return (
      <section className="cta-2">
        <div className="container">
          <div className="cta-block p-5 rounded">
            <div className="row justify-content-center align-items-center">
              <div className="col-lg-7">
                <span className="text-color">{data.subtitle}</span>
                <h2 className="mt-2 text-white">{data.heading}</h2>
              </div>
              <div className="col-lg-4">
                <Link
                  href={data.buttonUrl}
                  className="btn btn-main btn-round-full float-right"
                >
                  {data.buttonLabel}
                </Link>
              </div>
            </div>
          </div>
        </div>
      </section>
    );
  }

  return (
    <section className="mt-70 position-relative">
      <div className="container">
        <div className="cta-block-2 bg-gray p-5 rounded border-1">
          <div className="row justify-content-center align-items-center">
            <div className="col-lg-7">
              <span className="text-color">{data.subtitle}</span>
              <h2 className="mt-2 mb-4 mb-lg-0">{data.heading}</h2>
            </div>
            <div className="col-lg-4">
              <Link
                href={data.buttonUrl}
                className="btn btn-main btn-round-full float-lg-right"
              >
                {data.buttonLabel}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
