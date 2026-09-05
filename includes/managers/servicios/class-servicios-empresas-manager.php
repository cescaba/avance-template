<?php
/**
 * Servicios Empresas Manager
 * Orquesta el flujo completo de propuestas de servicios
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Servicios_Empresas_Manager {
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
		require_once get_template_directory() . '/includes/database/servicios/class-servicio-empresa-db.php';
		require_once get_template_directory() . '/includes/api/class-servicio-empresa-handler.php';
		require_once get_template_directory() . '/includes/admin/servicios/class-servicios-empresas-admin.php';

		// Crear tabla al cargar
		add_action('wp_loaded', array($this, 'maybe_create_table'));

		// Registrar endpoint AJAX
		add_action('wp_ajax_nopriv_avance_submit_servicio_empresa', array($this, 'handle_submission'));
		add_action('wp_ajax_avance_submit_servicio_empresa', array($this, 'handle_submission'));
	}

	public function maybe_create_table() {
		if (class_exists('Avance_Servicio_Empresa_DB')) {
			Avance_Servicio_Empresa_DB::init();
			Avance_Servicio_Empresa_DB::create_table();
		}
	}

	public function handle_submission() {
		$handler = new Avance_Servicio_Empresa_Handler();
		$handler->process();
	}
}

// Inicializar
Avance_Servicios_Empresas_Manager::get_instance();
