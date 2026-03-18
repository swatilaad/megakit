import FlexibleSections from '@/components/sections/FlexibleSections';
import PageTitle from '@/components/sections/PageTitle';
import WordPressError from '@/components/ui/WordPressError';
import { getPage } from '@/lib/wordpress';
import type { FlexSection } from '@/types/wordpress';

export const metadata = {
  title: 'Contact | Megakit',
  description: 'Get in touch with our team.',
};

export default async function ContactPage() {
  const result = await getPage('contact');

  if (result.error) {
    return (
      <div className="main-wrapper">
        <PageTitle
          subtitle="Contact Us"
          heading="Get in Touch"
          breadcrumbs={[{ label: 'Home', href: '/' }, { label: 'Contact Us' }]}
        />
        <WordPressError error={result.error} pageName="Contact" />
      </div>
    );
  }

  const sections = (result.data?.pageFields?.sections ?? []) as FlexSection[];

  return (
    <div className="main-wrapper">
      <FlexibleSections sections={sections} />

      {/* Map placeholder */}
      <div className="google-map">
        <div
          id="map"
          style={{
            width: '100%',
            height: 450,
            background: '#e8e8e8',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
          }}
        >
          <p className="text-muted">
            Map integration requires a Google Maps API key.{' '}
            <a
              href="https://developers.google.com/maps/documentation/javascript/get-api-key"
              target="_blank"
              rel="noreferrer"
            >
              Get API key
            </a>
          </p>
        </div>
      </div>
    </div>
  );
}
