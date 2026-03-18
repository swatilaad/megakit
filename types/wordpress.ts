// WordPress / ACF TypeScript types

export interface MediaItem {
  sourceUrl: string;
  altText?: string;
  title?: string;
}

export interface MenuItem {
  label: string;
  url: string;
  children?: MenuItem[];
}

// Global Settings (Options Page)
export interface GlobalSettings {
  siteLogo: { node: MediaItem } | null;
  siteName: string;
  tagline: string;
  phone: string;
  email: string;
  address: string;
  facebookUrl: string;
  twitterUrl: string;
  githubUrl: string;
  linkedinUrl: string;
  copyrightText: string;
  footerCompanyLinks: FooterLink[];
  footerQuickLinks: FooterLink[];
  footerSubscribeText: string;
}

export interface FooterLink {
  label: string;
  url: string;
}

// ─── Flexible Content Layout Types ────────────────────────────────────────────
// fieldGroupName is returned by WPGraphQL for each flex layout row.
// Format: "Page_Pagefields_Sections_{LayoutName}" (e.g. "Page_Pagefields_Sections_Hero")

export interface FlexBase {
  fieldGroupName: string;
}

// ACF image fields from WPGraphQL return { node: MediaItem } — not MediaItem directly
export interface AcfImage {
  node: MediaItem;
}

export interface HeroLayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsHeroLayout';
  tagline?: string;
  heading?: string;
  buttonLabel?: string;
  buttonUrl?: string;
  backgroundImage?: AcfImage | null;
}

export interface IntroFeaturesLayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsIntroFeaturesLayout';
  subtitle?: string;
  heading?: string;
  features?: IntroFeature[];
}

export interface AboutHomeLayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsAboutHomeLayout';
  subtitle?: string;
  heading?: string;
  subheading?: string;
  description?: string;
  buttonLabel?: string;
  buttonUrl?: string;
  backgroundImage?: AcfImage | null;
}

export interface AboutDetailLayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsAboutDetailLayout';
  subtitle?: string;
  heading?: string;
  description?: string;
  buttonLabel?: string;
  buttonUrl?: string;
  image?: AcfImage | null;
  infoItems?: AboutInfoItem[];
}

export interface CounterLayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsCounterLayout';
  items?: CounterItem[];
}

export interface ServicesLayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsServicesLayout';
  subtitle?: string;
  heading?: string;
  services?: ServiceItem[];
}

export interface CTALayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsCtaLayout';
  subtitle?: string;
  heading?: string;
  description?: string;
  phone?: string;
  // no backgroundImage in this schema
}

export interface TestimonialsLayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsTestimonialsLayout';
  subtitle?: string;
  heading?: string;
  testimonials?: TestimonialItem[];
}

export interface LatestBlogLayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsLatestBlogLayout';
  subtitle?: string;
  heading?: string;
}

export interface CTABlockLayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsCtaBlockLayout';
  subtitle?: string;
  heading?: string;
  buttonLabel?: string;
  buttonUrl?: string;
}

export interface TeamMemberRaw {
  name: string;
  role: string;
  photo?: AcfImage | null;
  facebookUrl?: string;
  twitterUrl?: string;
  instagramUrl?: string;
  linkedinUrl?: string;
}

export interface TeamLayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsTeamLayout';
  subtitle?: string;
  heading?: string;
  members?: TeamMemberRaw[];
}

export interface PortfolioLayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsPortfolioLayout';
  subtitle?: string;
  heading?: string;
}

export interface PricingLayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsPricingLayout';
  subtitle?: string;
  heading?: string;
  plans?: PricingPlan[];
}

export interface ContactInfoLayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsContactInfoLayout';
  address?: string;
  email?: string;
  phone?: string;
  facebookUrl?: string;
  twitterUrl?: string;
  linkedinUrl?: string;
}

export interface PageTitleLayout extends FlexBase {
  fieldGroupName: 'PageFieldsSectionsPageTitleLayout';
  subtitle?: string;
  heading?: string;
  breadcrumbLabel?: string | null;
}

export type FlexSection =
  | HeroLayout
  | IntroFeaturesLayout
  | AboutHomeLayout
  | AboutDetailLayout
  | CounterLayout
  | ServicesLayout
  | CTALayout
  | TestimonialsLayout
  | LatestBlogLayout
  | CTABlockLayout
  | TeamLayout
  | PortfolioLayout
  | PricingLayout
  | ContactInfoLayout
  | PageTitleLayout;

// ─── Shared Field Types ────────────────────────────────────────────────────────

export interface IntroFeature {
  icon: string;
  title: string;
  description: string;
}

export interface CounterItem {
  value: string;
  suffix: string;
  label: string;
  icon?: string;
}

export interface ServiceItem {
  icon: string;
  title: string;
  description: string;
}

export interface TestimonialItem {
  text: string;
  authorName: string;
  authorRole: string;
}

export interface TeamMember {
  id?: string;
  name: string;
  role: string;
  photo?: MediaItem;
  facebookUrl?: string;
  twitterUrl?: string;
  instagramUrl?: string;
  linkedinUrl?: string;
}

export interface PricingPlan {
  name: string;
  price: string;
  period: string;
  features: string[];
  buttonLabel: string;
  buttonUrl: string;
  highlighted: boolean;
}

export interface AboutInfoItem {
  number: string;
  title: string;
  description: string;
}

// ─── Section interfaces (kept for backward compat in section components) ──────

export interface HeroSection {
  tagline: string;
  heading: string;
  buttonLabel: string;
  buttonUrl: string;
  backgroundImage?: MediaItem;
}

export interface IntroSection {
  subtitle: string;
  heading: string;
  features: IntroFeature[];
}

export interface AboutSection {
  subtitle: string;
  heading: string;
  subheading: string;
  description: string;
  buttonLabel: string;
  buttonUrl: string;
  backgroundImage?: MediaItem;
}

export interface CounterSection {
  items: CounterItem[];
}

export interface ServicesSection {
  subtitle: string;
  heading: string;
  services: ServiceItem[];
}

export interface CTASection {
  subtitle: string;
  heading: string;
  description: string;
  phone: string;
  backgroundImage?: MediaItem;
}

export interface CTABlock2 {
  subtitle: string;
  heading: string;
  buttonLabel: string;
  buttonUrl: string;
}

export interface TestimonialsSection {
  subtitle: string;
  heading: string;
  testimonials: TestimonialItem[];
}

// ─── Blog Post ─────────────────────────────────────────────────────────────────

export interface BlogPost {
  id: string;
  slug: string;
  title: string;
  excerpt: string;
  date: string;
  featuredImage?: MediaItem;
  categories?: { name: string }[];
  author?: { name: string };
  content?: string;
  tags?: { name: string }[];
}

// ─── Portfolio ─────────────────────────────────────────────────────────────────

export interface PortfolioItem {
  id: string;
  title: string;
  category: string;
  image?: MediaItem;
}

// ─── Contact ───────────────────────────────────────────────────────────────────

export interface ContactPage {
  address: string;
  email: string;
  phone: string;
  facebookUrl: string;
  twitterUrl: string;
  linkedinUrl: string;
  mapApiKey?: string;
}

// ─── Page (generic) ───────────────────────────────────────────────────────────

export interface Page {
  title: string;
  slug: string;
  content?: string;
  featuredImage?: MediaItem;
  seo?: {
    title?: string;
    description?: string;
  };
}

// ─── Page with Flexible Sections ─────────────────────────────────────────────

export interface PageData {
  title: string;
  slug: string;
  pageFields?: {
    sections?: FlexSection[];
  };
  // Portfolio items come separately via a second query field
  portfolioItems?: {
    nodes: Array<{
      id: string;
      title: string;
      portfolioFields?: {
        category?: string;
        image?: MediaItem;
      };
    }>;
  };
}

// ─── Home Page (legacy – kept for reference) ──────────────────────────────────

export interface HomePage {
  hero: HeroSection;
  intro: IntroSection;
  about: AboutSection;
  counter: CounterSection;
  services: ServicesSection;
  cta: CTASection;
  testimonials: TestimonialsSection;
  ctaBlock2: CTABlock2;
}

// ─── API Response ─────────────────────────────────────────────────────────────

export interface WordPressApiResponse<T> {
  data: T | null;
  error: string | null;
}
