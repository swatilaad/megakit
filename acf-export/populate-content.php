<?php
/**
 * Megakit – WordPress Content Population Script
 *
 * Run via WP-CLI from your WordPress root:
 *   wp eval-file /path/to/populate-content.php
 *
 * This script:
 *  1. Creates all required pages (home, about, services, portfolio, pricing, contact)
 *  2. Populates each page's flexible content sections with real template content
 *  3. Sets Global Settings options (phone, email, footer links, social URLs, copyright)
 *  4. Creates 6 portfolio CPT items
 *  5. Creates 4 sample blog posts
 *
 * IMPORTANT: Run AFTER importing acf-field-groups.json via ACF → Tools → Import.
 * IMPORTANT: Requires ACF Pro and WPGraphQL for ACF plugins to be active.
 */

// ─── Helpers ──────────────────────────────────────────────────────────────────

function mk_get_or_create_page( string $title, string $slug, int $parent = 0 ): int {
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		return $existing->ID;
	}
	$id = wp_insert_post( [
		'post_title'  => $title,
		'post_name'   => $slug,
		'post_status' => 'publish',
		'post_type'   => 'page',
		'post_parent' => $parent,
	] );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( "Failed to create page '{$title}': " . $id->get_error_message() );
	}
	WP_CLI::success( "Created page: {$title} (ID: {$id})" );
	return $id;
}

function mk_set_home_page( int $page_id ): void {
	update_option( 'page_on_front', $page_id );
	update_option( 'show_on_front', 'page' );
}

// ─── 1. Create Pages ──────────────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '=== Creating Pages ===' );

$home_id      = mk_get_or_create_page( 'Home',      'home' );
$about_id     = mk_get_or_create_page( 'About',     'about' );
$services_id  = mk_get_or_create_page( 'Services',  'services' );
$portfolio_id = mk_get_or_create_page( 'Portfolio', 'portfolio' );
$pricing_id   = mk_get_or_create_page( 'Pricing',   'pricing' );
$contact_id   = mk_get_or_create_page( 'Contact',   'contact' );

mk_set_home_page( $home_id );
WP_CLI::success( "Set Home as front page." );

// ─── 2. Home Page Sections ────────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '=== Populating Home Page ===' );

$home_sections = [
	// Hero
	[
		'acf_fc_layout'   => 'hero',
		'tagline'         => 'Creative & Minimal Design Agency',
		'heading'         => 'We Are Creative Digital Agency',
		'button_label'    => 'Get Started',
		'button_url'      => '/contact',
		'background_image' => '', // Leave empty – uses CSS background in template
	],
	// Intro Features
	[
		'acf_fc_layout' => 'intro_features',
		'subtitle'      => 'We are Creative',
		'heading'       => 'Award Winning Digital Agency',
		'features'      => [
			[
				'icon'        => 'ti-palette',
				'title'       => 'Creative Design',
				'description' => 'We craft beautiful interfaces that balance aesthetics and usability to make your brand stand out.',
			],
			[
				'icon'        => 'ti-vector',
				'title'       => 'Retina Ready',
				'description' => 'All our designs are pixel-perfect on every screen, from mobile to ultra-high-resolution displays.',
			],
			[
				'icon'        => 'ti-panel',
				'title'       => 'W3c Valid Code',
				'description' => 'We write clean, semantic, and standards-compliant code that ensures cross-browser compatibility.',
			],
		],
	],
	// About (Home style – background image panel)
	[
		'acf_fc_layout'    => 'about_home',
		'subtitle'         => 'About Us',
		'heading'          => 'We Are Digital Creative Agency',
		'subheading'       => 'Think beyond the ordinary',
		'description'      => 'We are a team of passionate designers and developers who create exceptional digital experiences. Our approach combines strategy, creativity, and technology to deliver solutions that drive real results for our clients. Every project we take on is crafted with care and precision.',
		'button_label'     => 'Learn More',
		'button_url'       => '/about',
		'background_image' => '', // optional – uses /images/about/about-1.jpg via CSS
	],
	// Counter
	[
		'acf_fc_layout' => 'counter',
		'items'         => [
			[ 'value' => '3900', 'suffix' => '+', 'label' => 'Projects Completed' ],
			[ 'value' => '2500', 'suffix' => '+', 'label' => 'Satisfied Clients' ],
			[ 'value' => '15',   'suffix' => '+', 'label' => 'Years in Business' ],
			[ 'value' => '20',   'suffix' => '',  'label' => 'Awards Won' ],
		],
	],
	// Services
	[
		'acf_fc_layout' => 'services',
		'subtitle'      => 'What we do',
		'heading'       => 'Our Expertise',
		'services'      => [
			[
				'icon'        => 'ti-palette',
				'title'       => 'UI/UX Design',
				'description' => 'We create intuitive and visually stunning user interfaces that deliver exceptional user experiences.',
			],
			[
				'icon'        => 'ti-vector',
				'title'       => 'Web Development',
				'description' => 'From simple landing pages to complex web applications, we build robust digital solutions.',
			],
			[
				'icon'        => 'ti-mobile',
				'title'       => 'Mobile Apps',
				'description' => 'Native and cross-platform mobile applications that engage your users on every device.',
			],
			[
				'icon'        => 'ti-email',
				'title'       => 'Digital Marketing',
				'description' => 'Strategic marketing campaigns that increase your online visibility and drive conversions.',
			],
			[
				'icon'        => 'ti-camera',
				'title'       => 'Photography',
				'description' => 'Professional photography services that capture your brand story in the most compelling way.',
			],
			[
				'icon'        => 'ti-stats-up',
				'title'       => 'SEO Optimization',
				'description' => 'Data-driven SEO strategies that boost your organic rankings and attract qualified traffic.',
			],
		],
	],
	// CTA Section (with background)
	[
		'acf_fc_layout'    => 'cta',
		'subtitle'         => 'Call To Action',
		'heading'          => 'Have a project in mind?',
		'description'      => 'We help startups, agencies and brands build beautiful digital products. Let\'s talk about how we can turn your idea into a remarkable experience.',
		'phone'            => '+23-456-6588',
		'background_image' => '', // uses /images/bg/cta-bg.jpg via CSS
	],
	// Testimonials
	[
		'acf_fc_layout' => 'testimonials',
		'subtitle'      => 'Happy clients',
		'heading'       => 'What People Say',
		'testimonials'  => [
			[
				'text'        => 'Megakit delivered our project on time and exceeded our expectations in every way. The attention to detail and quality of work was exceptional. Highly recommended!',
				'author_name' => 'Jessica Williams',
				'author_role' => 'CEO, TechStartup Inc.',
			],
			[
				'text'        => 'Working with the Megakit team was a great experience. They understood our vision immediately and translated it into a beautiful, functional website that our customers love.',
				'author_name' => 'Michael Thompson',
				'author_role' => 'Founder, Creative Studios',
			],
			[
				'text'        => 'Professional, responsive and incredibly talented. The team went above and beyond to ensure our digital presence was exactly what we needed to grow our business.',
				'author_name' => 'Sarah Johnson',
				'author_role' => 'Marketing Director, Global Corp',
			],
		],
	],
	// Latest Blog (auto-pulls from WP posts, no custom fields needed – just the layout flag)
	[
		'acf_fc_layout' => 'latest_blog',
		'subtitle'       => 'Latest Blog',
		'heading'        => 'News & Articles',
	],
	// CTA Block (bottom banner)
	[
		'acf_fc_layout' => 'cta_block',
		'subtitle'      => 'Get started',
		'heading'       => 'Ready to take your business to the next level?',
		'button_label'  => 'Contact Us',
		'button_url'    => '/contact',
	],
];

update_field( 'field_page_sections', $home_sections, $home_id );
WP_CLI::success( "Home page sections populated (" . count( $home_sections ) . " sections)." );

// ─── 3. About Page Sections ───────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '=== Populating About Page ===' );

$about_sections = [
	// Page Title
	[
		'acf_fc_layout' => 'page_title',
		'subtitle'      => 'About Us',
		'heading'       => 'Our Company',
		'breadcrumbs'   => [
			[ 'label' => 'Home', 'url' => '/' ],
			[ 'label' => 'About Us', 'url' => '' ],
		],
	],
	// About Detail (image + info items)
	[
		'acf_fc_layout' => 'about_detail',
		'subtitle'      => 'Who We Are',
		'heading'       => 'We Are Creative & Dedicated Team',
		'description'   => 'Founded in 2009, Megakit has grown from a small design studio into a full-service digital agency with a global client base. We believe that great design paired with solid engineering can change how people interact with technology. Our multidisciplinary team brings together designers, developers, strategists, and marketers to create holistic digital solutions.',
		'button_label'  => 'Get Started',
		'button_url'    => '/contact',
		'image'         => '', // optional upload
		'info_items'    => [
			[
				'number'      => '01',
				'title'       => 'Our Mission',
				'description' => 'To empower businesses with innovative digital solutions that create meaningful connections between brands and their audiences.',
			],
			[
				'number'      => '02',
				'title'       => 'Our Vision',
				'description' => 'To be the most trusted creative partner for forward-thinking businesses, setting new standards in digital excellence.',
			],
			[
				'number'      => '03',
				'title'       => 'Our Approach',
				'description' => 'We combine human-centred design thinking with agile development processes to deliver solutions that truly resonate.',
			],
		],
	],
	// Counter (dark variant)
	[
		'acf_fc_layout' => 'counter',
		'items'         => [
			[ 'value' => '3900', 'suffix' => '+', 'label' => 'Projects Completed', 'icon' => 'ti-bar-chart' ],
			[ 'value' => '2500', 'suffix' => '+', 'label' => 'Happy Clients',       'icon' => 'ti-face-smile' ],
			[ 'value' => '15',   'suffix' => '+', 'label' => 'Years Experience',    'icon' => 'ti-time' ],
			[ 'value' => '20',   'suffix' => '',  'label' => 'Awards Won',          'icon' => 'ti-cup' ],
		],
	],
	// Team
	[
		'acf_fc_layout' => 'team',
		'subtitle'      => 'Our Team',
		'heading'       => 'The People Behind Megakit',
		'members'       => [
			[
				'name'          => 'Jessica Williams',
				'role'          => 'CEO & Founder',
				'facebook_url'  => '#',
				'twitter_url'   => '#',
				'instagram_url' => '#',
				'linkedin_url'  => '#',
				'photo'         => '',
			],
			[
				'name'          => 'Michael Thompson',
				'role'          => 'Lead Designer',
				'facebook_url'  => '#',
				'twitter_url'   => '#',
				'instagram_url' => '#',
				'linkedin_url'  => '#',
				'photo'         => '',
			],
			[
				'name'          => 'Sarah Johnson',
				'role'          => 'Head of Development',
				'facebook_url'  => '#',
				'twitter_url'   => '#',
				'instagram_url' => '#',
				'linkedin_url'  => '#',
				'photo'         => '',
			],
			[
				'name'          => 'David Martinez',
				'role'          => 'Marketing Strategist',
				'facebook_url'  => '#',
				'twitter_url'   => '#',
				'instagram_url' => '#',
				'linkedin_url'  => '#',
				'photo'         => '',
			],
		],
	],
	// Testimonials
	[
		'acf_fc_layout' => 'testimonials',
		'subtitle'      => 'Happy clients',
		'heading'       => 'What People Say',
		'testimonials'  => [
			[
				'text'        => 'Megakit transformed our outdated website into a modern, high-performing digital presence that our entire team is proud of. The results speak for themselves.',
				'author_name' => 'Robert Chen',
				'author_role' => 'CTO, InnovateTech',
			],
			[
				'text'        => 'From concept to launch, the Megakit team was professional, communicative, and delivered exactly what they promised. Our conversion rate has doubled since the redesign.',
				'author_name' => 'Emily Davis',
				'author_role' => 'Head of Digital, RetailBrand',
			],
			[
				'text'        => 'I\'ve worked with many agencies over the years, but Megakit\'s combination of creative talent and technical expertise is truly unmatched in the industry.',
				'author_name' => 'James Wilson',
				'author_role' => 'Product Manager, SaaS Platform',
			],
		],
	],
];

update_field( 'field_page_sections', $about_sections, $about_id );
WP_CLI::success( "About page sections populated (" . count( $about_sections ) . " sections)." );

// ─── 4. Services Page Sections ────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '=== Populating Services Page ===' );

$services_sections = [
	[
		'acf_fc_layout' => 'page_title',
		'subtitle'      => 'Our Services',
		'heading'       => 'What We Do',
		'breadcrumbs'   => [
			[ 'label' => 'Home', 'url' => '/' ],
			[ 'label' => 'Our Services', 'url' => '' ],
		],
	],
	[
		'acf_fc_layout' => 'services',
		'subtitle'      => 'What we do',
		'heading'       => 'We Provide Best Services',
		'services'      => [
			[
				'icon'        => 'ti-palette',
				'title'       => 'UI/UX Design',
				'description' => 'Our designers craft user-centred interfaces that delight your customers and drive engagement with your brand.',
			],
			[
				'icon'        => 'ti-vector',
				'title'       => 'Web Development',
				'description' => 'We build high-performance websites and web applications using modern technologies and best practices.',
			],
			[
				'icon'        => 'ti-mobile',
				'title'       => 'App Development',
				'description' => 'iOS, Android, and cross-platform apps that give your users a seamless, native-quality experience.',
			],
			[
				'icon'        => 'ti-email',
				'title'       => 'Email Marketing',
				'description' => 'Automated email campaigns that nurture leads, retain customers, and grow your revenue.',
			],
			[
				'icon'        => 'ti-rocket',
				'title'       => 'Branding',
				'description' => 'Complete brand identity design — from logo and color palette to voice, tone, and brand guidelines.',
			],
			[
				'icon'        => 'ti-stats-up',
				'title'       => 'Digital Marketing',
				'description' => 'Data-driven SEO, PPC, and social media strategies that put your business in front of the right audience.',
			],
			[
				'icon'        => 'ti-camera',
				'title'       => 'Photography',
				'description' => 'Professional product, lifestyle, and corporate photography that elevates your visual brand story.',
			],
			[
				'icon'        => 'ti-layers',
				'title'       => 'Content Strategy',
				'description' => 'Compelling content that educates, entertains and converts — blogs, videos, infographics, and more.',
			],
			[
				'icon'        => 'ti-shield',
				'title'       => 'Security & Hosting',
				'description' => 'Managed hosting, SSL, firewalls and ongoing maintenance to keep your website fast, safe, and online.',
			],
		],
	],
	[
		'acf_fc_layout' => 'cta_block',
		'subtitle'      => 'Get started',
		'heading'       => 'Ready to start your project?',
		'button_label'  => 'Contact Us',
		'button_url'    => '/contact',
	],
];

update_field( 'field_page_sections', $services_sections, $services_id );
WP_CLI::success( "Services page sections populated (" . count( $services_sections ) . " sections)." );

// ─── 5. Portfolio Page Sections ───────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '=== Populating Portfolio Page ===' );

$portfolio_sections = [
	[
		'acf_fc_layout' => 'page_title',
		'subtitle'      => 'Latest Works',
		'heading'       => 'Portfolio',
		'breadcrumbs'   => [
			[ 'label' => 'Home', 'url' => '/' ],
			[ 'label' => 'Latest Works', 'url' => '' ],
		],
	],
	[
		'acf_fc_layout' => 'portfolio',
		'subtitle'      => 'Recent Projects',
		'heading'       => 'Our Works',
	],
];

update_field( 'field_page_sections', $portfolio_sections, $portfolio_id );
WP_CLI::success( "Portfolio page sections populated (" . count( $portfolio_sections ) . " sections)." );

// ─── 6. Pricing Page Sections ─────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '=== Populating Pricing Page ===' );

$pricing_sections = [
	[
		'acf_fc_layout' => 'page_title',
		'subtitle'      => 'Our Pricing',
		'heading'       => 'Pricing Package',
		'breadcrumbs'   => [
			[ 'label' => 'Home', 'url' => '/' ],
			[ 'label' => 'Our Pricing', 'url' => '' ],
		],
	],
	[
		'acf_fc_layout' => 'intro_features',
		'subtitle'      => 'Why choose us',
		'heading'       => 'Transparent, Flexible Pricing',
		'features'      => [
			[
				'icon'        => 'ti-shield',
				'title'       => 'No Hidden Fees',
				'description' => 'Our pricing is completely transparent. What you see is exactly what you pay — no surprises.',
			],
			[
				'icon'        => 'ti-headphone-alt',
				'title'       => 'Dedicated Support',
				'description' => 'Every plan includes access to our expert support team, ready to help whenever you need it.',
			],
			[
				'icon'        => 'ti-reload',
				'title'       => 'Cancel Anytime',
				'description' => 'No long-term lock-in. Upgrade, downgrade, or cancel your plan at any time with zero hassle.',
			],
		],
	],
	[
		'acf_fc_layout' => 'pricing',
		'subtitle'      => 'Pricing',
		'heading'       => 'Flexible Plans For Everyone',
		'plans'         => [
			[
				'name'         => 'Starter',
				'price'        => '29',
				'period'       => 'per month',
				'features'     => "5 Projects\n10 GB Storage\n2 Team Members\nBasic Analytics\nEmail Support",
				'button_label' => 'Get Started',
				'button_url'   => '/contact',
				'highlighted'  => false,
			],
			[
				'name'         => 'Professional',
				'price'        => '79',
				'period'       => 'per month',
				'features'     => "Unlimited Projects\n50 GB Storage\n10 Team Members\nAdvanced Analytics\nPriority Support\nCustom Domain",
				'button_label' => 'Get Started',
				'button_url'   => '/contact',
				'highlighted'  => true,
			],
			[
				'name'         => 'Enterprise',
				'price'        => '149',
				'period'       => 'per month',
				'features'     => "Unlimited Projects\n500 GB Storage\nUnlimited Members\nEnterprise Analytics\n24/7 Phone Support\nCustom Domain\nDedicated Manager",
				'button_label' => 'Get Started',
				'button_url'   => '/contact',
				'highlighted'  => false,
			],
		],
	],
	[
		'acf_fc_layout' => 'cta_block',
		'subtitle'      => 'Still have questions?',
		'heading'       => 'Not sure which plan is right for you?',
		'button_label'  => 'Talk to Us',
		'button_url'    => '/contact',
	],
];

update_field( 'field_page_sections', $pricing_sections, $pricing_id );
WP_CLI::success( "Pricing page sections populated (" . count( $pricing_sections ) . " sections)." );

// ─── 7. Contact Page Sections ─────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '=== Populating Contact Page ===' );

$contact_sections = [
	[
		'acf_fc_layout' => 'page_title',
		'subtitle'      => 'Contact Us',
		'heading'       => 'Get in Touch',
		'breadcrumbs'   => [
			[ 'label' => 'Home', 'url' => '/' ],
			[ 'label' => 'Contact Us', 'url' => '' ],
		],
	],
	[
		'acf_fc_layout' => 'contact_info',
		'address'       => '121 King Street, Melbourne,\nVictoria 3000, Australia',
		'email'         => 'support@megakit.com',
		'phone'         => '+23-456-6588',
		'facebook_url'  => '#',
		'twitter_url'   => '#',
		'linkedin_url'  => '#',
	],
];

update_field( 'field_page_sections', $contact_sections, $contact_id );
WP_CLI::success( "Contact page sections populated (" . count( $contact_sections ) . " sections)." );

// ─── 8. Global Settings (Options Page) ───────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '=== Setting Global Settings ===' );

// The options page slug registered in functions.php is 'global-settings'
$options_page = 'global-settings'; // adjust to match your acf_add_options_page 'menu_slug'

update_field( 'site_name',             'Megakit',                           $options_page );
update_field( 'tagline',               'Creative & Minimal Design Agency',  $options_page );
update_field( 'phone',                 '+23-456-6588',                      $options_page );
update_field( 'email',                 'support@megakit.com',               $options_page );
update_field( 'address',               '121 King Street, Melbourne, Victoria 3000, Australia', $options_page );
update_field( 'facebook_url',          '#',                                 $options_page );
update_field( 'twitter_url',           '#',                                 $options_page );
update_field( 'github_url',            '#',                                 $options_page );
update_field( 'linkedin_url',          '#',                                 $options_page );
update_field( 'copyright_text',        '&copy; Copyright Reserved to Megakit.', $options_page );
update_field( 'footer_subscribe_text', 'Subscribe to get latest news articles and resources.', $options_page );

update_field( 'footer_company_links', [
	[ 'label' => 'Terms & Conditions', 'url' => '#' ],
	[ 'label' => 'Privacy Policy',     'url' => '#' ],
	[ 'label' => 'Support',            'url' => '#' ],
	[ 'label' => 'FAQ',               'url' => '#' ],
], $options_page );

update_field( 'footer_quick_links', [
	[ 'label' => 'About',     'url' => '/about' ],
	[ 'label' => 'Services',  'url' => '/services' ],
	[ 'label' => 'Team',      'url' => '/about' ],
	[ 'label' => 'Contact',   'url' => '/contact' ],
], $options_page );

WP_CLI::success( "Global settings saved." );

// ─── 9. Portfolio Items (Custom Post Type) ────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '=== Creating Portfolio Items ===' );

$portfolio_items = [
	[
		'title'    => 'Retro Workspace',
		'category' => 'Photography',
		'image'    => '/images/portfolio/porto-1.jpg',
	],
	[
		'title'    => 'Creative Agency Website',
		'category' => 'Web Development',
		'image'    => '/images/portfolio/porto-2.jpg',
	],
	[
		'title'    => 'Modern E-Commerce',
		'category' => 'Design',
		'image'    => '/images/portfolio/porto-3.jpg',
	],
	[
		'title'    => 'Mobile Banking App',
		'category' => 'App Design',
		'image'    => '/images/portfolio/porto-4.jpg',
	],
	[
		'title'    => 'Brand Identity System',
		'category' => 'Branding',
		'image'    => '/images/portfolio/porto-5.jpg',
	],
	[
		'title'    => 'SaaS Dashboard UI',
		'category' => 'Design',
		'image'    => '/images/portfolio/porto-6.jpg',
	],
];

foreach ( $portfolio_items as $item ) {
	$existing = get_posts( [
		'post_type'      => 'portfolio_item',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		's'              => $item['title'],
	] );

	if ( ! empty( $existing ) ) {
		WP_CLI::log( "  Skipping existing portfolio item: {$item['title']}" );
		continue;
	}

	$post_id = wp_insert_post( [
		'post_title'  => $item['title'],
		'post_status' => 'publish',
		'post_type'   => 'portfolio_item',
	] );

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( "Failed to create portfolio item '{$item['title']}': " . $post_id->get_error_message() );
		continue;
	}

	update_field( 'category', $item['category'], $post_id );
	// Note: images require WordPress media — link to local path for reference only
	// You can manually upload via WP Admin → Media, then assign to these items

	WP_CLI::success( "Created portfolio item: {$item['title']} (ID: {$post_id})" );
}

// ─── 10. Sample Blog Posts ────────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '=== Creating Sample Blog Posts ===' );

$blog_posts = [
	[
		'title'   => '10 Design Trends That Will Dominate 2024',
		'slug'    => 'design-trends-2024',
		'excerpt' => 'From glassmorphism to bold typography, we explore the visual design trends reshaping digital experiences this year.',
		'content' => '<p>The design world is always evolving. In 2024, we see a convergence of nostalgia and futurism — brands are embracing retro aesthetics while pushing the boundaries of digital interaction.</p><h2>1. Glassmorphism Is Back</h2><p>The frosted glass effect popularised by iOS continues to find its way into web design, especially in dashboard and SaaS interfaces.</p><h2>2. Bold, Variable Typography</h2><p>Variable fonts give designers the flexibility to express personality while maintaining system performance. Expect to see more experimental type treatments.</p><h2>3. Dark Mode as Default</h2><p>With OLED screens now standard on most flagship devices, designers are increasingly treating dark mode as the primary design canvas, not an afterthought.</p><p>The key is to stay informed and experiment boldly. Great design comes from understanding both the rules and when to break them.</p>',
		'category' => 'Design',
	],
	[
		'title'   => 'Why Headless WordPress is the Future of Web Development',
		'slug'    => 'headless-wordpress-future',
		'excerpt' => 'Decoupled architecture, better performance, and developer freedom — here\'s why headless WordPress is gaining momentum.',
		'content' => '<p>Traditional WordPress is a monolith — the CMS and the front end are tightly coupled. Headless WordPress separates them, using WordPress purely as a content API and letting developers choose any front-end framework.</p><h2>Performance Gains</h2><p>Next.js with headless WordPress generates static pages at build time, resulting in near-instant load times and perfect Lighthouse scores — something impossible with traditional WordPress themes.</p><h2>Developer Experience</h2><p>Modern JavaScript developers can use the tools they love — React, TypeScript, Tailwind CSS — while content editors still enjoy the familiar WordPress dashboard.</p><h2>Security Benefits</h2><p>With the WordPress admin completely separate from the public front end, the attack surface is dramatically reduced.</p><p>The headless approach does add complexity, but for most medium-to-large projects, the benefits far outweigh the costs.</p>',
		'category' => 'Development',
	],
	[
		'title'   => 'How to Build a Winning Content Marketing Strategy',
		'slug'    => 'content-marketing-strategy',
		'excerpt' => 'Content marketing is more than publishing blog posts. Here\'s a framework for building a strategy that actually drives results.',
		'content' => '<p>Most businesses know they should be doing content marketing, but few do it well. The difference between content that converts and content that disappears into the void is strategy.</p><h2>Start With Audience Research</h2><p>Before writing a single word, understand your audience deeply. What questions are they asking? What problems keep them up at night? Your content should answer those questions better than anything else online.</p><h2>Build a Content Pillar Framework</h2><p>Organise your content around 4-6 core topics that are central to your business. Create comprehensive pillar pages on each topic, then support them with detailed cluster content.</p><h2>Distribution Is Half the Battle</h2><p>Creating great content is only 50% of the job. You need a systematic approach to distribution — email newsletters, social media, SEO, and paid promotion — to ensure your content reaches the right people.</p>',
		'category' => 'Marketing',
	],
	[
		'title'   => 'The ROI of Good UX: Why User Experience Pays Off',
		'slug'    => 'roi-of-good-ux',
		'excerpt' => 'Investing in user experience design isn\'t a nice-to-have — it\'s one of the highest-ROI activities a business can pursue.',
		'content' => '<p>According to Forrester Research, every $1 invested in UX returns $100 on average. That\'s a 9,900% ROI. Yet many businesses still treat UX as a luxury rather than a necessity.</p><h2>Reduced Development Costs</h2><p>Fixing a UX problem during the design phase costs 10x less than fixing it after development, and 100x less than fixing it after launch. Good UX design catches problems before they become expensive.</p><h2>Higher Conversion Rates</h2><p>A streamlined, intuitive checkout flow can increase e-commerce conversions by 35% or more. Removing friction from the user journey directly impacts your bottom line.</p><h2>Reduced Support Costs</h2><p>When users can figure out how to use your product without help, your support ticket volume drops. Good UX is self-service by design.</p><p>The data is clear: companies that invest in UX outperform their competitors. It\'s not a cost — it\'s a competitive advantage.</p>',
		'category' => 'Design',
	],
];

foreach ( $blog_posts as $post_data ) {
	$existing = get_page_by_path( $post_data['slug'], OBJECT, 'post' );
	if ( $existing ) {
		WP_CLI::log( "  Skipping existing post: {$post_data['title']}" );
		continue;
	}

	// Create or get category
	$category = get_term_by( 'name', $post_data['category'], 'category' );
	if ( ! $category ) {
		$result   = wp_insert_term( $post_data['category'], 'category' );
		$cat_id   = is_wp_error( $result ) ? 1 : $result['term_id'];
	} else {
		$cat_id = $category->term_id;
	}

	$post_id = wp_insert_post( [
		'post_title'   => $post_data['title'],
		'post_name'    => $post_data['slug'],
		'post_excerpt' => $post_data['excerpt'],
		'post_content' => $post_data['content'],
		'post_status'  => 'publish',
		'post_type'    => 'post',
		'post_category' => [ $cat_id ],
	] );

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( "Failed to create post '{$post_data['title']}': " . $post_id->get_error_message() );
		continue;
	}

	WP_CLI::success( "Created blog post: {$post_data['title']} (ID: {$post_id})" );
}

// ─── Done ─────────────────────────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '==========================================' );
WP_CLI::success( 'Megakit content population complete!' );
WP_CLI::log( '' );
WP_CLI::log( 'Next steps:' );
WP_CLI::log( '  1. In WP Admin → Settings → Reading, verify Home page is set to "Home".' );
WP_CLI::log( '  2. Upload images to WP Admin → Media and assign to portfolio items.' );
WP_CLI::log( '  3. Go to ACF → Global Settings and verify all option fields are saved.' );
WP_CLI::log( '  4. Flush permalinks: WP Admin → Settings → Permalinks → Save Changes.' );
WP_CLI::log( '  5. Test your WPGraphQL endpoint at /wp/graphql?query=%7B__typename%7D' );
WP_CLI::log( '==========================================' );
