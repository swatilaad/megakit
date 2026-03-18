import Link from 'next/link';

interface Breadcrumb {
  label: string;
  href?: string;
}

interface PageTitleProps {
  subtitle: string;
  heading: string;
  breadcrumbs?: Breadcrumb[];
}

export default function PageTitle({ subtitle, heading, breadcrumbs = [] }: PageTitleProps) {
  return (
    <section className="page-title bg-1">
      <div className="container">
        <div className="row">
          <div className="col-md-12">
            <div className="block text-center">
              <span className="text-white">{subtitle}</span>
              <h1 className="text-capitalize mb-4 text-lg">{heading}</h1>
              <ul className="list-inline">
                {breadcrumbs.map((crumb, i) => (
                  <li className="list-inline-item" key={i}>
                    {crumb.href ? (
                      <Link href={crumb.href} className="text-white">
                        {crumb.label}
                      </Link>
                    ) : (
                      <a href="#" className="text-white-50">
                        {crumb.label}
                      </a>
                    )}
                    {i < breadcrumbs.length - 1 && (
                      <span className="text-white mx-2">/</span>
                    )}
                  </li>
                ))}
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
