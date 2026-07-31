<?php
/**
 * Avance Nativo - Funciones del tema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Soporte del tema
add_action( 'after_setup_theme', function() {
	load_theme_textdomain( 'avance-nativo', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
	add_theme_support( 'responsive-embeds' );
	register_nav_menus( array(
		'primary' => 'Menú Principal',
		'footer' => 'Menú Footer',
	) );
} );

// Enqueue styles and scripts
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'avance-nativo', get_stylesheet_uri(), array(), '1.0.0' );
	wp_enqueue_script( 'avance-nativo', get_template_directory_uri() . '/js/main.js', array(), '1.0.0', true );
} );

// Widgets
add_action( 'widgets_init', function() {
	register_sidebar( array(
		'name' => 'Footer 1',
		'id' => 'footer-1',
		'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => 'Footer 2',
		'id' => 'footer-2',
		'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => 'Footer 3',
		'id' => 'footer-3',
		'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
} );

// Utilidades
function avance_nativo_get_site_title() {
	return esc_html( get_bloginfo( 'name' ) );
}

function avance_nativo_get_site_description() {
	$desc = get_bloginfo( 'description', 'display' );
	return ! empty( $desc ) ? esc_html( $desc ) : '';
}

function avance_nativo_get_copyright_year() {
	return date_i18n( 'Y' );
}

// Seguridad
remove_action( 'wp_head', 'wp_generator' );
