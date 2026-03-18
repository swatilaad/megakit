# WordPress + Next.js Headless Setup Guide

## Overview

This project uses **Next.js** as the frontend and **WordPress** as a headless CMS via **WPGraphQL**.

---

## 1. WordPress Setup

### Required Plugins

Install and activate the following WordPress plugins:

| Plugin | Purpose |
|--------|---------|
| [WPGraphQL](https://www.wpgraphql.com/) | GraphQL API for WordPress |
| [WPGraphQL for ACF](https://github.com/wp-graphql/wpgraphql-acf) | Exposes ACF fields to GraphQL |
| [Advanced Custom Fields PRO](https://www.advancedcustomfields.com/pro/) | Custom field management |
| [Custom Post Type UI](https://wordpress.org/plugins/custom-post-type-ui/) | Register Portfolio custom post type |

---

## 2. Create Pages in WordPress

Create these pages (with the exact slugs):

| Page Title | Slug |
|-----------|------|
| Home | `home` |
| About | `about` |
| Services | `services` |
| Portfolio | `portfolio` |
| Pricing | `pricing` |
| Contact | `contact` |

---

## 3. Register Portfolio Custom Post Type

In **CPT UI**, create a new Post Type:
- **Post Type Slug**: `portfolio`
- **Plural Label**: Portfolio Items
- **Singular Label**: Portfolio Item
- Enable: Show in GraphQL ✅
- **GraphQL Single Name**: `portfolioItem`
- **GraphQL Plural Name**: `portfolioItems`

---

## 4. Create Global Settings Options Page

In your theme's `functions.php` or a plugin, add:

```php
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title'    => 'Global Settings',
        'menu_title'    => 'Global Settings',
        'menu_slug'     => 'global-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false,
        'show_in_graphql' => true,
    ));
}
```

---

## 5. Import ACF Field Groups

1. Go to **WordPress Admin → Custom Fields → Tools**
2. Under **Import Field Groups**, upload `acf-export/acf-field-groups.json`
3. Click **Import JSON**

This will create all field groups for:
- Global Settings (options page)
- Home Page
- About Page
- Services Page
- Pricing Page
- Contact Page
- Portfolio Items (custom post type)

---

## 6. Configure WPGraphQL

1. Go to **WordPress Admin → GraphQL → Settings**
2. Enable **Public Introspection** (for development)
3. Note your GraphQL endpoint: `https://yourdomain.com/graphql`

### Expose ACF Fields to GraphQL

For each ACF Field Group, ensure:
- **Show in GraphQL**: ✅ Enabled
- **GraphQL Field Name**: Will be auto-generated from the field group name

---

## 7. Connect Next.js to WordPress

1. Copy the environment example file:
   ```bash
   cp .env.local.example .env.local
   ```

2. Edit `.env.local` and set your WordPress GraphQL URL:
   ```env
   NEXT_PUBLIC_WORDPRESS_API_URL=https://yourdomain.com/graphql
   ```

3. Start the development server:
   ```bash
   npm run dev
   ```

---

## 8. Content Entry Guide

### Global Settings
Go to **WordPress Admin → Global Settings** and fill in:
- Site Name, Tagline
- Logo image
- Phone, Email, Address
- Social media URLs (Facebook, Twitter, GitHub, LinkedIn)
- Copyright text
- Footer links

### Home Page
Edit the **Home** page in WordPress and fill in all ACF sections:
- Hero (tagline, heading, button, background image)
- Intro Features (3 features with icon/title/description)
- About Section
- Counter (project stats)
- Services list
- CTA Section
- Testimonials
- Bottom CTA Block

### Blog Posts
Create regular WordPress Posts — they will automatically appear on the blog page.

### Portfolio Items
Create **Portfolio Items** (custom post type) with:
- Title
- Category (text field)
- Image

---

## 9. Local Development with Local WP

If using **Local by Flywheel** or **Lando**:
- WordPress URL: `http://megakit.local`
- GraphQL endpoint: `http://megakit.local/graphql`
- Set `NEXT_PUBLIC_WORDPRESS_API_URL=http://megakit.local/graphql` in `.env.local`

---

## 10. GraphQL Endpoint Test

Test your GraphQL connection at:
```
http://yourdomain.com/graphql
```

Sample query to test:
```graphql
query {
  generalSettings {
    title
    description
  }
}
```

---

## Troubleshooting

### "Unable to connect to WordPress" error
- Ensure WordPress is running
- Check `NEXT_PUBLIC_WORDPRESS_API_URL` in `.env.local`
- Verify WPGraphQL plugin is active
- Check CORS settings in WordPress (may need to add headers for localhost)

### ACF fields not showing in GraphQL
- Open each ACF field group → enable "Show in GraphQL"
- Re-save the field group
- Install WPGraphQL for ACF plugin

### Images not loading
- In `next.config.ts`, the `remotePatterns` allows all domains
- Ensure WordPress media URLs are accessible from the Next.js server
