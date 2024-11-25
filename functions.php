<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 */

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/src/StarterSite.php';

Timber\Timber::init();

// Sets the directories (inside your theme) to find .twig files.
Timber::$dirname = [ 'templates', 'views' ];

new StarterSite();

add_action('init', function() {
	register_nav_menus([
		'primary' => __('Primary Menu', 'my-timber-theme'),
	]);
});

add_theme_support('custom-logo', [
	'height'      => 100,
	'width'       => 300,
	'flex-height' => true,
	'flex-width'  => true,
	'header-text' => ['site-title', 'site-description'],
]);


add_action('template_redirect', function() {
	$context = Timber::context();
	$context['site'] = new Timber\Site();

	if (has_custom_logo()) {
		$custom_logo_id = get_theme_mod('custom_logo');
		$context['site']->custom_logo = wp_get_attachment_image_url($custom_logo_id, 'full');
	}

	Timber::render('base.twig', $context);
});



function register_acf_blocks() {
	foreach ($blocks = new DirectoryIterator( __DIR__ . '/blocks' ) as $item) {
		// Check if block.json file exists in each subfolder.
		if ($item->isDir() && !$item->isDot()
		    && file_exists($item->getPathname() . '/block.json')
		) {
			// Register the block given the directory name within the blocks
			// directory.
			register_block_type($item -> getPathname());
		}
	}
}

add_action('init', 'register_acf_blocks');


/**
 * Render callback to prepare and display a registered block using Timber.
 *
 * @param    array    $attributes The block attributes.
 * @param    string   $content The block content.
 * @param    bool     $is_preview Whether or not the block is being rendered for editing preview.
 * @param    int      $post_id The current post being edited or viewed.
 * @param    WP_Block $wp_block The block instance (since WP 5.5).
 * @return   void
 */
function my_acf_block_render_callback($attributes, $content = '', $is_preview = false, $post_id = 0, $wp_block = null) {
	// Create the slug of the block using the name property in the block.json.
	$slug = str_replace( 'acf/', '', $attributes['name'] );

	$context = Timber::context();

	// Store block attributes.
	$context['attributes'] = $attributes;

	// Store field values. These are the fields from your ACF field group for the block.
	$context['fields'] = get_fields();

	// Store whether the block is being rendered in the editor or on the frontend.
	$context['is_preview'] = $is_preview;

	// Render the block.
	Timber::render(
		'blocks/' . $slug . '/' . $slug . '.twig',
		$context
	);
}

function add_header_button_link_to_context($context) {
	$context['header_button_link'] = get_field('phone_number', 'option');
	return $context;
}
add_filter('timber/context', 'add_header_button_link_to_context');

function add_logo_footer($context) {
	$context['logo_footer'] = get_field('logo', 'option');
	return $context;
}
add_filter('timber/context', 'add_logo_footer');

function enqueue_block_styles() {
	wp_enqueue_style(
		'theme-blocks',
		get_template_directory_uri() . '/dist/main.css',
		array(),
		filemtime(get_template_directory() . '/dist/main.css')
	);
}
add_action('wp_enqueue_scripts', 'enqueue_block_styles');

function my_theme_enqueue_scripts() {
	wp_enqueue_script(
		'main-script',
		get_template_directory_uri() . '/js/main.js',
		array('jquery'),
		'1.0',
		true
	);
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_scripts');


//$paged = get_query_var('paged') ? get_query_var('paged') : 1;
//
//$context['posts'] = Timber::get_posts(array(
//    'post_type' => 'post',
//    'posts_per_page' => 12,
//    'paged' => $paged,
//));
//
//$max_pages = $GLOBALS['wp_query']->max_num_pages;
//
//if ($max_pages > 1) {
//    $context['pagination'] = paginate_links(array(
//        'base' => get_pagenum_link(1) . '%_%',
//        'format' => '/page/%#%',
//        'current' => $paged,
//        'total' => $max_pages,
//        'prev_text' => '&laquo; Prev',
//        'next_text' => 'Next &raquo;',
//    ));
//}
//Timber::render('blocks/filter-project/filter-project.twig', $context);


function get_breadcrumbs() {
	$breadcrumbs = [];
	$home_url = home_url('/');
	$breadcrumbs[] = [
		'url' => $home_url,
		'title' => __('Home', 'archizem'),
	];

	if (is_singular()) {
		$post = get_post();
		if ($post->post_parent) {
			$ancestors = array_reverse(get_post_ancestors($post->ID));
			foreach ($ancestors as $ancestor) {
				$breadcrumbs[] = [
					'url' => get_permalink($ancestor),
					'title' => get_the_title($ancestor),
				];
			}
		}
		$breadcrumbs[] = [
			'url' => get_permalink($post),
			'title' => get_the_title($post),
		];
	} elseif (is_archive()) {
		$breadcrumbs[] = [
			'url' => get_post_type_archive_link(get_post_type()),
			'title' => post_type_archive_title('', false),
		];
	} elseif (is_search()) {
		$breadcrumbs[] = [
			'url' => get_search_link(),
			'title' => sprintf(__('Search: %s', 'archizem'), get_search_query()),
		];
	} elseif (is_404()) {
		$breadcrumbs[] = [
			'url' => '',
			'title' => __('404 Error', 'archizem'),
		];
	}

	return $breadcrumbs;
}


//use Timber\Timber;
//use Timber\PostQuery;
//
//$context = Timber::context();
//
//$args = [
//	'post_type'      => 'post',
//	'posts_per_page' => 5,
//	'paged'          => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
//];
//
//$context['posts']      = new PostQuery( $args );
//$context['pagination'] = Timber::get_pagination();
//
//// Передача даних до Twig
//Timber::render( 'blocks/filter-project/filter-project.twig', $context );

//function fix_pagination($query) {
//	if (!is_admin() && $query->is_main_query()) {
//		if ($query->is_home() || $query->is_archive()) {
//			$query->set('paged', get_query_var('paged') ? get_query_var('paged') : 1);
//		}
//	}
//}
//add_action('pre_get_posts', 'fix_pagination');
