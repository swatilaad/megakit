<?php
/**
 * Plugin Name: Megakit Setup New
 * Description: One-click tool to create all pages and populate ACF flexible content sections. Delete after use.
 * Version:     1.0.0
 * Author:      Megakit
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

add_action( 'admin_notices', function () {
	if ( ! get_option( 'megakit_setup_done' ) ) {
		echo '<div class="notice notice-warning"><p>';
		echo '<strong>Megakit Setup:</strong> Content has not been populated yet. ';
		echo '<a href="' . esc_url( admin_url( 'tools.php?page=megakit-setup' ) ) . '">Go to Megakit Setup &rarr;</a>';
		echo '</p></div>';
	}
} );

// ─── Setup Page ───────────────────────────────────────────────────────────────

function megakit_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) return;

	$ran = false; $log = []; $errors = [];

	if ( isset( $_POST['megakit_run'] ) && check_admin_referer( 'megakit_populate', 'megakit_nonce' ) ) {
		if ( ! class_exists( 'ACF' ) ) {
			$errors[] = 'ACF Pro is not active. Please activate ACF Pro and import acf-field-groups.json first.';
		} else {
			[ $log, $errors ] = megakit_run_population();
			if ( empty( $errors ) ) update_option( 'megakit_setup_done', true );
			$ran = true;
		}
	}
	?>
	<div class="wrap">
		<h1>Megakit Content Setup</h1>
		<p>Creates all WordPress pages and populates them with content matching the original HTML templates.</p>
		<div style="background:#fff3cd;border-left:4px solid #ffc107;padding:12px 16px;margin:16px 0;">
			<strong>Before running:</strong>
			<ol style="margin:8px 0 0 20px;">
				<li>ACF Pro must be installed and active.</li>
				<li>WPGraphQL and WPGraphQL for ACF must be active.</li>
				<li>Import <code>acf-field-groups.json</code> via <strong>ACF &rarr; Tools &rarr; Import</strong>.</li>
			</ol>
		</div>
		<?php if ( get_option( 'megakit_setup_done' ) && ! $ran ) : ?>
			<div style="background:#d4edda;border-left:4px solid #28a745;padding:12px 16px;margin:16px 0;">
				<strong>Setup already completed.</strong> Re-running will skip existing pages/posts.
			</div>
		<?php endif; ?>
		<?php if ( $ran ) : ?>
			<?php if ( ! empty( $errors ) ) : ?>
				<div style="background:#f8d7da;border-left:4px solid #dc3545;padding:12px 16px;margin:16px 0;">
					<strong>Errors:</strong><ul style="margin:6px 0 0 20px;"><?php foreach($errors as $e) echo '<li>'.esc_html($e).'</li>'; ?></ul>
				</div>
			<?php else : ?>
				<div style="background:#d4edda;border-left:4px solid #28a745;padding:12px 16px;margin:16px 0;">
					<strong>Content populated successfully!</strong> Now go to
					<a href="<?php echo esc_url(admin_url('options-reading.php')); ?>">Settings &rarr; Reading</a> and set the Homepage to the <em>Home</em> page.
					Then flush <a href="<?php echo esc_url(admin_url('options-permalink.php')); ?>">permalinks</a>.
				</div>
			<?php endif; ?>
			<h3>Log</h3>
			<div style="background:#f6f7f7;padding:12px 16px;font-family:monospace;font-size:13px;max-height:400px;overflow:auto;border:1px solid #ccd0d4;">
				<?php foreach($log as $line) echo esc_html($line).'<br>'; ?>
			</div>
		<?php endif; ?>
		<form method="post" style="margin-top:20px;">
			<?php wp_nonce_field( 'megakit_populate', 'megakit_nonce' ); ?>
			<input type="submit" name="megakit_run" class="button button-primary button-hero"
				value="<?php echo get_option('megakit_setup_done') ? 'Re-run Population' : 'Populate Content Now'; ?>"
				onclick="return confirm('This will create pages and populate ACF content. Continue?');" />
		</form>
		<hr style="margin-top:30px;">
		<p style="color:#888;font-size:12px;">Delete this plugin after content is populated.</p>
	</div>
	<?php
}

// ─── Population Logic ─────────────────────────────────────────────────────────

function megakit_run_population(): array {
	$log = []; $errors = [];

	$make_page = function( string $title, string $slug ) use ( &$log ): int {
		$existing = get_page_by_path( $slug );
		if ( $existing ) { $log[] = "  -> Skipped (exists): {$title}"; return $existing->ID; }
		$id = wp_insert_post([ 'post_title' => $title, 'post_name' => $slug, 'post_status' => 'publish', 'post_type' => 'page' ]);
		if ( is_wp_error($id) ) return 0;
		$log[] = "  + Created page: {$title} (ID: {$id})";
		return $id;
	};

	// ── 1. Create Pages ───────────────────────────────────────────────────────

	$log[] = '=== Creating Pages ===';
	$home_id      = $make_page( 'Home',      'home' );
	$about_id     = $make_page( 'About',     'about' );
	$services_id  = $make_page( 'Services',  'services' );
	$portfolio_id = $make_page( 'Portfolio', 'portfolio' );
	$pricing_id   = $make_page( 'Pricing',   'pricing' );
	$contact_id   = $make_page( 'Contact',   'contact' );
	update_option( 'page_on_front', $home_id );
	update_option( 'show_on_front', 'page' );
	$log[] = '  + Set Home as front page';

	// ── 2. HOME PAGE ──────────────────────────────────────────────────────────

	$log[] = ''; $log[] = '=== Populating Home Page ===';

	update_field( 'field_page_sections', [

		// Hero
		[
			'acf_fc_layout' => 'hero',
			'tagline'       => 'Prepare for new future',
			'heading'       => 'Our work is presentation of our capabilities.',
			'button_label'  => 'Get started',
			'button_url'    => '#',
		],

		// Intro Features
		[
			'acf_fc_layout' => 'intro_features',
			'subtitle'      => 'We are creative & expert people',
			'heading'       => 'We work with business & provide solution to client with their business problem',
			'features'      => [
				[ 'icon' => 'ti-desktop',    'title' => 'Modern & Responsive design',       'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odit, ducimus.' ],
				[ 'icon' => 'ti-medall',     'title' => 'Awarded licensed company',          'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odit, ducimus.' ],
				[ 'icon' => 'ti-layers-alt', 'title' => 'Build your website Professionally', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odit, ducimus.' ],
			],
		],

		// About (Home style)
		[
			'acf_fc_layout' => 'about_home',
			'subtitle'      => 'What we are',
			'heading'       => 'We are dynamic team of creative people',
			'subheading'    => 'We are Perfect Solution',
			'description'   => 'We provide consulting services in the area of IFRS and management reporting, helping companies to reach their highest level. We optimize business processes, making them easier.',
			'button_label'  => 'Get started',
			'button_url'    => '#',
		],

		// Counter
		[
			'acf_fc_layout' => 'counter',
			'items'         => [
				[ 'value' => '1730', 'suffix' => '+', 'label' => 'Project Done' ],
				[ 'value' => '125',  'suffix' => 'M', 'label' => 'User Worldwide' ],
				[ 'value' => '39',   'suffix' => '',  'label' => 'Availble Country' ],
				[ 'value' => '14',   'suffix' => '',  'label' => 'Award Winner' ],
			],
		],

		// Services
		[
			'acf_fc_layout' => 'services',
			'subtitle'      => 'Our Services',
			'heading'       => 'We provide a wide range of creative services',
			'services'      => [
				[ 'icon' => 'ti-desktop',    'title' => 'Web development.',    'description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
				[ 'icon' => 'ti-layers',     'title' => 'Interface Design.',   'description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
				[ 'icon' => 'ti-bar-chart',  'title' => 'Business Consulting.','description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
				[ 'icon' => 'ti-vector',     'title' => 'Branding.',           'description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
				[ 'icon' => 'ti-android',    'title' => 'App development.',    'description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
				[ 'icon' => 'ti-pencil-alt', 'title' => 'Content creation.',   'description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
			],
		],

		// CTA Section
		[
			'acf_fc_layout' => 'cta',
			'subtitle'      => 'We create for you',
			'heading'       => 'Entrust Your Project to Our Best Team of Professionals',
			'description'   => 'Have any project on mind? For immidiate support :',
			'phone'         => '+23 876 65 455',
		],

		// Testimonials
		[
			'acf_fc_layout' => 'testimonials',
			'subtitle'      => 'Clients testimonial',
			'heading'       => "Check what's our clients say about us",
			'testimonials'  => [
				[ 'text' => 'Quam maiores perspiciatis temporibus odio reiciendis error alias debitis atque consequuntur natus iusto recusandae numquam corrupti facilis blanditiis.',  'author_name' => 'Thomas Johnson', 'author_role' => 'Excutive Director, themefisher' ],
				[ 'text' => 'Consectetur adipisicing elit. Quam maiores perspiciatis temporibus odio reiciendis error alias debitis atque consequuntur natus iusto recusandae .', 'author_name' => 'Mickel hussy',    'author_role' => 'Excutive Director, themefisher' ],
				[ 'text' => 'Quam maiores perspiciatis temporibus odio reiciendis error alias debitis atque consequuntur natus iusto recusandae numquam corrupti.',               'author_name' => 'James Watson',    'author_role' => 'Excutive Director, themefisher' ],
				[ 'text' => 'Consectetur adipisicing elit. Quam maiores perspiciatis temporibus odio reiciendis error alias debitis atque consequuntur natus iusto recusandae .', 'author_name' => 'Mickel hussy',    'author_role' => 'Excutive Director, themefisher' ],
			],
		],

		// Latest Blog
		[
			'acf_fc_layout' => 'latest_blog',
			'subtitle'      => 'Latest News',
			'heading'       => 'Latest articles to enrich knowledge',
		],

		// CTA Block
		[
			'acf_fc_layout' => 'cta_block',
			'subtitle'      => 'For Every type business',
			'heading'       => 'Entrust Your Project to Our Best Team of Professionals',
			'button_label'  => 'Contact Us',
			'button_url'    => '/contact',
		],

	], $home_id );
	$log[] = '  + Home sections saved';

	// ── 3. ABOUT PAGE ─────────────────────────────────────────────────────────

	$log[] = ''; $log[] = '=== Populating About Page ===';

	update_field( 'field_page_sections', [

		[ 'acf_fc_layout' => 'page_title', 'subtitle' => 'About Us', 'heading' => 'Our Company', 'breadcrumb_label' => 'About Us' ],

		[
			'acf_fc_layout' => 'about_detail',
			'subtitle'      => 'What we are',
			'heading'       => 'We are dynamic team of creative people',
			'description'   => 'We provide consulting services in the area of IFRS and management reporting, helping companies to reach their highest level. We optimize business processes, making them easier.',
			'button_label'  => 'Get started',
			'button_url'    => '#',
			'info_items'    => [
				[ 'number' => '01', 'title' => 'Our Mission', 'description' => 'Illum similique ducimus accusamus laudantium praesentium, impedit quaerat, itaque maxime sunt deleniti voluptas distinctio.' ],
				[ 'number' => '02', 'title' => 'Vission',     'description' => 'Illum similique ducimus accusamus laudantium praesentium, impedit quaerat, itaque maxime sunt deleniti voluptas distinctio.' ],
				[ 'number' => '03', 'title' => 'Our Approach','description' => 'Illum similique ducimus accusamus laudantium praesentium, impedit quaerat, itaque maxime sunt deleniti voluptas distinctio.' ],
			],
		],

		[
			'acf_fc_layout' => 'counter',
			'items'         => [
				[ 'value' => '1730', 'suffix' => '+', 'label' => 'Project Done',    'icon' => 'ti-check' ],
				[ 'value' => '125',  'suffix' => 'M', 'label' => 'User Worldwide',  'icon' => 'ti-flag' ],
				[ 'value' => '39',   'suffix' => '',  'label' => 'Availble Country','icon' => 'ti-layers' ],
				[ 'value' => '14',   'suffix' => '',  'label' => 'Award Winner',    'icon' => 'ti-medall' ],
			],
		],

		[
			'acf_fc_layout' => 'team',
			'subtitle'      => 'Our team',
			'heading'       => 'The people behind the megakit',
			'members'       => [
				[ 'name' => 'Justin hammer',  'role' => 'Digital Marketer',  'facebook_url' => '#', 'twitter_url' => '#', 'instagram_url' => '#', 'linkedin_url' => '#' ],
				[ 'name' => 'Jason roy',      'role' => 'UI/UX Designer',    'facebook_url' => '#', 'twitter_url' => '#', 'instagram_url' => '#', 'linkedin_url' => '#' ],
				[ 'name' => 'Henry oswald',   'role' => 'Developer',         'facebook_url' => '#', 'twitter_url' => '#', 'instagram_url' => '#', 'linkedin_url' => '#' ],
				[ 'name' => 'David Williams', 'role' => 'Senior Marketer',   'facebook_url' => '#', 'twitter_url' => '#', 'instagram_url' => '#', 'linkedin_url' => '#' ],
				[ 'name' => 'Peter Odin',     'role' => 'App Developer',     'facebook_url' => '#', 'twitter_url' => '#', 'instagram_url' => '#', 'linkedin_url' => '#' ],
				[ 'name' => 'David Spensor',  'role' => 'Project Manager',   'facebook_url' => '#', 'twitter_url' => '#', 'instagram_url' => '#', 'linkedin_url' => '#' ],
			],
		],

		[
			'acf_fc_layout' => 'testimonials',
			'subtitle'      => 'Clients testimonial',
			'heading'       => "Check what's our clients say about us",
			'testimonials'  => [
				[ 'text' => 'Quam maiores perspiciatis temporibus odio reiciendis error alias debitis atque consequuntur natus iusto recusandae numquam corrupti facilis blanditiis.',  'author_name' => 'Thomas Johnson', 'author_role' => 'Excutive Director, themefisher' ],
				[ 'text' => 'Consectetur adipisicing elit. Quam maiores perspiciatis temporibus odio reiciendis error alias debitis atque consequuntur natus iusto recusandae .', 'author_name' => 'Mickel hussy',    'author_role' => 'Excutive Director, themefisher' ],
				[ 'text' => 'Quam maiores perspiciatis temporibus odio reiciendis error alias debitis atque consequuntur natus iusto recusandae numquam corrupti.',               'author_name' => 'James Watson',    'author_role' => 'Excutive Director, themefisher' ],
				[ 'text' => 'Consectetur adipisicing elit. Quam maiores perspiciatis temporibus odio reiciendis error alias debitis atque consequuntur natus iusto recusandae .', 'author_name' => 'Mickel hussy',    'author_role' => 'Excutive Director, themefisher' ],
			],
		],

	], $about_id );
	$log[] = '  + About sections saved';

	// ── 4. SERVICES PAGE ──────────────────────────────────────────────────────

	$log[] = ''; $log[] = '=== Populating Services Page ===';

	update_field( 'field_page_sections', [

		[ 'acf_fc_layout' => 'page_title', 'subtitle' => 'Our services', 'heading' => 'What We Do', 'breadcrumb_label' => 'Our services' ],

		[
			'acf_fc_layout' => 'services',
			'subtitle'      => 'Our Services',
			'heading'       => 'We provide a wide range of creative services',
			'services'      => [
				[ 'icon' => 'ti-desktop',    'title' => 'Web development.',    'description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
				[ 'icon' => 'ti-layers',     'title' => 'Interface Design.',   'description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
				[ 'icon' => 'ti-bar-chart',  'title' => 'Business Consulting.','description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
				[ 'icon' => 'ti-vector',     'title' => 'Branding.',           'description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
				[ 'icon' => 'ti-android',    'title' => 'App development.',    'description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
				[ 'icon' => 'ti-pencil-alt', 'title' => 'Content creation.',   'description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
				[ 'icon' => 'ti-layers',     'title' => 'Interface Design.',   'description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
				[ 'icon' => 'ti-bar-chart',  'title' => 'Business Consulting.','description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
				[ 'icon' => 'ti-vector',     'title' => 'Branding.',           'description' => "A digital agency isn't here to replace your internal team, we're here to partner" ],
			],
		],

		[
			'acf_fc_layout' => 'cta_block',
			'subtitle'      => 'For Every type business',
			'heading'       => 'Entrust Your Project to Our Best Team of Professionals',
			'button_label'  => 'Contact Us',
			'button_url'    => '/contact',
		],

	], $services_id );
	$log[] = '  + Services sections saved';

	// ── 5. PORTFOLIO PAGE ─────────────────────────────────────────────────────

	$log[] = ''; $log[] = '=== Populating Portfolio Page ===';

	update_field( 'field_page_sections', [

		[ 'acf_fc_layout' => 'page_title', 'subtitle' => 'Latest works', 'heading' => 'Portfolio', 'breadcrumb_label' => 'Latest works' ],

		[
			'acf_fc_layout' => 'portfolio',
			'subtitle'      => 'Our works',
			'heading'       => 'We have done lots of works, lets check some',
		],

	], $portfolio_id );
	$log[] = '  + Portfolio sections saved';

	// ── 6. PRICING PAGE ───────────────────────────────────────────────────────

	$log[] = ''; $log[] = '=== Populating Pricing Page ===';

	update_field( 'field_page_sections', [

		[ 'acf_fc_layout' => 'page_title', 'subtitle' => 'Our pricing', 'heading' => 'Pricing Package', 'breadcrumb_label' => 'Our pricing' ],

		[
			'acf_fc_layout' => 'intro_features',
			'subtitle'      => 'We are creative',
			'heading'       => 'We provide best solution to client with their business problem',
			'features'      => [
				[ 'icon' => 'ti-wand',   'title' => 'Modern & Responsive design', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Earum, aspernatur.' ],
				[ 'icon' => 'ti-medall', 'title' => 'Awarded licensed company',   'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Earum, aspernatur.' ],
			],
		],

		[
			'acf_fc_layout' => 'pricing',
			'subtitle'      => 'Our Pricing',
			'heading'       => 'No hidden Charges.Choose Your Perfect Plan',
			'plans'         => [
				[ 'name' => 'Free',    'price' => '$0',  'period' => 'Per User / Month', 'features' => "Up to 1 User\nMax 100 Item\n500 Queries\nBasic Statistics",                        'button_label' => 'Download Now', 'button_url' => '#', 'highlighted' => false ],
				[ 'name' => 'Basic',   'price' => '$12', 'period' => 'Per User / Month', 'features' => "Up to 5 User\nMax 1000 Item\n5000 Queries\nStandard Statistics",                   'button_label' => 'Signup Now',   'button_url' => '#', 'highlighted' => true  ],
				[ 'name' => 'Premium', 'price' => '$39', 'period' => 'Per User / Month', 'features' => "Unlimited User\nUnlimited Item\nUnlimited Queries\nFull Statistics",               'button_label' => 'Download Now', 'button_url' => '#', 'highlighted' => false ],
			],
		],

		[
			'acf_fc_layout' => 'cta_block',
			'subtitle'      => 'For Every type business',
			'heading'       => 'Entrust Your Project to Our Best Team of Professionals',
			'button_label'  => 'Contact Us',
			'button_url'    => '/contact',
		],

	], $pricing_id );
	$log[] = '  + Pricing sections saved';

	// ── 7. CONTACT PAGE ───────────────────────────────────────────────────────

	$log[] = ''; $log[] = '=== Populating Contact Page ===';

	update_field( 'field_page_sections', [

		[ 'acf_fc_layout' => 'page_title', 'subtitle' => 'Contact Us', 'heading' => 'Get in Touch', 'breadcrumb_label' => 'Contact Us' ],

		[
			'acf_fc_layout' => 'contact_info',
			'address'       => 'North Main Street, Brooklyn Australia',
			'email'         => 'contact@mail.com',
			'phone'         => '+88 01672 506 744',
			'facebook_url'  => 'https://www.facebook.com/themefisher',
			'twitter_url'   => 'https://twitter.com/themefisher',
			'linkedin_url'  => 'https://www.pinterest.com/themefisher/',
		],

	], $contact_id );
	$log[] = '  + Contact sections saved';

	// ── 8. GLOBAL SETTINGS ────────────────────────────────────────────────────

	$log[] = ''; $log[] = '=== Saving Global Settings ===';

	update_field( 'site_name',             'Megakit',                                        'options' );
	update_field( 'tagline',               'Html5 Agency Template',                         'options' );
	update_field( 'phone',                 '+23-345-67890',                                 'options' );
	update_field( 'email',                 'support@gmail.com',                             'options' );
	update_field( 'address',               'North Main Street, Brooklyn Australia',         'options' );
	update_field( 'facebook_url',          'https://www.facebook.com/themefisher',          'options' );
	update_field( 'twitter_url',           'https://twitter.com/themefisher',               'options' );
	update_field( 'github_url',            'https://github.com/themefisher/',               'options' );
	update_field( 'linkedin_url',          'https://www.pinterest.com/themefisher/',        'options' );
	update_field( 'copyright_text',        '&copy; Copyright Reserved to <span class="text-color">Megakit.</span> by <a href="https://themefisher.com/" target="_blank">Themefisher</a>', 'options' );
	update_field( 'footer_subscribe_text', 'Subscribe to get latest news article and resources', 'options' );
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
	$log[] = '  + Global settings saved';

	// ── 9. PORTFOLIO ITEMS ────────────────────────────────────────────────────

	$log[] = ''; $log[] = '=== Creating Portfolio Items ===';

	for ( $i = 1; $i <= 6; $i++ ) {
		$title    = 'Project california';
		$existing = get_posts([ 'post_type' => 'portfolio_item', 'post_status' => 'publish', 'posts_per_page' => 1, 'title' => $title . ' ' . $i ]);
		if ( ! empty( $existing ) ) { $log[] = "  -> Skipped: Project {$i}"; continue; }
		$pid = wp_insert_post([ 'post_title' => $title, 'post_status' => 'publish', 'post_type' => 'portfolio_item' ]);
		if ( ! is_wp_error( $pid ) ) {
			update_field( 'category', 'Web Development', $pid );
			$log[] = "  + Portfolio item {$i}: Project california";
		}
	}

	// ── 10. BLOG POSTS ────────────────────────────────────────────────────────

	$log[] = ''; $log[] = '=== Creating Sample Blog Posts ===';

	$blog_posts = [
		[
			'title'    => 'How to improve design with typography?',
			'slug'     => 'how-to-improve-design-with-typography',
			'excerpt'  => 'Typography is one of the most powerful tools in a designer\'s toolkit. Learn how to use it effectively.',
			'content'  => '<p>Typography is one of the most powerful tools in a designer\'s toolkit. The right typeface can convey personality, establish hierarchy, and guide the reader\'s eye through your layout.</p><h2>Choose Typefaces That Work Together</h2><p>Pairing fonts is an art. A common approach is to combine a serif typeface for headings with a clean sans-serif for body text. The contrast creates visual interest while maintaining readability.</p><h2>Establish a Clear Hierarchy</h2><p>Use size, weight, and colour to create a typographic hierarchy that guides the reader naturally through the content — from headline to subheading to body copy.</p><h2>Mind Your Line Length</h2><p>For comfortable reading, aim for 50–75 characters per line. Lines that are too long or too short disrupt the reading flow and make text harder to digest.</p>',
			'category' => 'Design',
		],
		[
			'title'    => 'Interactivity design may connect consumer',
			'slug'     => 'interactivity-design-may-connect-consumer',
			'excerpt'  => 'Interactive design bridges the gap between brands and their audiences in meaningful ways.',
			'content'  => '<p>In today\'s digital landscape, static design is no longer enough. Users expect experiences that respond to their actions, adapt to their needs, and feel alive.</p><h2>Micro-interactions Matter</h2><p>Small animations and feedback cues — a button that changes colour on hover, a form field that highlights on focus — communicate that the interface is responsive and alive. These micro-interactions build trust and delight.</p><h2>Design for Engagement</h2><p>Interactive elements like quizzes, sliders, and calculators keep users engaged far longer than passive content. They also provide valuable data about user preferences and behaviour.</p>',
			'category' => 'Design',
		],
		[
			'title'    => 'Marketing Strategy to bring more affect',
			'slug'     => 'marketing-strategy-to-bring-more-affect',
			'excerpt'  => 'A well-crafted marketing strategy can transform how your brand connects with customers.',
			'content'  => '<p>Great marketing doesn\'t happen by accident. Behind every successful campaign is a clear strategy built on audience insight, compelling messaging, and disciplined execution.</p><h2>Start with Your Audience</h2><p>Before crafting any message, understand exactly who you\'re talking to. What are their pain points? What motivates them? What channels do they use? The more precisely you can answer these questions, the more effective your marketing will be.</p><h2>Set Measurable Goals</h2><p>Vague goals produce vague results. Define specific, measurable objectives for each campaign — whether that\'s website traffic, leads generated, or conversions — and track them rigorously.</p>',
			'category' => 'Marketing',
		],
	];

	foreach ( $blog_posts as $post_data ) {
		$existing = get_page_by_path( $post_data['slug'], OBJECT, 'post' );
		if ( $existing ) { $log[] = "  -> Skipped (exists): {$post_data['title']}"; continue; }
		$cat    = get_term_by( 'name', $post_data['category'], 'category' );
		$cat_id = $cat ? $cat->term_id : ( wp_insert_term( $post_data['category'], 'category' )['term_id'] ?? 1 );
		$pid    = wp_insert_post([
			'post_title'    => $post_data['title'],
			'post_name'     => $post_data['slug'],
			'post_excerpt'  => $post_data['excerpt'],
			'post_content'  => $post_data['content'],
			'post_status'   => 'publish',
			'post_type'     => 'post',
			'post_category' => [ $cat_id ],
		]);
		if ( ! is_wp_error($pid) ) $log[] = "  + Blog post: {$post_data['title']}";
	}

	$log[] = ''; $log[] = '=== Done! ===';
	return [ $log, $errors ];
}
