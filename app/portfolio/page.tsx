import FlexibleSections from '@/components/sections/FlexibleSections';
import PageTitle from '@/components/sections/PageTitle';
import WordPressError from '@/components/ui/WordPressError';
import { getPortfolioPageData } from '@/lib/wordpress';
import type { FlexSection } from '@/types/wordpress';

export const metadata = {
  title: 'Portfolio | Megakit',
  description: 'View our latest portfolio and projects.',
};

export default async function PortfolioPage() {
  const result = await getPortfolioPageData();

  if (result.error) {
    return (
      <div className="main-wrapper">
        <PageTitle
          subtitle="Latest Works"
          heading="Portfolio"
          breadcrumbs={[{ label: 'Home', href: '/' }, { label: 'Latest Works' }]}
        />
        <WordPressError error={result.error} pageName="Portfolio" />
      </div>
    );
  }

  const sections = (result.data?.page?.pageFields?.sections ?? []) as FlexSection[];
  const portfolioItems = result.data?.portfolioItems?.nodes ?? [];

  return (
    <div className="main-wrapper">
      <FlexibleSections sections={sections} portfolioItems={portfolioItems} />
    </div>
  );
}
