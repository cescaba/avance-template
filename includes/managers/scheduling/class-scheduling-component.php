<?php
/**
 * Scheduling Component
 *
 * Componente reutilizable para la sección de agendamiento
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Scheduling_Component {

	/**
	 * Configuración del componente
	 *
	 * @var array
	 */
	private $config = array();

	/**
	 * Constructor
	 *
	 * @param array $config Configuración personalizada
	 */
	public function __construct($config = array()) {
		$defaults = array(
			'title' => 'Sesión gratuita de 10 min',
			'subtitle' => 'Google Meet · Sin compromiso',
			'kicker' => 'Agendamiento directo',
			'show_whatsapp_btn' => true,
			'whatsapp_number' => '51993508652',
			'show_feature_cards' => true,
			'container_class' => 'contacto-agenda',
		);

		$this->config = wp_parse_args($config, $defaults);
	}

	/**
	 * Renderizar el componente
	 *
	 * @return void
	 */
	public function render() {
		$this->enqueue_assets();
		echo $this->get_content();
	}

	/**
	 * Obtener contenido HTML del componente
	 *
	 * @return string
	 */
	private function get_content() {
		ob_start();
		get_template_part('template-parts/scheduling-section', null, $this->config);
		return ob_get_clean();
	}

	/**
	 * Obtener configuración
	 *
	 * @return array
	 */
	public function get_config() {
		return $this->config;
	}

	/**
	 * Enqueue scripts y estilos
	 *
	 * @return void
	 */
	public static function enqueue_assets() {
		$theme_uri = get_template_directory_uri();

		wp_enqueue_script(
			'avance-contacto-agenda',
			$theme_uri . '/assets/js/contacto-agenda.js',
			array(),
			wp_get_theme()->get('Version'),
			true
		);

		wp_localize_script(
			'avance-contacto-agenda',
			'avanceAgendamientoConfig',
			array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('avance_agendamiento_form'),
			)
		);
	}
}
