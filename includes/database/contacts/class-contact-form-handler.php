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
		$post_data = $this->get_form_data($_POST);

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

		$validated = Global_Form_Handler::process('contact', $data_for_global);
		Form_Validators::validate_contact($validated);

		// Validar duplicados por WhatsApp en últimas 24 horas
		if ($this->is_whatsapp_duplicate($validated['whatsapp'])) {
			wp_send_json_error(['message' => 'Este número de WhatsApp ya fue registrado en las últimas 24 horas. Intenta mañana.'], 409);
		}

		$data_for_db = array(
			'nombre' => $validated['nombre'],
			'email' => $validated['email'],
			'numero' => $validated['whatsapp'],
			'asunto' => $validated['asunto'],
			'mensaje' => $validated['mensaje'],
			'whatsapp' => $validated['whatsapp'],
		);

		$contact_id = Avance_Contact_DB::save_contact($data_for_db);
		if (!$contact_id) {
			error_log('Error: No se pudo guardar en BD');
			wp_send_json_error(['message' => 'Error al guardar el contacto. Intenta de nuevo.'], 500);
		}

		$ip = Avance_Rate_Limiter::get_client_ip();
		Avance_Rate_Limiter::log_attempt($ip, $validated['email'], 'success');

		$whatsapp_url = $this->build_whatsapp_url($data_for_db);
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
			'Hola, buenos días.',
			'Acabo de completar el formulario multiservicio y te comparto mis datos:',
			'',
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

	/**
	 * Verificar si WhatsApp ya fue usado en últimas 24 horas
	 */
	private function is_whatsapp_duplicate($whatsapp) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'avance_contacts';

		$count = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM $table_name
			 WHERE numero = %s AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)",
			$whatsapp
		));

		return $count > 0;
	}
}

new Avance_Contact_Form_Handler();
