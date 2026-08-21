<?php
/**
 * Appointment Handler - AJAX + WhatsApp
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Appointment_Handler {

	public function __construct() {
		add_action('wp_ajax_nopriv_avance_submit_appointment', array($this, 'handle_ajax_submit'));
		add_action('wp_ajax_avance_submit_appointment', array($this, 'handle_ajax_submit'));
	}

	/**
	 * Manejar envío del agendamiento via AJAX
	 */
	public function handle_ajax_submit() {
		// Verificar nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'avance_appointment_form')) {
			wp_send_json_error(array('message' => 'Error de seguridad'));
		}

		// Obtener datos
		$data = array(
			'nombre' => $_POST['nombre'] ?? '',
			'whatsapp' => $_POST['whatsapp'] ?? '',
			'servicio' => $_POST['servicio'] ?? '',
			'fecha' => $_POST['fecha'] ?? '',
			'hora' => $_POST['hora'] ?? '',
			'notas' => $_POST['notas'] ?? '',
		);

		// Sanitizar
		$data = Avance_Appointment_Validator::sanitize($data);

		// Validar
		$validation = Avance_Appointment_Validator::validate($data);
		if (!$validation['valid']) {
			wp_send_json_error(array('errors' => $validation['errors']));
		}

		// Guardar en BD
		$appointment_id = Avance_Appointment_DB::save_appointment($data);
		if (!$appointment_id) {
			wp_send_json_error(array('message' => 'Error al guardar la cita'));
		}

		// Enviar a WhatsApp
		$whatsapp_result = $this->send_to_whatsapp($data, $appointment_id);
		if ($whatsapp_result['success']) {
			Avance_Appointment_DB::update_status($appointment_id, 'confirmada');
			wp_send_json_success(array(
				'message' => 'Cita agendada correctamente. Te enviaremos detalles por WhatsApp.',
				'appointment_id' => $appointment_id,
				'url' => $whatsapp_result['url'],
			));
		} else {
			wp_send_json_error(array('message' => 'Error al enviar a WhatsApp'));
		}
	}

	/**
	 * Enviar mensaje a WhatsApp
	 */
	private function send_to_whatsapp($data, $appointment_id) {
		if (!defined('AVANCE_WHATSAPP_OWNER')) {
			return array('success' => false);
		}

		$owner_phone = AVANCE_WHATSAPP_OWNER;
		$message = $this->build_whatsapp_message($data, $appointment_id);
		// Normalizar número para WhatsApp (agregar +51 si falta)
		$phone = preg_replace('/[^0-9]/', '', $owner_phone);
		if (strlen($phone) === 9) {
			$phone = '51' . $phone;
		}

		$url = 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);

		return array(
			'success' => true,
			'url' => $url,
		);
	}

	/**
	 * Construir mensaje para WhatsApp
	 */
	private function build_whatsapp_message($data, $appointment_id) {
		$lines = array(
			'📅 Nueva Cita Agendada - ID: ' . $appointment_id,
			'',
			'Nombre: ' . $data['nombre'],
			'WhatsApp: ' . $data['whatsapp'],
			'Servicio: ' . $data['servicio'],
			'Fecha: ' . wp_date('d/m/Y', strtotime($data['fecha'])),
			'Hora: ' . $data['hora'],
		);

		if (!empty($data['notas'])) {
			$lines[] = 'Notas: ' . $data['notas'];
		}

		return implode("\n", $lines);
	}
}
