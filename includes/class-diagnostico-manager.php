<?php
/**
 * Diagnostico Manager
 * Orquesta el flujo completo del diagnóstico
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Diagnostico_Manager {
	private static $instance = null;

	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		$this->init();
	}

	private function init() {
		require_once get_template_directory() . '/includes/database/class-diagnostico-table.php';
		require_once get_template_directory() . '/includes/validators/class-diagnostico-validator.php';
		require_once get_template_directory() . '/includes/whatsapp/class-whatsapp-service.php';
		require_once get_template_directory() . '/includes/api/class-diagnostico-handler.php';
		require_once get_template_directory() . '/includes/admin/class-diagnostico-admin.php';

		// Crear tabla al activar tema
		add_action('wp_loaded', array($this, 'maybe_create_table'));

		// Registrar endpoint AJAX
		add_action('wp_ajax_nopriv_avance_submit_diagnostico', array($this, 'handle_diagnostico_submission'));
		add_action('wp_ajax_avance_submit_diagnostico', array($this, 'handle_diagnostico_submission'));

		// Encolar scripts
		add_action('wp_enqueue_scripts', array($this, 'enqueue_diagnostico_scripts'));
	}

	public function maybe_create_table() {
		if (!function_exists('Avance_Diagnostico_Table')) {
			$table = new Avance_Diagnostico_Table();
			$table->create_table_if_not_exists();
		}
	}

	public function handle_diagnostico_submission() {
		$handler = new Avance_Diagnostico_Handler();
		$handler->process();
	}

	public function enqueue_diagnostico_scripts() {
		if (is_page_template('templates/page-diagnostico.php')) {
			wp_enqueue_script(
				'avance-diagnostico-submit',
				get_template_directory_uri() . '/assets/js/diagnostico-submit.js',
				array(),
				wp_get_theme()->get('Version'),
				true
			);

			wp_localize_script('avance-diagnostico-submit', 'avanceDiagnosticoConfig', array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'nonce'   => wp_create_nonce('avance_diagnostico_nonce'),
			));
		}
	}
}

// Inicializar
Avance_Diagnostico_Manager::get_instance();
