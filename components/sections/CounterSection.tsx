'use client';

import { useEffect, useState } from 'react';
import { useInView } from 'react-intersection-observer';
import CountUp from 'react-countup';
import type { CounterSection as CounterSectionType } from '@/types/wordpress';

interface CounterSectionProps {
  data?: CounterSectionType | null;
  dark?: boolean;
}

export default function CounterSection({ data, dark = false }: CounterSectionProps) {
  const { ref, inView } = useInView({ threshold: 0.3, triggerOnce: true });
  const [started, setStarted] = useState(false);

  useEffect(() => {
    if (inView) setStarted(true);
  }, [inView]);

  if (!data?.items?.length) return null;

  const items = data.items;

  return (
    <section className={`section counter${dark ? ' bg-counter' : ''}`} ref={ref}>
      <div className="container">
        <div className="row">
          {items.map((item, i) => (
            <div key={i} className="col-lg-3 col-md-6 col-sm-6">
              <div
                className={`counter-item text-center${i < items.length - 1 ? ' mb-5 mb-lg-0' : ''}`}
              >
                {dark && item.icon && (
                  <i className={`${item.icon} color-one text-md`}></i>
                )}
                <h3 className={`${dark ? 'mt-2 mb-0 text-white' : 'mb-0'}`}>
                  <span className="counter-stat font-weight-bold">
                    {started ? (
                      <CountUp
                        end={parseInt(item.value.replace(/[^0-9]/g, ''), 10) || 0}
                        duration={2.5}
                        separator=","
                      />
                    ) : (
                      '0'
                    )}
                  </span>{' '}
                  {item.suffix}
                </h3>
                <p className={dark ? 'text-white-50' : 'text-muted'}>{item.label}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
