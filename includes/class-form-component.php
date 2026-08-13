<?php
/**
 * Form Component
 *
 * Componente reutilizable para formularios de contacto
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Form_Component {

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
			'label' => 'FORMULARIO MULTISERVICIO',
			'title' => '¿Listo para dar el siguiente paso?',
			'description' => 'Completa el formulario y me contacto en menos de 24h.',
			'button_text' => 'Enviar mensaje',
			'whatsapp_text' => 'Escribir al WhatsApp',
			'show_fields' => array(
				'nombre' => true,
				'email' => true,
				'numero' => true,
				'asunto' => true,
				'mensaje' => true,
			),
			'container_class' => 'home-form',
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
		get_template_part('template-parts/form-section', null, $this->config);
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
			'avance-contact-whatsapp',
			$theme_uri . '/assets/js/contact-whatsapp.js',
			array(),
			wp_get_theme()->get('Version'),
			true
		);

		wp_localize_script(
			'avance-contact-whatsapp',
			'avanceFormConfig',
			array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('avance_contact_form'),
			)
		);
	}
}
