import FlexibleSections from '@/components/sections/FlexibleSections';
import PageTitle from '@/components/sections/PageTitle';
import WordPressError from '@/components/ui/WordPressError';
import { getPage } from '@/lib/wordpress';
import type { FlexSection } from '@/types/wordpress';

export const metadata = {
  title: 'About Us | Megakit',
  description: 'Learn about our creative and dynamic team.',
};

export default async function AboutPage() {
  const result = await getPage('about');

  if (result.error) {
    return (
      <div className="main-wrapper">
        <PageTitle
          subtitle="About Us"
          heading="Our Company"
          breadcrumbs={[{ label: 'Home', href: '/' }, { label: 'About Us' }]}
        />
        <WordPressError error={result.error} pageName="About" />
      </div>
    );
  }

  const sections = (result.data?.pageFields?.sections ?? []) as FlexSection[];

  return (
    <div className="main-wrapper">
      <FlexibleSections sections={sections} counterDark />
    </div>
  );
}
