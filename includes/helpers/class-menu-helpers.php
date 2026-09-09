<?php
/**
 * Menu Helpers - Enqueue y gestión de scripts del menú
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Menu_Helpers {

	public function __construct() {
		add_action('wp_enqueue_scripts', [$this, 'enqueue_menu_script']);
	}

	/**
	 * Enqueue script del menú via WordPress (NO inline)
	 */
	public function enqueue_menu_script() {
		$script_path = get_template_directory() . '/assets/js/avance-menu.js';
		if (!file_exists($script_path)) {
			return;
		}

		wp_enqueue_script(
			'avance-menu',
			get_template_directory_uri() . '/assets/js/avance-menu.js',
			[],
			filemtime($script_path),
			true
		);
	}
}
