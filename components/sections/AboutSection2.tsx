import Image from 'next/image';
import Link from 'next/link';
import type { MediaItem } from '@/types/wordpress';

interface AboutInfoItem {
  number: string;
  title: string;
  description: string;
}

interface AboutSection2Props {
  subtitle?: string;
  heading?: string;
  description?: string;
  buttonLabel?: string;
  buttonUrl?: string;
  image?: MediaItem | null;
  infoItems?: AboutInfoItem[];
}

export default function AboutSection2({
  subtitle,
  heading,
  description,
  buttonLabel,
  buttonUrl,
  image,
  infoItems,
}: AboutSection2Props) {
  if (!heading && !subtitle) return null;

  return (
    <>
      <section className="section about-2 position-relative">
        <div className="container">
          <div className="row">
            <div className="col-lg-6 col-md-6">
              <div className="about-item pr-3 mb-5 mb-lg-0">
                {subtitle && <span className="h6 text-color">{subtitle}</span>}
                {heading && <h2 className="mt-3 mb-4 position-relative content-title">{heading}</h2>}
                {description && (
                  <p className="mb-5" dangerouslySetInnerHTML={{ __html: description }} />
                )}
                {buttonLabel && buttonUrl && (
                  <Link href={buttonUrl} className="btn btn-main btn-round-full">
                    {buttonLabel}
                  </Link>
                )}
              </div>
            </div>
            {image?.sourceUrl && (
              <div className="col-lg-6 col-md-6">
                <div className="about-item-img">
                  <Image
                    src={image.sourceUrl}
                    alt={image.altText || heading || ''}
                    width={600}
                    height={450}
                    className="img-fluid"
                    style={{ objectFit: 'cover', width: '100%' }}
                  />
                </div>
              </div>
            )}
          </div>
        </div>
      </section>

      {infoItems && infoItems.length > 0 && (
        <section className="about-info section pt-0">
          <div className="container">
            <div className="row justify-content-center">
              {infoItems.map((item, i) => (
                <div key={i} className="col-lg-4 col-md-6 col-sm-6">
                  <div className="about-info-item mb-4 mb-lg-0">
                    <h3 className="mb-3">
                      <span className="text-color mr-2 text-md">{item.number}</span>
                      {item.title}
                    </h3>
                    <p>{item.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>
      )}
    </>
  );
}
