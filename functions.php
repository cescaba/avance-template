<?php
/**
 * Avance Template - Functions
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Enqueue styles and scripts
 */
function avance_template_enqueue_assets() {
	$theme_uri = get_template_directory_uri();

	// CSS
	wp_enqueue_style(
		'avance-base',
		$theme_uri . '/assets/css/base.css',
		array(),
		wp_get_theme()->get('Version'),
		'all'
	);

	wp_enqueue_style(
		'avance-pages',
		$theme_uri . '/assets/css/pages.css',
		array('avance-base'),
		wp_get_theme()->get('Version'),
		'all'
	);

	// Page-specific styles and scripts - Mentorías
	if (is_page_template('templates/page-mentoria.php')) {
		wp_enqueue_style(
			'avance-page-mentoria',
			$theme_uri . '/assets/css/page-mentoria.css',
			array('avance-base'),
			wp_get_theme()->get('Version'),
			'all'
		);

		wp_enqueue_script(
			'avance-faq-accordion',
			$theme_uri . '/assets/js/faq-accordion.js',
			array(),
			wp_get_theme()->get('Version'),
			true
		);
	}

	// Page-specific styles - Servicios Empresa
	if (is_page_template('templates/page-servicio-empresa.php')) {
		wp_enqueue_style(
			'avance-page-servicio-empresa',
			$theme_uri . '/assets/css/page-servicio-empresa.css',
			array('avance-base'),
			wp_get_theme()->get('Version'),
			'all'
		);
	}

	// WordPress styles
	wp_enqueue_style('wp-block-library');

	// JavaScript - Animations (Scroll + Counter)
	wp_enqueue_script(
		'avance-animations',
		$theme_uri . '/assets/js/animations.js',
		array(),
		wp_get_theme()->get('Version'),
		true
	);
}
add_action('wp_enqueue_scripts', 'avance_template_enqueue_assets');

/**
 * Setup theme features
 */
function avance_template_setup() {
	// Add theme support
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('custom-logo');
	add_theme_support('html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	));

	// Register navigation menus
	register_nav_menus(array(
		'primary' => esc_html__('Primary Menu', 'avance-template'),
	));
}
add_action('after_setup_theme', 'avance_template_setup');

/**
 * Register widgets
 */
function avance_template_widgets_init() {
	register_sidebar(array(
		'name'          => esc_html__('Footer Widget Area', 'avance-template'),
		'id'            => 'footer-1',
		'description'   => esc_html__('Footer widget area', 'avance-template'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));
}
add_action('widgets_init', 'avance_template_widgets_init');

/**
 * Crear páginas requeridas automáticamente
 */
function avance_template_create_pages() {
	$pages = array(
		array(
			'post_title'   => 'Mentorías',
			'post_name'    => 'mentorias',
			'post_content' => '',
			'page_template' => 'templates/page-mentoria.php',
		),
		array(
			'post_title'   => 'Servicios Empresa',
			'post_name'    => 'servicio-empresa',
			'post_content' => '',
			'page_template' => 'templates/page-servicio-empresa.php',
		),
	);

	foreach ($pages as $page) {
		$existing = get_page_by_path($page['post_name']);

		if (!$existing) {
			$page_id = wp_insert_post(array(
				'post_type'   => 'page',
				'post_title'  => $page['post_title'],
				'post_name'   => $page['post_name'],
				'post_content' => $page['post_content'],
				'post_status' => 'publish',
			));

			if ($page_id && isset($page['page_template'])) {
				update_post_meta($page_id, '_wp_page_template', $page['page_template']);
			}
		}
	}
}
add_action('after_setup_theme', 'avance_template_create_pages');
