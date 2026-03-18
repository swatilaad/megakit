'use client';

import Slider from 'react-slick';
import type { TestimonialsSection as TestimonialsSectionType } from '@/types/wordpress';

interface TestimonialsSectionProps {
  data?: TestimonialsSectionType | null;
  bgGray?: boolean;
}

const slickSettings = {
  dots: true,
  infinite: true,
  speed: 500,
  slidesToShow: 2,
  slidesToScroll: 2,
  arrows: false,
  autoplay: true,
  autoplaySpeed: 6000,
  responsive: [
    { breakpoint: 900, settings: { slidesToShow: 2, slidesToScroll: 2 } },
    { breakpoint: 600, settings: { slidesToShow: 1, slidesToScroll: 1 } },
    { breakpoint: 480, settings: { slidesToShow: 1, slidesToScroll: 1 } },
  ],
};

export default function TestimonialsSection({ data, bgGray = true }: TestimonialsSectionProps) {
  if (!data) return null;

  return (
    <section className={`section testimonial${bgGray ? ' bg-gray' : ''}`}>
      <div className="container">
        <div className="row justify-content-center">
          <div className="col-lg-7 text-center">
            <div className="section-title">
              <span className="h6 text-color">{data.subtitle}</span>
              <h2 className="mt-3 content-title">{data.heading}</h2>
            </div>
          </div>
        </div>
      </div>

      <div className="container">
        <Slider {...slickSettings} className="testimonial-wrap">
          {data.testimonials.map((item, i) => (
            <div key={i} className="testimonial-item position-relative">
              <i className="ti-quote-left text-color"></i>
              <div className="testimonial-item-content">
                <p className="testimonial-text">{item.text}</p>
                <div className="testimonial-author">
                  <h5 className="mb-0 text-capitalize">{item.authorName}</h5>
                  <p>{item.authorRole}</p>
                </div>
              </div>
            </div>
          ))}
        </Slider>
      </div>
    </section>
  );
}
