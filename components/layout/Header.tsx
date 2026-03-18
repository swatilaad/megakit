'use client';

import { useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import type { GlobalSettings } from '@/types/wordpress';

interface HeaderProps {
  settings?: GlobalSettings | null;
}

const defaultNav = [
  { label: 'Home', href: '/', children: [] },
  {
    label: 'About',
    href: '#',
    children: [
      { label: 'Our Company', href: '/about' },
      { label: 'Pricing', href: '/pricing' },
    ],
  },
  { label: 'Services', href: '/services', children: [] },
  { label: 'Portfolio', href: '/portfolio', children: [] },
  {
    label: 'Blog',
    href: '#',
    children: [
      { label: 'Blog Grid', href: '/blog' },
      { label: 'Blog Single', href: '/blog/sample-post' },
    ],
  },
  { label: 'Contact', href: '/contact', children: [] },
];

export default function Header({ settings }: HeaderProps) {
  const [navOpen, setNavOpen] = useState(false);
  const [openDropdown, setOpenDropdown] = useState<string | null>(null);
  const pathname = usePathname();

  const phone = settings?.phone || '+23-345-67890';
  const email = settings?.email || 'support@gmail.com';
  const facebook = settings?.facebookUrl || '#';
  const twitter = settings?.twitterUrl || '#';
  const github = settings?.githubUrl || '#';
  const siteName = settings?.siteName || 'Megakit';

  return (
    <header className="navigation">
      {/* Top Bar */}
      <div className="header-top">
        <div className="container">
          <div className="row justify-content-between align-items-center">
            <div className="col-lg-2 col-md-4">
              <div className="header-top-socials text-center text-lg-left text-md-left">
                <a href={facebook} target="_blank" rel="noreferrer">
                  <i className="ti-facebook"></i>
                </a>
                <a href={twitter} target="_blank" rel="noreferrer">
                  <i className="ti-twitter"></i>
                </a>
                <a href={github} target="_blank" rel="noreferrer">
                  <i className="ti-github"></i>
                </a>
              </div>
            </div>
            <div className="col-lg-10 col-md-8 text-center text-lg-right text-md-right">
              <div className="header-top-info">
                <a href={`tel:${phone}`}>
                  Call Us : <span>{phone}</span>
                </a>
                <a href={`mailto:${email}`}>
                  <i className="fa fa-envelope mr-2"></i>
                  <span>{email}</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Main Navbar */}
      <nav className="navbar navbar-expand-lg py-4" id="navbar">
        <div className="container">
          <Link className="navbar-brand" href="/">
            {siteName.slice(0, -3)}
            <span>{siteName.slice(-3)}.</span>
          </Link>

          <button
            className={`navbar-toggler${navOpen ? '' : ' collapsed'}`}
            type="button"
            onClick={() => setNavOpen(!navOpen)}
            aria-label="Toggle navigation"
          >
            <span className="fa fa-bars"></span>
          </button>

          <div className={`${navOpen ? '' : 'collapse '}navbar-collapse text-center`} id="navbarsExample09">
            <ul className="navbar-nav ml-auto">
              {defaultNav.map((item) => (
                <li
                  key={item.label}
                  className={`nav-item${item.children?.length ? ' dropdown' : ''}${pathname === item.href ? ' active' : ''}`}
                  onMouseEnter={() => item.children?.length && setOpenDropdown(item.label)}
                  onMouseLeave={() => setOpenDropdown(null)}
                >
                  {item.children?.length ? (
                    <>
                      <a
                        className="nav-link dropdown-toggle"
                        href={item.href}
                        onClick={(e) => e.preventDefault()}
                      >
                        {item.label}
                      </a>
                      <ul
                        className="dropdown-menu"
                        style={{
                          visibility: openDropdown === item.label ? 'visible' : 'hidden',
                          opacity: openDropdown === item.label ? 1 : 0,
                        }}
                      >
                        {item.children.map((child) => (
                          <li key={child.label}>
                            <Link
                              className="dropdown-item"
                              href={child.href}
                              onClick={() => setNavOpen(false)}
                            >
                              {child.label}
                            </Link>
                          </li>
                        ))}
                      </ul>
                    </>
                  ) : (
                    <Link
                      className="nav-link"
                      href={item.href}
                      onClick={() => setNavOpen(false)}
                    >
                      {item.label}
                    </Link>
                  )}
                </li>
              ))}
            </ul>

            <form className="form-lg-inline my-2 my-md-0 ml-lg-4 text-center">
              <Link href="/contact" className="btn btn-solid-border btn-round-full">
                Get a Quote
              </Link>
            </form>
          </div>
        </div>
      </nav>
    </header>
  );
}
