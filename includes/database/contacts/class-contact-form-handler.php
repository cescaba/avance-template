<?php
/**
 * Contact Form Handler - USA GLOBAL_FORM_HANDLER
 *
 * 1. Global_Form_Handler::process() - Validaciones globales
 * 2. Form_Validators::validate_contact() - Validaciones custom
 * 3. Guardar en BD
 * 4. Generar URL WhatsApp
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
		// 1. OBTENER DATOS DEL POST
		$post_data = $this->get_form_data($_POST);

		// 2. PREPARAR PARA GLOBAL_FORM_HANDLER (mapear numero → whatsapp, dividir nombre)
		$nombre_completo = $post_data['nombre'] ?? '';
		$partes = explode(' ', trim($nombre_completo), 2);
		$data_for_global = array(
			'nombre' => $partes[0] ?? '',
			'apellido' => $partes[1] ?? $partes[0] ?? '',
			'email' => $post_data['email'] ?? '',
			'whatsapp' => $post_data['numero'] ?? '',
			'asunto' => $post_data['asunto'] ?? '',
			'mensaje' => $post_data['mensaje'] ?? '',
			'nonce' => $post_data['nonce'] ?? '',
		);

		// 3. GLOBAL_FORM_HANDLER::PROCESS()
		$validated = Global_Form_Handler::process('contact', $data_for_global);

		// 4. FORM_VALIDATORS::VALIDATE_CONTACT()
		Form_Validators::validate_contact($validated);

		// 5. PREPARAR DATOS PARA BD (mapear whatsapp → numero)
		$data_for_db = array(
			'nombre' => $validated['nombre'],
			'email' => $validated['email'],
			'numero' => $validated['whatsapp'],
			'asunto' => $validated['asunto'],
			'mensaje' => $validated['mensaje'],
			'whatsapp' => $validated['whatsapp'],  // Para build_whatsapp_message()
		);

		// 6. GUARDAR EN BD
		$contact_id = Avance_Contact_DB::save_contact($data_for_db);
		if (!$contact_id) {
			error_log('Error: No se pudo guardar en BD');
			wp_send_json_error(['message' => 'Error al guardar el contacto. Intenta de nuevo.'], 500);
		}

		// 7. GENERAR URL WHATSAPP
		$whatsapp_url = $this->build_whatsapp_url($data_for_db);
		error_log('Paso 4: ✅ OK');

		// 8. RESPUESTA
		wp_send_json_success([
			'message' => 'Mensaje guardado correctamente. Abriendo WhatsApp...',
			'contact_id' => $contact_id,
			'url' => $whatsapp_url,
		]);
	}

	private function get_form_data($post_data) {
		return array(
			'nombre' => $post_data['nombre'] ?? '',
			'email' => $post_data['email'] ?? '',
			'numero' => $post_data['numero'] ?? '',
			'asunto' => $post_data['asunto'] ?? '',
			'mensaje' => $post_data['mensaje'] ?? '',
			'nonce' => $post_data['nonce'] ?? '',
		);
	}

	/**
	 * Construir URL de WhatsApp
	 */
	private function build_whatsapp_url($data) {
		if (!defined('AVANCE_WHATSAPP_OWNER') || empty(AVANCE_WHATSAPP_OWNER)) {
			return '';
		}

		$owner_phone = AVANCE_WHATSAPP_OWNER;
		$message = $this->build_whatsapp_message($data);

		$phone = preg_replace('/[^0-9]/', '', $owner_phone);
		if (strlen($phone) === 9) {
			$phone = '51' . $phone;
		}

		return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
	}

	/**
	 * Construir mensaje para WhatsApp
	 */
	private function build_whatsapp_message($data) {
		$lines = array(
			'Nuevo contacto web:',
			'Nombre: ' . $data['nombre'],
			'Email: ' . $data['email'],
			'WhatsApp: ' . $data['whatsapp'],
			'Asunto: ' . $data['asunto'],
		);

		if (!empty($data['mensaje'])) {
			$lines[] = 'Mensaje: ' . $data['mensaje'];
		}

		return implode("\n", $lines);
	}
}

new Avance_Contact_Form_Handler();
