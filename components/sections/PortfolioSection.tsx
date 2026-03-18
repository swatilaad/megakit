'use client';

import { useState } from 'react';
import Image from 'next/image';
import type { PortfolioItem } from '@/types/wordpress';

interface PortfolioSectionProps {
  subtitle?: string;
  heading?: string;
  items?: PortfolioItem[];
}

export default function PortfolioSection({ subtitle, heading, items }: PortfolioSectionProps) {
  const [lightbox, setLightbox] = useState<string | null>(null);

  if (!items?.length) return null;

  return (
    <section className="section portfolio pb-0">
      <div className="container">
        <div className="row justify-content-center">
          <div className="col-lg-7 text-center">
            <div className="section-title">
              <span className="h6 text-color">{subtitle}</span>
              <h2 className="mt-3 content-title">{heading}</h2>
            </div>
          </div>
        </div>
      </div>

      <div className="container-fluid">
        <div className="row portfolio-gallery">
          {items.map((item) => (
            <div key={item.id} className="col-lg-4 col-md-6">
              <div className="portflio-item position-relative mb-4">
                <button
                  className="d-block w-100 border-0 p-0 bg-transparent"
                  onClick={() => item.image && setLightbox(item.image.sourceUrl)}
                  style={{ cursor: 'pointer' }}
                >
                  {item.image && (
                    <Image
                      src={item.image.sourceUrl}
                      alt={item.image.altText || item.title}
                      width={600}
                      height={400}
                      className="img-fluid w-100"
                      style={{ objectFit: 'cover', display: 'block' }}
                    />
                  )}
                  <i className="ti-plus overlay-item"></i>
                  <div className="portfolio-item-content">
                    <h3 className="mb-0 text-white">{item.title}</h3>
                    <p className="text-white-50">{item.category}</p>
                  </div>
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Simple Lightbox */}
      {lightbox && (
        <div
          className="position-fixed"
          style={{
            top: 0, left: 0, right: 0, bottom: 0,
            background: 'rgba(0,0,0,0.9)',
            zIndex: 9999,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
          }}
          onClick={() => setLightbox(null)}
        >
          <button
            onClick={() => setLightbox(null)}
            style={{
              position: 'absolute', top: 20, right: 30,
              background: 'none', border: 'none', color: '#fff',
              fontSize: 40, cursor: 'pointer',
            }}
          >
            &times;
          </button>
          <Image
            src={lightbox}
            alt="Portfolio"
            width={900}
            height={600}
            style={{ maxWidth: '90vw', maxHeight: '85vh', objectFit: 'contain' }}
          />
        </div>
      )}
    </section>
  );
}
