<?php
/**
 * Contact Form Handler
 *
 * Maneja el flujo completo del formulario
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Contact_Form_Handler {

	public function __construct() {
		add_action('wp_ajax_nopriv_avance_submit_contact', array($this, 'handle_ajax_submit'));
		add_action('wp_ajax_avance_submit_contact', array($this, 'handle_ajax_submit'));
	}

	/**
	 * Manejar envío del formulario via AJAX
	 */
	public function handle_ajax_submit() {
		// Verificar nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'avance_contact_form')) {
			wp_send_json_error(
				array(
					'message' => 'Error de seguridad. Por favor, recarga la página.',
				),
				400
			);
		}

		// Obtener datos del formulario
		$data = $this->get_form_data($_POST);

		// Sanitizar
		$data = Avance_Contact_Validator::sanitize($data);

		// Validar
		$validation = Avance_Contact_Validator::validate($data);

		if (!$validation['valid']) {
			wp_send_json_error(
				array(
					'message' => implode(', ', $validation['errors']),
					'reason' => $validation['reason'],
				),
				400
			);
		}

		// Guardar en BD (estado: pendiente)
		$contact_id = Avance_Contact_DB::save_contact($data);

		if (!$contact_id) {
			wp_send_json_error(
				array(
					'message' => 'Error al guardar el contacto. Intenta de nuevo.',
				),
				500
			);
		}

		// Enviar a WhatsApp
		$whatsapp_result = $this->send_to_whatsapp($data, $contact_id);

		if ($whatsapp_result['success']) {
			// Marcar como enviado
			Avance_Contact_DB::update_status($contact_id, 'enviado');

			wp_send_json_success(
				array(
					'message' => 'Mensaje enviado correctamente. Te contactaremos pronto.',
					'contact_id' => $contact_id,
				)
			);
		} else {
			// Marcar como error
			Avance_Contact_DB::update_status($contact_id, 'error', $whatsapp_result['reason']);

			wp_send_json_error(
				array(
					'message' => 'Ocurrió un error al enviar el mensaje. Intenta de nuevo.',
					'reason' => 'whatsapp_error',
				),
				500
			);
		}
	}

	/**
	 * Obtener datos del formulario
	 */
	private function get_form_data($post_data) {
		return array(
			'nombre' => $post_data['nombre'] ?? '',
			'email' => $post_data['email'] ?? '',
			'numero' => $post_data['numero'] ?? '',
			'asunto' => $post_data['asunto'] ?? '',
			'mensaje' => $post_data['mensaje'] ?? '',
		);
	}

	/**
	 * Enviar mensaje a WhatsApp
	 */
	private function send_to_whatsapp($data, $contact_id) {
		$owner_phone = AVANCE_WHATSAPP_OWNER;

		// Construir mensaje
		$message = $this->build_whatsapp_message($data);

		// Construir URL de wa.me
		$url = 'https://wa.me/' . $owner_phone . '?text=' . rawurlencode($message);

		// Intentar abrir WhatsApp (esto se hace desde el cliente, no desde el servidor)
		// El servidor solo construye el URL y lo retorna
		// El cliente será el que abra el link

		return array(
			'success' => true,
			'url' => $url,
			'reason' => 'whatsapp_ready',
		);
	}

	/**
	 * Construir mensaje para WhatsApp
	 */
	private function build_whatsapp_message($data) {
		$lines = array(
			'Nuevo contacto web:',
			'Nombre: ' . $data['nombre'],
			'Email: ' . $data['email'],
			'WhatsApp: ' . $data['numero'],
			'Asunto: ' . $data['asunto'],
		);

		if (!empty($data['mensaje'])) {
			$lines[] = 'Mensaje: ' . $data['mensaje'];
		}

		return implode("\n", $lines);
	}
}

// Inicializar handler
new Avance_Contact_Form_Handler();
