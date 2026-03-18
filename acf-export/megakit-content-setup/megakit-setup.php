<?php
/**
 * Plugin Name: Megakit Content Setup
 * Description: One-click tool to create all pages and populate ACF flexible content sections. Delete after use.
 * Version:     1.0.0
 * Author:      Megakit
 *
 * IMPORTANT: Import acf-field-groups.json via ACF → Tools → Import BEFORE running this plugin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ─── Admin Menu ───────────────────────────────────────────────────────────────

add_action( 'admin_menu', function () {
	add_management_page(
		'Megakit Setup',
		'Megakit Setup',
		'manage_options',
		'megakit-setup',
		'megakit_setup_page'
	);
} );

// ─── Admin Notice ─────────────────────────────────────────────────────────────

add_action( 'admin_notices', function () {
	if ( ! get_option( 'megakit_setup_done' ) ) {
		echo '<div class="notice notice-warning"><p>';
		echo '<strong>Megakit Setup:</strong> Content has not been populated yet. ';
		echo '<a href="' . esc_url( admin_url( 'tools.php?page=megakit-setup' ) ) . '">Go to Megakit Setup →</a>';
		echo '</p></div>';
	}
} );

// ─── Setup Page ───────────────────────────────────────────────────────────────

function megakit_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) return;

	$ran    = false;
	$log    = [];
	$errors = [];

	if (
		isset( $_POST['megakit_run'] ) &&
		check_admin_referer( 'megakit_populate', 'megakit_nonce' )
	) {
		if ( ! class_exists( 'ACF' ) ) {
			$errors[] = 'ACF Pro is not active. Please activate ACF Pro and re-import acf-field-groups.json before running setup.';
		} else {
			[ $log, $errors ] = megakit_run_population();
			if ( empty( $errors ) ) {
				update_option( 'megakit_setup_done', true );
			}
			$ran = true;
		}
	}

	?>
	<div class="wrap">
		<h1>🚀 Megakit Content Setup</h1>
		<p>This tool creates all WordPress pages and populates them with the Megakit template content using ACF Flexible Content sections.</p>

		<div style="background:#fff3cd;border-left:4px solid #ffc107;padding:12px 16px;margin:16px 0;border-radius:3px;">
			<strong>Before running:</strong>
			<ol style="margin:8px 0 0 20px;padding:0;">
				<li>Make sure <strong>ACF Pro</strong> is installed and active.</li>
				<li>Make sure <strong>WPGraphQL</strong> and <strong>WPGraphQL for ACF</strong> are active.</li>
				<li>Import <code>acf-field-groups.json</code> via <strong>ACF → Tools → Import</strong>.</li>
				<li>Go to <strong>Settings → Reading</strong> and set Homepage Display to <em>A static page</em> (you can choose after pages are created).</li>
			</ol>
		</div>

		<?php if ( get_option( 'megakit_setup_done' ) && ! $ran ) : ?>
			<div style="background:#d4edda;border-left:4px solid #28a745;padding:12px 16px;margin:16px 0;border-radius:3px;">
				<strong>✅ Setup already completed.</strong> Re-running will skip existing pages and posts.
			</div>
		<?php endif; ?>

		<?php if ( $ran ) : ?>
			<?php if ( ! empty( $errors ) ) : ?>
				<div style="background:#f8d7da;border-left:4px solid #dc3545;padding:12px 16px;margin:16px 0;border-radius:3px;">
					<strong>⚠️ Errors:</strong>
					<ul style="margin:8px 0 0 20px;"><?php foreach ( $errors as $e ) echo '<li>' . esc_html( $e ) . '</li>'; ?></ul>
				</div>
			<?php else : ?>
				<div style="background:#d4edda;border-left:4px solid #28a745;padding:12px 16px;margin:16px 0;border-radius:3px;">
					<strong>✅ Content populated successfully!</strong>
					<p style="margin:6px 0 0;">Next steps: Go to <a href="<?php echo esc_url( admin_url( 'options-reading.php' ) ); ?>">Settings → Reading</a> and set the Homepage to the <em>Home</em> page. Then flush permalinks at <a href="<?php echo esc_url( admin_url( 'options-permalink.php' ) ); ?>">Settings → Permalinks</a>.</p>
				</div>
			<?php endif; ?>

			<h3>Log</h3>
			<div style="background:#f6f7f7;padding:12px 16px;border-radius:3px;font-family:monospace;font-size:13px;max-height:400px;overflow:auto;border:1px solid #ccd0d4;">
				<?php foreach ( $log as $line ) echo esc_html( $line ) . '<br>'; ?>
			</div>
		<?php endif; ?>

		<form method="post" style="margin-top:20px;">
			<?php wp_nonce_field( 'megakit_populate', 'megakit_nonce' ); ?>
			<p>
				<input
					type="submit"
					name="megakit_run"
					class="button button-primary button-hero"
					value="<?php echo get_option( 'megakit_setup_done' ) ? '🔄 Re-run Population' : '▶ Populate Content Now'; ?>"
					onclick="return confirm('This will create pages and populate ACF flexible content. Continue?');"
				/>
			</p>
		</form>

		<hr style="margin-top:30px;">
		<p style="color:#888;font-size:12px;">You can safely delete this plugin after content is populated. It has no effect on the live site once removed.</p>
	</div>
	<?php
}

// ─── Population Logic ─────────────────────────────────────────────────────────

function megakit_run_population(): array {
	$log    = [];
	$errors = [];

	// Helper: create page if not exists
	$make_page = function ( string $title, string $slug ) use ( &$log ): int {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$log[] = "  → Skipped (already exists): {$title}";
			return $existing->ID;
		}
		$id = wp_insert_post( [
			'post_title'  => $title,
			'post_name'   => $slug,
			'post_status' => 'publish',
			'post_type'   => 'page',
		] );
		if ( is_wp_error( $id ) ) {
			return 0;
		}
		$log[] = "  ✓ Created page: {$title} (ID: {$id})";
		return $id;
	};

	// ── 1. Pages ─────────────────────────────────────────────────────────────

	$log[] = '';
	$log[] = '=== Creating Pages ===';

	$home_id      = $make_page( 'Home',      'home' );
	$about_id     = $make_page( 'About',     'about' );
	$services_id  = $make_page( 'Services',  'services' );
	$portfolio_id = $make_page( 'Portfolio', 'portfolio' );
	$pricing_id   = $make_page( 'Pricing',   'pricing' );
	$contact_id   = $make_page( 'Contact',   'contact' );

	// Set home page
	update_option( 'page_on_front', $home_id );
	update_option( 'show_on_front', 'page' );
	$log[] = '  ✓ Set Home as front page';

	// ── 2. Home Page Sections ─────────────────────────────────────────────────

	$log[] = '';
	$log[] = '=== Populating Home Page ===';

	$home_sections = [
		[
			'acf_fc_layout'    => 'hero',
			'tagline'          => 'Creative & Minimal Design Agency',
			'heading'          => 'We Are Creative Digital Agency',
			'button_label'     => 'Get Started',
			'button_url'       => '/contact',
			'background_image' => '',
		],
		[
			'acf_fc_layout' => 'intro_features',
			'subtitle'      => 'We are Creative',
			'heading'       => 'Award Winning Digital Agency',
			'features'      => [
				[ 'icon' => 'ti-palette', 'title' => 'Creative Design',   'description' => 'We craft beautiful interfaces that balance aesthetics and usability to make your brand stand out.' ],
				[ 'icon' => 'ti-vector',  'title' => 'Retina Ready',       'description' => 'All our designs are pixel-perfect on every screen, from mobile to ultra-high-resolution displays.' ],
				[ 'icon' => 'ti-panel',   'title' => 'W3c Valid Code',     'description' => 'We write clean, semantic, and standards-compliant code that ensures cross-browser compatibility.' ],
			],
		],
		[
			'acf_fc_layout'    => 'about_home',
			'subtitle'         => 'About Us',
			'heading'          => 'We Are Digital Creative Agency',
			'subheading'       => 'Think beyond the ordinary',
			'description'      => 'We are a team of passionate designers and developers who create exceptional digital experiences. Our approach combines strategy, creativity, and technology to deliver solutions that drive real results for our clients.',
			'button_label'     => 'Learn More',
			'button_url'       => '/about',
			'background_image' => '',
		],
		[
			'acf_fc_layout' => 'counter',
			'items'         => [
				[ 'value' => '3900', 'suffix' => '+', 'label' => 'Projects Completed' ],
				[ 'value' => '2500', 'suffix' => '+', 'label' => 'Satisfied Clients' ],
				[ 'value' => '15',   'suffix' => '+', 'label' => 'Years in Business' ],
				[ 'value' => '20',   'suffix' => '',  'label' => 'Awards Won' ],
			],
		],
		[
			'acf_fc_layout' => 'services',
			'subtitle'      => 'What we do',
			'heading'       => 'Our Expertise',
			'services'      => [
				[ 'icon' => 'ti-palette',   'title' => 'UI/UX Design',       'description' => 'We create intuitive and visually stunning user interfaces that deliver exceptional user experiences.' ],
				[ 'icon' => 'ti-vector',    'title' => 'Web Development',     'description' => 'From simple landing pages to complex web applications, we build robust digital solutions.' ],
				[ 'icon' => 'ti-mobile',    'title' => 'Mobile Apps',         'description' => 'Native and cross-platform mobile applications that engage your users on every device.' ],
				[ 'icon' => 'ti-email',     'title' => 'Digital Marketing',   'description' => 'Strategic marketing campaigns that increase your online visibility and drive conversions.' ],
				[ 'icon' => 'ti-camera',    'title' => 'Photography',         'description' => 'Professional photography services that capture your brand story in the most compelling way.' ],
				[ 'icon' => 'ti-stats-up',  'title' => 'SEO Optimization',    'description' => 'Data-driven SEO strategies that boost your organic rankings and attract qualified traffic.' ],
			],
		],
		[
			'acf_fc_layout'    => 'cta',
			'subtitle'         => 'Call To Action',
			'heading'          => 'Have a project in mind?',
			'description'      => "We help startups, agencies and brands build beautiful digital products. Let's talk about how we can turn your idea into a remarkable experience.",
			'phone'            => '+23-456-6588',
			'background_image' => '',
		],
		[
			'acf_fc_layout' => 'testimonials',
			'subtitle'      => 'Happy clients',
			'heading'       => 'What People Say',
			'testimonials'  => [
				[ 'text' => 'Megakit delivered our project on time and exceeded our expectations in every way. The attention to detail and quality of work was exceptional. Highly recommended!', 'author_name' => 'Jessica Williams', 'author_role' => 'CEO, TechStartup Inc.' ],
				[ 'text' => "Working with the Megakit team was a great experience. They understood our vision immediately and translated it into a beautiful, functional website that our customers love.", 'author_name' => 'Michael Thompson', 'author_role' => 'Founder, Creative Studios' ],
				[ 'text' => 'Professional, responsive and incredibly talented. The team went above and beyond to ensure our digital presence was exactly what we needed to grow our business.', 'author_name' => 'Sarah Johnson', 'author_role' => 'Marketing Director, Global Corp' ],
			],
		],
		[
			'acf_fc_layout' => 'latest_blog',
			'subtitle'      => 'Latest Blog',
			'heading'       => 'News & Articles',
		],
		[
			'acf_fc_layout' => 'cta_block',
			'subtitle'      => 'Get started',
			'heading'       => 'Ready to take your business to the next level?',
			'button_label'  => 'Contact Us',
			'button_url'    => '/contact',
		],
	];

	update_field( 'field_page_sections', $home_sections, $home_id );
	$log[] = '  ✓ Home: ' . count( $home_sections ) . ' sections saved';

	// ── 3. About Page ─────────────────────────────────────────────────────────

	$log[] = '';
	$log[] = '=== Populating About Page ===';

	$about_sections = [
		[
			'acf_fc_layout' => 'page_title',
			'subtitle'      => 'About Us',
			'heading'       => 'Our Company',
			'breadcrumbs'   => [ [ 'label' => 'Home', 'url' => '/' ], [ 'label' => 'About Us', 'url' => '' ] ],
		],
		[
			'acf_fc_layout' => 'about_detail',
			'subtitle'      => 'Who We Are',
			'heading'       => 'We Are Creative & Dedicated Team',
			'description'   => 'Founded in 2009, Megakit has grown from a small design studio into a full-service digital agency with a global client base. We believe that great design paired with solid engineering can change how people interact with technology.',
			'button_label'  => 'Get Started',
			'button_url'    => '/contact',
			'image'         => '',
			'info_items'    => [
				[ 'number' => '01', 'title' => 'Our Mission', 'description' => 'To empower businesses with innovative digital solutions that create meaningful connections between brands and their audiences.' ],
				[ 'number' => '02', 'title' => 'Our Vision',  'description' => 'To be the most trusted creative partner for forward-thinking businesses, setting new standards in digital excellence.' ],
				[ 'number' => '03', 'title' => 'Our Approach','description' => 'We combine human-centred design thinking with agile development processes to deliver solutions that truly resonate.' ],
			],
		],
		[
			'acf_fc_layout' => 'counter',
			'items'         => [
				[ 'value' => '3900', 'suffix' => '+', 'label' => 'Projects Completed', 'icon' => 'ti-bar-chart' ],
				[ 'value' => '2500', 'suffix' => '+', 'label' => 'Happy Clients',       'icon' => 'ti-face-smile' ],
				[ 'value' => '15',   'suffix' => '+', 'label' => 'Years Experience',    'icon' => 'ti-time' ],
				[ 'value' => '20',   'suffix' => '',  'label' => 'Awards Won',          'icon' => 'ti-cup' ],
			],
		],
		[
			'acf_fc_layout' => 'team',
			'subtitle'      => 'Our Team',
			'heading'       => 'The People Behind Megakit',
			'members'       => [
				[ 'name' => 'Jessica Williams', 'role' => 'CEO & Founder',         'facebook_url' => '#', 'twitter_url' => '#', 'instagram_url' => '#', 'linkedin_url' => '#', 'photo' => '' ],
				[ 'name' => 'Michael Thompson', 'role' => 'Lead Designer',         'facebook_url' => '#', 'twitter_url' => '#', 'instagram_url' => '#', 'linkedin_url' => '#', 'photo' => '' ],
				[ 'name' => 'Sarah Johnson',    'role' => 'Head of Development',   'facebook_url' => '#', 'twitter_url' => '#', 'instagram_url' => '#', 'linkedin_url' => '#', 'photo' => '' ],
				[ 'name' => 'David Martinez',   'role' => 'Marketing Strategist',  'facebook_url' => '#', 'twitter_url' => '#', 'instagram_url' => '#', 'linkedin_url' => '#', 'photo' => '' ],
			],
		],
		[
			'acf_fc_layout' => 'testimonials',
			'subtitle'      => 'Happy clients',
			'heading'       => 'What People Say',
			'testimonials'  => [
				[ 'text' => 'Megakit transformed our outdated website into a modern, high-performing digital presence that our entire team is proud of. The results speak for themselves.', 'author_name' => 'Robert Chen',   'author_role' => 'CTO, InnovateTech' ],
				[ 'text' => 'From concept to launch, the Megakit team was professional, communicative, and delivered exactly what they promised. Our conversion rate has doubled since the redesign.', 'author_name' => 'Emily Davis',   'author_role' => 'Head of Digital, RetailBrand' ],
				[ 'text' => "I've worked with many agencies over the years, but Megakit's combination of creative talent and technical expertise is truly unmatched in the industry.", 'author_name' => 'James Wilson',  'author_role' => 'Product Manager, SaaS Platform' ],
			],
		],
	];

	update_field( 'field_page_sections', $about_sections, $about_id );
	$log[] = '  ✓ About: ' . count( $about_sections ) . ' sections saved';

	// ── 4. Services Page ──────────────────────────────────────────────────────

	$log[] = '';
	$log[] = '=== Populating Services Page ===';

	$services_sections = [
		[
			'acf_fc_layout' => 'page_title',
			'subtitle'      => 'Our Services',
			'heading'       => 'What We Do',
			'breadcrumbs'   => [ [ 'label' => 'Home', 'url' => '/' ], [ 'label' => 'Our Services', 'url' => '' ] ],
		],
		[
			'acf_fc_layout' => 'services',
			'subtitle'      => 'What we do',
			'heading'       => 'We Provide Best Services',
			'services'      => [
				[ 'icon' => 'ti-palette',  'title' => 'UI/UX Design',      'description' => 'Our designers craft user-centred interfaces that delight your customers and drive engagement with your brand.' ],
				[ 'icon' => 'ti-vector',   'title' => 'Web Development',    'description' => 'We build high-performance websites and web applications using modern technologies and best practices.' ],
				[ 'icon' => 'ti-mobile',   'title' => 'App Development',    'description' => 'iOS, Android, and cross-platform apps that give your users a seamless, native-quality experience.' ],
				[ 'icon' => 'ti-email',    'title' => 'Email Marketing',    'description' => 'Automated email campaigns that nurture leads, retain customers, and grow your revenue.' ],
				[ 'icon' => 'ti-rocket',   'title' => 'Branding',           'description' => 'Complete brand identity design — from logo and colour palette to voice, tone, and brand guidelines.' ],
				[ 'icon' => 'ti-stats-up', 'title' => 'Digital Marketing',  'description' => 'Data-driven SEO, PPC, and social media strategies that put your business in front of the right audience.' ],
				[ 'icon' => 'ti-camera',   'title' => 'Photography',        'description' => 'Professional product, lifestyle, and corporate photography that elevates your visual brand story.' ],
				[ 'icon' => 'ti-layers',   'title' => 'Content Strategy',   'description' => 'Compelling content that educates, entertains and converts — blogs, videos, infographics, and more.' ],
				[ 'icon' => 'ti-shield',   'title' => 'Security & Hosting', 'description' => 'Managed hosting, SSL, firewalls and ongoing maintenance to keep your website fast, safe, and online.' ],
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
	$log[] = '  ✓ Services: ' . count( $services_sections ) . ' sections saved';

	// ── 5. Portfolio Page ─────────────────────────────────────────────────────

	$log[] = '';
	$log[] = '=== Populating Portfolio Page ===';

	$portfolio_sections = [
		[
			'acf_fc_layout' => 'page_title',
			'subtitle'      => 'Latest Works',
			'heading'       => 'Portfolio',
			'breadcrumbs'   => [ [ 'label' => 'Home', 'url' => '/' ], [ 'label' => 'Latest Works', 'url' => '' ] ],
		],
		[
			'acf_fc_layout' => 'portfolio',
			'subtitle'      => 'Recent Projects',
			'heading'       => 'Our Works',
		],
	];

	update_field( 'field_page_sections', $portfolio_sections, $portfolio_id );
	$log[] = '  ✓ Portfolio: ' . count( $portfolio_sections ) . ' sections saved';

	// ── 6. Pricing Page ───────────────────────────────────────────────────────

	$log[] = '';
	$log[] = '=== Populating Pricing Page ===';

	$pricing_sections = [
		[
			'acf_fc_layout' => 'page_title',
			'subtitle'      => 'Our Pricing',
			'heading'       => 'Pricing Package',
			'breadcrumbs'   => [ [ 'label' => 'Home', 'url' => '/' ], [ 'label' => 'Our Pricing', 'url' => '' ] ],
		],
		[
			'acf_fc_layout' => 'intro_features',
			'subtitle'      => 'Why choose us',
			'heading'       => 'Transparent, Flexible Pricing',
			'features'      => [
				[ 'icon' => 'ti-shield',       'title' => 'No Hidden Fees',    'description' => 'Our pricing is completely transparent. What you see is exactly what you pay — no surprises.' ],
				[ 'icon' => 'ti-headphone-alt', 'title' => 'Dedicated Support', 'description' => 'Every plan includes access to our expert support team, ready to help whenever you need it.' ],
				[ 'icon' => 'ti-reload',        'title' => 'Cancel Anytime',    'description' => 'No long-term lock-in. Upgrade, downgrade, or cancel your plan at any time with zero hassle.' ],
			],
		],
		[
			'acf_fc_layout' => 'pricing',
			'subtitle'      => 'Pricing',
			'heading'       => 'Flexible Plans For Everyone',
			'plans'         => [
				[ 'name' => 'Starter',      'price' => '29',  'period' => 'per month', 'features' => "5 Projects\n10 GB Storage\n2 Team Members\nBasic Analytics\nEmail Support",                                                          'button_label' => 'Get Started', 'button_url' => '/contact', 'highlighted' => false ],
				[ 'name' => 'Professional', 'price' => '79',  'period' => 'per month', 'features' => "Unlimited Projects\n50 GB Storage\n10 Team Members\nAdvanced Analytics\nPriority Support\nCustom Domain",                             'button_label' => 'Get Started', 'button_url' => '/contact', 'highlighted' => true  ],
				[ 'name' => 'Enterprise',   'price' => '149', 'period' => 'per month', 'features' => "Unlimited Projects\n500 GB Storage\nUnlimited Members\nEnterprise Analytics\n24/7 Phone Support\nCustom Domain\nDedicated Manager",   'button_label' => 'Get Started', 'button_url' => '/contact', 'highlighted' => false ],
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
	$log[] = '  ✓ Pricing: ' . count( $pricing_sections ) . ' sections saved';

	// ── 7. Contact Page ───────────────────────────────────────────────────────

	$log[] = '';
	$log[] = '=== Populating Contact Page ===';

	$contact_sections = [
		[
			'acf_fc_layout' => 'page_title',
			'subtitle'      => 'Contact Us',
			'heading'       => 'Get in Touch',
			'breadcrumbs'   => [ [ 'label' => 'Home', 'url' => '/' ], [ 'label' => 'Contact Us', 'url' => '' ] ],
		],
		[
			'acf_fc_layout' => 'contact_info',
			'address'       => "121 King Street, Melbourne,\nVictoria 3000, Australia",
			'email'         => 'support@megakit.com',
			'phone'         => '+23-456-6588',
			'facebook_url'  => '#',
			'twitter_url'   => '#',
			'linkedin_url'  => '#',
		],
	];

	update_field( 'field_page_sections', $contact_sections, $contact_id );
	$log[] = '  ✓ Contact: ' . count( $contact_sections ) . ' sections saved';

	// ── 8. Global Settings ────────────────────────────────────────────────────

	$log[] = '';
	$log[] = '=== Saving Global Settings ===';

	$opts = 'options';
	update_field( 'site_name',             'Megakit',                                                'options' );
	update_field( 'tagline',               'Creative & Minimal Design Agency',                      'options' );
	update_field( 'phone',                 '+23-456-6588',                                          'options' );
	update_field( 'email',                 'support@megakit.com',                                   'options' );
	update_field( 'address',               '121 King Street, Melbourne, Victoria 3000, Australia',  'options' );
	update_field( 'facebook_url',          '#',                                                     'options' );
	update_field( 'twitter_url',           '#',                                                     'options' );
	update_field( 'github_url',            '#',                                                     'options' );
	update_field( 'linkedin_url',          '#',                                                     'options' );
	update_field( 'copyright_text',        '&copy; Copyright Reserved to Megakit.',                 'options' );
	update_field( 'footer_subscribe_text', 'Subscribe to get latest news articles and resources.',  'options' );
	update_field( 'footer_company_links', [
		[ 'label' => 'Terms & Conditions', 'url' => '#' ],
		[ 'label' => 'Privacy Policy',     'url' => '#' ],
		[ 'label' => 'Support',            'url' => '#' ],
		[ 'label' => 'FAQ',                'url' => '#' ],
	], 'options' );
	update_field( 'footer_quick_links', [
		[ 'label' => 'About',    'url' => '/about' ],
		[ 'label' => 'Services', 'url' => '/services' ],
		[ 'label' => 'Team',     'url' => '/about' ],
		[ 'label' => 'Contact',  'url' => '/contact' ],
	], 'options' );

	$log[] = '  ✓ Global settings saved';

	// ── 9. Portfolio Items ────────────────────────────────────────────────────

	$log[] = '';
	$log[] = '=== Creating Portfolio Items ===';

	$portfolio_items = [
		[ 'title' => 'Retro Workspace',         'category' => 'Photography' ],
		[ 'title' => 'Creative Agency Website',  'category' => 'Web Development' ],
		[ 'title' => 'Modern E-Commerce',        'category' => 'Design' ],
		[ 'title' => 'Mobile Banking App',       'category' => 'App Design' ],
		[ 'title' => 'Brand Identity System',    'category' => 'Branding' ],
		[ 'title' => 'SaaS Dashboard UI',        'category' => 'Design' ],
	];

	foreach ( $portfolio_items as $item ) {
		$existing = get_posts( [ 'post_type' => 'portfolio_item', 'post_status' => 'publish', 'posts_per_page' => 1, 'title' => $item['title'] ] );
		if ( ! empty( $existing ) ) {
			$log[] = "  → Skipped (exists): {$item['title']}";
			continue;
		}
		$pid = wp_insert_post( [ 'post_title' => $item['title'], 'post_status' => 'publish', 'post_type' => 'portfolio_item' ] );
		if ( ! is_wp_error( $pid ) ) {
			update_field( 'category', $item['category'], $pid );
			$log[] = "  ✓ Portfolio item: {$item['title']}";
		}
	}

	// ── 10. Blog Posts ────────────────────────────────────────────────────────

	$log[] = '';
	$log[] = '=== Creating Sample Blog Posts ===';

	$blog_posts = [
		[
			'title'    => '10 Design Trends That Will Dominate This Year',
			'slug'     => 'design-trends-this-year',
			'excerpt'  => 'From glassmorphism to bold typography, we explore the visual design trends reshaping digital experiences.',
			'content'  => '<p>The design world is always evolving. This year we see a convergence of nostalgia and futurism — brands are embracing retro aesthetics while pushing the boundaries of digital interaction.</p><h2>1. Glassmorphism Is Back</h2><p>The frosted glass effect continues to find its way into web design, especially in dashboard and SaaS interfaces.</p><h2>2. Bold, Variable Typography</h2><p>Variable fonts give designers the flexibility to express personality while maintaining system performance.</p><h2>3. Dark Mode as Default</h2><p>With OLED screens now standard, designers are increasingly treating dark mode as the primary design canvas.</p>',
			'category' => 'Design',
		],
		[
			'title'    => 'Why Headless WordPress is the Future of Web Development',
			'slug'     => 'headless-wordpress-future',
			'excerpt'  => "Decoupled architecture, better performance, and developer freedom — here's why headless WordPress is gaining momentum.",
			'content'  => '<p>Traditional WordPress is a monolith — the CMS and the front end are tightly coupled. Headless WordPress separates them, using WordPress purely as a content API.</p><h2>Performance Gains</h2><p>Next.js with headless WordPress generates static pages at build time, resulting in near-instant load times.</p><h2>Developer Experience</h2><p>Modern JavaScript developers can use the tools they love while content editors still enjoy the familiar WordPress dashboard.</p>',
			'category' => 'Development',
		],
		[
			'title'    => 'How to Build a Winning Content Marketing Strategy',
			'slug'     => 'content-marketing-strategy',
			'excerpt'  => "Content marketing is more than publishing blog posts. Here's a framework for building a strategy that actually drives results.",
			'content'  => '<p>Most businesses know they should be doing content marketing, but few do it well. The difference between content that converts and content that disappears is strategy.</p><h2>Start With Audience Research</h2><p>Before writing a single word, understand your audience deeply. What questions are they asking? What problems keep them up at night?</p><h2>Distribution Is Half the Battle</h2><p>Creating great content is only 50% of the job. You need a systematic approach to distribution.</p>',
			'category' => 'Marketing',
		],
		[
			'title'    => 'The ROI of Good UX: Why User Experience Pays Off',
			'slug'     => 'roi-of-good-ux',
			'excerpt'  => "Investing in user experience design isn't a nice-to-have — it's one of the highest-ROI activities a business can pursue.",
			'content'  => '<p>According to Forrester Research, every $1 invested in UX returns $100 on average. Yet many businesses still treat UX as a luxury.</p><h2>Reduced Development Costs</h2><p>Fixing a UX problem during the design phase costs 10x less than fixing it after development.</p><h2>Higher Conversion Rates</h2><p>A streamlined checkout flow can increase e-commerce conversions by 35% or more.</p>',
			'category' => 'Design',
		],
	];

	foreach ( $blog_posts as $post_data ) {
		$existing = get_page_by_path( $post_data['slug'], OBJECT, 'post' );
		if ( $existing ) {
			$log[] = "  → Skipped (exists): {$post_data['title']}";
			continue;
		}
		$cat = get_term_by( 'name', $post_data['category'], 'category' );
		$cat_id = $cat ? $cat->term_id : wp_insert_term( $post_data['category'], 'category' )['term_id'];
		$post_id = wp_insert_post( [
			'post_title'    => $post_data['title'],
			'post_name'     => $post_data['slug'],
			'post_excerpt'  => $post_data['excerpt'],
			'post_content'  => $post_data['content'],
			'post_status'   => 'publish',
			'post_type'     => 'post',
			'post_category' => [ $cat_id ],
		] );
		if ( ! is_wp_error( $post_id ) ) {
			$log[] = "  ✓ Blog post: {$post_data['title']}";
		}
	}

	$log[] = '';
	$log[] = '=== Done! ===';

	return [ $log, $errors ];
}
