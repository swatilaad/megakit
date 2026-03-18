// ─── Global Settings (Options Page) ────────────────────────────────────────
export const GET_GLOBAL_SETTINGS = `
  query GetGlobalSettings {
    globalSettings {
      globalSettingsFields {
        siteName
        tagline
        siteLogo {
          node {
            sourceUrl
            altText
          }
        }
        phone
        email
        address
        facebookUrl
        twitterUrl
        githubUrl
        linkedinUrl
        copyrightText
        footerSubscribeText
        footerCompanyLinks {
          label
          url
        }
        footerQuickLinks {
          label
          url
        }
      }
    }
  }
`;

// ─── Single Page by URI with Flexible Content Sections ───────────────────────
// ACF image fields return AcfMediaItemConnectionEdge — must use node { ... }
// CTA layout has no backgroundImage field in this schema
// PageTitle uses breadcrumbLabel (single text), not a repeater
export const GET_PAGE_BY_SLUG = `
  query GetPageBySlug($slug: ID!) {
    page(id: $slug, idType: URI) {
      title
      slug
      pageFields {
        sections {
          fieldGroupName
          ... on PageFieldsSectionsHeroLayout {
            tagline
            heading
            buttonLabel
            buttonUrl
            backgroundImage {
              node {
                sourceUrl
                altText
              }
            }
          }
          ... on PageFieldsSectionsIntroFeaturesLayout {
            subtitle
            heading
            features {
              icon
              title
              description
            }
          }
          ... on PageFieldsSectionsAboutHomeLayout {
            subtitle
            heading
            subheading
            description
            buttonLabel
            buttonUrl
            backgroundImage {
              node {
                sourceUrl
                altText
              }
            }
          }
          ... on PageFieldsSectionsAboutDetailLayout {
            subtitle
            heading
            description
            buttonLabel
            buttonUrl
            image {
              node {
                sourceUrl
                altText
              }
            }
            infoItems {
              number
              title
              description
            }
          }
          ... on PageFieldsSectionsCounterLayout {
            items {
              value
              suffix
              label
              icon
            }
          }
          ... on PageFieldsSectionsServicesLayout {
            subtitle
            heading
            services {
              icon
              title
              description
            }
          }
          ... on PageFieldsSectionsCtaLayout {
            subtitle
            heading
            description
            phone
          }
          ... on PageFieldsSectionsTestimonialsLayout {
            subtitle
            heading
            testimonials {
              text
              authorName
              authorRole
            }
          }
          ... on PageFieldsSectionsLatestBlogLayout {
            subtitle
            heading
          }
          ... on PageFieldsSectionsCtaBlockLayout {
            subtitle
            heading
            buttonLabel
            buttonUrl
          }
          ... on PageFieldsSectionsTeamLayout {
            subtitle
            heading
            members {
              name
              role
              facebookUrl
              twitterUrl
              instagramUrl
              linkedinUrl
              photo {
                node {
                  sourceUrl
                  altText
                }
              }
            }
          }
          ... on PageFieldsSectionsPortfolioLayout {
            subtitle
            heading
          }
          ... on PageFieldsSectionsPricingLayout {
            subtitle
            heading
            plans {
              name
              price
              period
              features
              buttonLabel
              buttonUrl
              highlighted
            }
          }
          ... on PageFieldsSectionsContactInfoLayout {
            address
            email
            phone
            facebookUrl
            twitterUrl
            linkedinUrl
          }
          ... on PageFieldsSectionsPageTitleLayout {
            subtitle
            heading
            breadcrumbLabel
          }
        }
      }
    }
  }
`;

// ─── Portfolio Items (CPT) ────────────────────────────────────────────────────
export const GET_PORTFOLIO_ITEMS = `
  query GetPortfolioItems {
    portfolioItems(first: 12) {
      nodes {
        id
        title
        portfolioFields {
          category
          image {
            node {
              sourceUrl
              altText
            }
          }
        }
      }
    }
  }
`;

// ─── Blog Posts ──────────────────────────────────────────────────────────────
export const GET_BLOG_POSTS = `
  query GetBlogPosts($first: Int = 9, $after: String) {
    posts(first: $first, after: $after, where: { status: PUBLISH }) {
      pageInfo {
        hasNextPage
        endCursor
      }
      nodes {
        id
        slug
        title
        excerpt
        date
        categories {
          nodes {
            name
          }
        }
        author {
          node {
            name
          }
        }
        featuredImage {
          node {
            sourceUrl
            altText
          }
        }
      }
    }
  }
`;

// ─── Single Blog Post ────────────────────────────────────────────────────────
export const GET_BLOG_POST = `
  query GetBlogPost($slug: ID!) {
    post(id: $slug, idType: SLUG) {
      id
      title
      content
      date
      excerpt
      categories {
        nodes {
          name
        }
      }
      tags {
        nodes {
          name
        }
      }
      author {
        node {
          name
          avatar {
            url
          }
        }
      }
      featuredImage {
        node {
          sourceUrl
          altText
        }
      }
    }
    posts(first: 3, where: { status: PUBLISH }) {
      nodes {
        id
        slug
        title
        featuredImage {
          node {
            sourceUrl
            altText
          }
        }
      }
    }
  }
`;

// ─── All Blog Slugs (for static generation) ─────────────────────────────────
export const GET_ALL_POST_SLUGS = `
  query GetAllPostSlugs {
    posts(first: 100) {
      nodes {
        slug
      }
    }
  }
`;
