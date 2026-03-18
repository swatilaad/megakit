import FlexibleSections from '@/components/sections/FlexibleSections';
import PageTitle from '@/components/sections/PageTitle';
import WordPressError from '@/components/ui/WordPressError';
import { getPage } from '@/lib/wordpress';
import type { FlexSection } from '@/types/wordpress';

export const metadata = {
  title: 'Services | Megakit',
  description: 'We provide a wide range of creative services.',
};

export default async function ServicesPage() {
  const result = await getPage('services');

  if (result.error) {
    return (
      <div className="main-wrapper">
        <PageTitle
          subtitle="Our Services"
          heading="What We Do"
          breadcrumbs={[{ label: 'Home', href: '/' }, { label: 'Our Services' }]}
        />
        <WordPressError error={result.error} pageName="Services" />
      </div>
    );
  }

  const sections = (result.data?.pageFields?.sections ?? []) as FlexSection[];

  return (
    <div className="main-wrapper">
      <FlexibleSections sections={sections} />
    </div>
  );
}
