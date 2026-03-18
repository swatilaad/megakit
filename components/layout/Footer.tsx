'use client';

import Link from 'next/link';
import type { GlobalSettings } from '@/types/wordpress';

interface FooterProps {
  settings?: GlobalSettings | null;
}

const defaultCompanyLinks = [
  { label: 'Terms & Conditions', url: '#' },
  { label: 'Privacy Policy', url: '#' },
  { label: 'Support', url: '#' },
  { label: 'FAQ', url: '#' },
];

const defaultQuickLinks = [
  { label: 'About', url: '/about' },
  { label: 'Services', url: '/services' },
  { label: 'Team', url: '/about' },
  { label: 'Contact', url: '/contact' },
];

export default function Footer({ settings }: FooterProps) {
  const siteName = settings?.siteName || 'Megakit';
  const email = settings?.email || 'Support@megakit.com';
  const phone = settings?.phone || '+23-456-6588';
  const companyLinks = settings?.footerCompanyLinks?.length ? settings.footerCompanyLinks : defaultCompanyLinks;
  const quickLinks = settings?.footerQuickLinks?.length ? settings.footerQuickLinks : defaultQuickLinks;
  const subscribeText = settings?.footerSubscribeText || 'Subscribe to get latest news article and resources';
  const copyright = settings?.copyrightText || `© Copyright Reserved to ${siteName}.`;
  const facebook = settings?.facebookUrl || '#';
  const twitter = settings?.twitterUrl || '#';
  const linkedin = settings?.linkedinUrl || '#';

  return (
    <footer className="footer section">
      <div className="container">
        <div className="row">
          {/* Company Links */}
          <div className="col-lg-3 col-md-6 col-sm-6">
            <div className="widget">
              <h4 className="text-capitalize mb-4">Company</h4>
              <ul className="list-unstyled footer-menu lh-35">
                {companyLinks.map((link) => (
                  <li key={link.label}>
                    <Link href={link.url}>{link.label}</Link>
                  </li>
                ))}
              </ul>
            </div>
          </div>

          {/* Quick Links */}
          <div className="col-lg-2 col-md-6 col-sm-6">
            <div className="widget">
              <h4 className="text-capitalize mb-4">Quick Links</h4>
              <ul className="list-unstyled footer-menu lh-35">
                {quickLinks.map((link) => (
                  <li key={link.label}>
                    <Link href={link.url}>{link.label}</Link>
                  </li>
                ))}
              </ul>
            </div>
          </div>

          {/* Subscribe */}
          <div className="col-lg-3 col-md-6 col-sm-6">
            <div className="widget">
              <h4 className="text-capitalize mb-4">Subscribe Us</h4>
              <p>{subscribeText}</p>
              <form action="#" className="sub-form" onSubmit={(e) => e.preventDefault()}>
                <input type="email" className="form-control mb-3" placeholder="Subscribe Now ..." />
                <button type="submit" className="btn btn-main btn-small">
                  Subscribe
                </button>
              </form>
            </div>
          </div>

          {/* Brand */}
          <div className="col-lg-3 ml-auto col-sm-6">
            <div className="widget">
              <div className="logo mb-4">
                <h3>
                  {siteName.slice(0, -3)}
                  <span>{siteName.slice(-3)}.</span>
                </h3>
              </div>
              <h6>
                <a href={`mailto:${email}`}>{email}</a>
              </h6>
              <a href={`tel:${phone}`}>
                <span className="text-color h4">{phone}</span>
              </a>
            </div>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="footer-btm pt-4">
          <div className="row">
            <div className="col-lg-4 col-md-12 col-sm-12">
              <div className="copyright" dangerouslySetInnerHTML={{ __html: copyright }} />
            </div>
            <div className="col-lg-4 col-md-12 col-sm-12">
              <div className="copyright">
                Distributed by{' '}
                <a href="https://themewagon.com/" target="_blank" rel="noreferrer">
                  Themewagon
                </a>
              </div>
            </div>
            <div className="col-lg-4 col-md-12 col-sm-12 text-left text-lg-left">
              <ul className="list-inline footer-socials">
                <li className="list-inline-item">
                  <a href={facebook} target="_blank" rel="noreferrer">
                    <i className="ti-facebook mr-2"></i>Facebook
                  </a>
                </li>
                <li className="list-inline-item">
                  <a href={twitter} target="_blank" rel="noreferrer">
                    <i className="ti-twitter mr-2"></i>Twitter
                  </a>
                </li>
                <li className="list-inline-item">
                  <a href={linkedin} target="_blank" rel="noreferrer">
                    <i className="ti-linkedin mr-2"></i>Linkedin
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}
