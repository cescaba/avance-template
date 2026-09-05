<?php
/**
 * Performance Optimizer - Optimizaciones de rendimiento
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Performance_Optimizer {

	public function __construct() {
		// Google Fonts preload
		add_action('wp_head', [$this, 'preload_google_fonts'], 1);

		// Remover emojis
		$this->disable_emojis();

		// Lazy load images
		add_filter('wp_get_attachment_image_attributes', [$this, 'enable_lazy_load']);

		// Comprimir HTML
		$this->compress_html();

		// Optimizar jQuery
		add_action('wp_default_scripts', [$this, 'optimize_jquery']);

		// Remover versión de WordPress
		remove_action('wp_head', 'wp_generator');
	}

	public function preload_google_fonts() {
		echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
		echo '<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">';
		echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">';
	}

	private function disable_emojis() {
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('admin_print_scripts', 'print_emoji_detection_script');
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_action('admin_print_styles', 'print_emoji_styles');
		remove_filter('wp_mail', 'wp_staticize_emoji');
		remove_filter('the_content_feed', 'wp_staticize_emoji');
		remove_filter('comment_text_rss', 'wp_staticize_emoji');
		remove_filter('the_excerpt', 'wp_staticize_emoji');
	}

	public function enable_lazy_load($attr) {
		if (!isset($attr['loading'])) {
			$attr['loading'] = 'lazy';
		}
		return $attr;
	}

	private function compress_html() {
		if (!defined('WP_DEBUG') || !WP_DEBUG) {
			add_action('init', function() {
				ob_start(function($buffer) {
					$buffer = preg_replace('/\s+/', ' ', $buffer);
					$buffer = preg_replace('/>\s+</', '><', $buffer);
					return $buffer;
				});
			});
		}
	}

	public function optimize_jquery($scripts) {
		if (!is_admin() && !empty($scripts->registered['jquery'])) {
			$scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']);
		}
	}
}
