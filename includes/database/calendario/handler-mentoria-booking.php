<?php
/**
 * AJAX Handler - Booking Mentoría
 *
 * Guarda datos en wp_avance_calendario_reservas_mentoria
 * Luego envía a WooCommerce checkout
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

require_once get_template_directory() . '/includes/database/calendario/class-calendario-reservas-mentoria-db.php';

class Avance_Handler_Mentoria_Booking {

	public function __construct() {
		add_action('wp_ajax_nopriv_avance_save_mentoria_booking', [$this, 'handle_booking']);
		add_action('wp_ajax_avance_save_mentoria_booking', [$this, 'handle_booking']);
	}

	public function handle_booking() {
		$post_data = array(
			'first_name' => $_POST['first_name'] ?? '',
			'last_name' => $_POST['last_name'] ?? '',
			'email' => $_POST['email'] ?? '',
			'whatsapp' => $_POST['whatsapp'] ?? '',
			'desafio' => $_POST['desafio'] ?? '',
			'plan' => $_POST['plan'] ?? '',
			'fecha' => $_POST['fecha'] ?? '',
			'hora' => $_POST['hora'] ?? '',
			'nonce' => $_POST['nonce'] ?? '',
		);

		// Validar nonce
		if (!isset($post_data['nonce']) || !wp_verify_nonce($post_data['nonce'], 'avance_mentoria_booking')) {
			wp_send_json_error(['message' => 'Error de seguridad: token inválido.'], 400);
		}

		// Validar nombre
		$first_name = trim($post_data['first_name'] ?? '');
		$last_name = trim($post_data['last_name'] ?? '');
		if (empty($first_name) || empty($last_name)) {
			wp_send_json_error(['message' => 'El nombre completo es requerido.'], 400);
		}

		// Validar email
		$email = trim($post_data['email'] ?? '');
		if (empty($email) || !is_email($email)) {
			wp_send_json_error(['message' => 'El email es inválido.'], 400);
		}

		// Validar WhatsApp
		$whatsapp = trim($post_data['whatsapp'] ?? '');
		if (empty($whatsapp)) {
			wp_send_json_error(['message' => 'El WhatsApp es requerido.'], 400);
		}

		// Validar desafío
		$desafio = trim($post_data['desafio'] ?? '');
		if (empty($desafio)) {
			wp_send_json_error(['message' => 'El desafío es requerido.'], 400);
		}

		// Validar plan
		$plan = trim($post_data['plan'] ?? '');
		if (empty($plan)) {
			wp_send_json_error(['message' => 'El plan es requerido.'], 400);
		}

		// Validar y convertir fecha
		$fecha_raw = trim($post_data['fecha'] ?? '');
		if (empty($fecha_raw)) {
			wp_send_json_error(['message' => 'La fecha es requerida.'], 400);
		}
		$fecha = $this->parse_date($fecha_raw);
		if (!$fecha) {
			wp_send_json_error(['message' => 'Formato de fecha inválido.'], 400);
		}

		// Validar hora
		$hora = trim($post_data['hora'] ?? '');
		if (empty($hora) || !preg_match('/^\d{2}:\d{2}$/', $hora)) {
			wp_send_json_error(['message' => 'La hora es inválida.'], 400);
		}

		// Verificar disponibilidad en calendario
		if (!Avance_Calendario_Reservas_Mentoria_DB::is_hora_disponible($fecha, $hora)) {
			wp_send_json_error(['message' => 'Esta hora ya no está disponible. Selecciona otra.'], 409);
		}

		// Guardar en calendario_reservas_mentoria
		$cal_result = Avance_Calendario_Reservas_Mentoria_DB::insert($fecha, $hora);
		if (!$cal_result) {
			wp_send_json_error(['message' => 'Error al guardar fecha/hora. Intenta de nuevo.'], 500);
		}

		// Guardar datos en sesión para checkout
		WC()->session->set('mentoria_booking_data', [
			'first_name' => $first_name,
			'last_name' => $last_name,
			'email' => $email,
			'whatsapp' => $whatsapp,
			'desafio' => $desafio,
			'plan' => $plan,
			'fecha' => $fecha,
			'hora' => $hora,
		]);

		wp_send_json_success([
			'message' => 'Datos guardados. Redirigiendo a checkout...',
			'redirect' => '/checkout/'
		]);
	}

	private function parse_date($date_str) {
		$parts = explode('/', $date_str);
		if (count($parts) !== 3) {
			return false;
		}
		$day = intval($parts[0]);
		$month = intval($parts[1]);
		$year = intval($parts[2]);

		if (!checkdate($month, $day, $year)) {
			return false;
		}

		return sprintf('%04d-%02d-%02d', $year, $month, $day);
	}
}

new Avance_Handler_Mentoria_Booking();
