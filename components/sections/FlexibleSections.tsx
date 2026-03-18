import type {
  FlexSection,
  HeroLayout,
  IntroFeaturesLayout,
  AboutHomeLayout,
  AboutDetailLayout,
  CounterLayout,
  ServicesLayout,
  CTALayout,
  TestimonialsLayout,
  LatestBlogLayout,
  CTABlockLayout,
  TeamLayout,
  PortfolioLayout,
  PricingLayout,
  ContactInfoLayout,
  PageTitleLayout,
  PortfolioItem,
  BlogPost,
  MediaItem,
  AcfImage,
} from '@/types/wordpress';

import HeroSection from './HeroSection';
import IntroSection from './IntroSection';
import AboutSection from './AboutSection';
import AboutSection2 from './AboutSection2';
import CounterSection from './CounterSection';
import ServicesSection from './ServicesSection';
import CTASection from './CTASection';
import TestimonialsSection from './TestimonialsSection';
import LatestBlogSection from './LatestBlogSection';
import CTABlock2 from './CTABlock2';
import TeamSection from './TeamSection';
import PortfolioSection from './PortfolioSection';
import PricingSection from './PricingSection';
import ContactSection from './ContactSection';
import PageTitle from './PageTitle';

interface FlexibleSectionsProps {
  sections: FlexSection[];
  blogPosts?: BlogPost[];
  portfolioItems?: PortfolioItem[];
  counterDark?: boolean;
}

// Extract the layout name from the fieldGroupName string.
// WPGraphQL ACF returns: "PageFieldsSectionsHeroLayout"
// Strip prefix "PageFieldsSections" and suffix "Layout" → "Hero"
function layoutName(fieldGroupName: string): string {
  return fieldGroupName
    .replace(/^PageFieldsSections/, '')
    .replace(/Layout$/, '');
}

// Unwrap ACF image field { node: { sourceUrl, altText } } → MediaItem
function img(acf?: AcfImage | null): MediaItem | undefined {
  if (!acf?.node?.sourceUrl) return undefined;
  return { sourceUrl: acf.node.sourceUrl, altText: acf.node.altText ?? '' };
}

export default function FlexibleSections({
  sections,
  blogPosts = [],
  portfolioItems = [],
  counterDark = false,
}: FlexibleSectionsProps) {
  let darkUsed = false;

  return (
    <>
      {sections.map((section, idx) => {
        const name = layoutName(section.fieldGroupName);

        switch (name) {
          case 'Hero': {
            const s = section as HeroLayout;
            return (
              <HeroSection
                key={idx}
                data={{
                  tagline:         s.tagline ?? '',
                  heading:         s.heading ?? '',
                  buttonLabel:     s.buttonLabel ?? '',
                  buttonUrl:       s.buttonUrl ?? '',
                  backgroundImage: img(s.backgroundImage),
                }}
              />
            );
          }

          case 'IntroFeatures': {
            const s = section as IntroFeaturesLayout;
            return (
              <IntroSection
                key={idx}
                data={{
                  subtitle: s.subtitle ?? '',
                  heading:  s.heading ?? '',
                  features: s.features ?? [],
                }}
              />
            );
          }

          case 'AboutHome': {
            const s = section as AboutHomeLayout;
            return (
              <AboutSection
                key={idx}
                data={{
                  subtitle:        s.subtitle ?? '',
                  heading:         s.heading ?? '',
                  subheading:      s.subheading ?? '',
                  description:     s.description ?? '',
                  buttonLabel:     s.buttonLabel ?? '',
                  buttonUrl:       s.buttonUrl ?? '',
                  backgroundImage: img(s.backgroundImage),
                }}
              />
            );
          }

          case 'AboutDetail': {
            const s = section as AboutDetailLayout;
            return (
              <AboutSection2
                key={idx}
                subtitle={s.subtitle}
                heading={s.heading}
                description={s.description}
                buttonLabel={s.buttonLabel}
                buttonUrl={s.buttonUrl}
                image={img(s.image) ?? null}
                infoItems={s.infoItems ?? []}
              />
            );
          }

          case 'Counter': {
            const s = section as CounterLayout;
            const useDark = counterDark && !darkUsed;
            if (counterDark) darkUsed = true;
            return (
              <CounterSection
                key={idx}
                data={{ items: s.items ?? [] }}
                dark={useDark}
              />
            );
          }

          case 'Services': {
            const s = section as ServicesLayout;
            return (
              <ServicesSection
                key={idx}
                data={{
                  subtitle: s.subtitle ?? '',
                  heading:  s.heading ?? '',
                  services: s.services ?? [],
                }}
              />
            );
          }

          case 'Cta': {
            const s = section as CTALayout;
            return (
              <CTASection
                key={idx}
                data={{
                  subtitle:    s.subtitle ?? '',
                  heading:     s.heading ?? '',
                  description: s.description ?? '',
                  phone:       s.phone ?? '',
                }}
              />
            );
          }

          case 'Testimonials': {
            const s = section as TestimonialsLayout;
            return (
              <TestimonialsSection
                key={idx}
                data={{
                  subtitle:     s.subtitle ?? '',
                  heading:      s.heading ?? '',
                  testimonials: s.testimonials ?? [],
                }}
              />
            );
          }

          case 'LatestBlog': {
            const s = section as LatestBlogLayout;
            return (
              <LatestBlogSection
                key={idx}
                posts={blogPosts.length ? blogPosts : undefined}
                subtitle={s.subtitle}
                heading={s.heading}
              />
            );
          }

          case 'CtaBlock': {
            const s = section as CTABlockLayout;
            return (
              <CTABlock2
                key={idx}
                data={{
                  subtitle:    s.subtitle ?? '',
                  heading:     s.heading ?? '',
                  buttonLabel: s.buttonLabel ?? '',
                  buttonUrl:   s.buttonUrl ?? '',
                }}
              />
            );
          }

          case 'Team': {
            const s = section as TeamLayout;
            const members = (s.members ?? []).map((m) => ({
              id:           m.name,
              name:         m.name,
              role:         m.role,
              photo:        img(m.photo),
              facebookUrl:  m.facebookUrl,
              twitterUrl:   m.twitterUrl,
              instagramUrl: m.instagramUrl,
              linkedinUrl:  m.linkedinUrl,
            }));
            return (
              <TeamSection
                key={idx}
                subtitle={s.subtitle}
                heading={s.heading}
                members={members}
              />
            );
          }

          case 'Portfolio': {
            const s = section as PortfolioLayout;
            return (
              <PortfolioSection
                key={idx}
                subtitle={s.subtitle}
                heading={s.heading}
                items={portfolioItems.length ? portfolioItems : undefined}
              />
            );
          }

          case 'Pricing': {
            const s = section as PricingLayout;
            // ACF stores features as a newline-delimited textarea — convert to string[]
            const plans = (s.plans ?? []).map((p) => ({
              ...p,
              features: Array.isArray(p.features)
                ? p.features
                : String(p.features ?? '').split('\n').map((f) => f.trim()).filter(Boolean),
            }));
            return (
              <PricingSection
                key={idx}
                subtitle={s.subtitle}
                heading={s.heading}
                plans={plans}
              />
            );
          }

          case 'ContactInfo': {
            const s = section as ContactInfoLayout;
            return (
              <ContactSection
                key={idx}
                data={{
                  address:     s.address ?? '',
                  email:       s.email ?? '',
                  phone:       s.phone ?? '',
                  facebookUrl: s.facebookUrl ?? '',
                  twitterUrl:  s.twitterUrl ?? '',
                  linkedinUrl: s.linkedinUrl ?? '',
                }}
              />
            );
          }

          case 'PageTitle': {
            const s = section as PageTitleLayout;
            // breadcrumbLabel is a single optional override for the last crumb
            const lastLabel = s.breadcrumbLabel || s.heading || '';
            return (
              <PageTitle
                key={idx}
                subtitle={s.subtitle ?? ''}
                heading={s.heading ?? ''}
                breadcrumbs={[
                  { label: 'Home', href: '/' },
                  { label: lastLabel },
                ]}
              />
            );
          }

          default:
            if (process.env.NODE_ENV === 'development') {
              console.warn(`[FlexibleSections] Unknown layout: "${name}" (${section.fieldGroupName})`);
            }
            return null;
        }
      })}
    </>
  );
}
