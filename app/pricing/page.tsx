import FlexibleSections from '@/components/sections/FlexibleSections';
import PageTitle from '@/components/sections/PageTitle';
import WordPressError from '@/components/ui/WordPressError';
import { getPage } from '@/lib/wordpress';
import type { FlexSection } from '@/types/wordpress';

export const metadata = {
  title: 'Pricing | Megakit',
  description: 'Choose from our flexible pricing plans.',
};

export default async function PricingPage() {
  const result = await getPage('pricing');

  if (result.error) {
    return (
      <div className="main-wrapper">
        <PageTitle
          subtitle="Our Pricing"
          heading="Pricing Package"
          breadcrumbs={[{ label: 'Home', href: '/' }, { label: 'Our Pricing' }]}
        />
        <WordPressError error={result.error} pageName="Pricing" />
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
