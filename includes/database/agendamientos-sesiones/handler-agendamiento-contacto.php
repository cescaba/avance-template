<?php
/**
 * AJAX Handler - Agendamiento Contacto
 *
 * Valida SIN email (solo nombre, whatsapp, tema, fecha, hora)
 * Seguridad: nonce, rate limit, spam detection
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

require_once get_template_directory() . '/includes/database/agendamientos-sesiones/class-agendamiento-contacto-db.php';
require_once get_template_directory() . '/includes/database/calendario/class-calendario-reservas-db.php';

class Avance_Handler_Agendamiento_Contacto {

	public function __construct() {
		add_action('wp_ajax_nopriv_avance_submit_agendamiento', [$this, 'handle_request']);
		add_action('wp_ajax_avance_submit_agendamiento', [$this, 'handle_request']);
		add_action('wp_ajax_nopriv_avance_get_available_hours', [$this, 'get_available_hours']);
		add_action('wp_ajax_avance_get_available_hours', [$this, 'get_available_hours']);
	}

	public function handle_request() {
		// Obtener datos del POST
		$post_data = array(
			'nombre' => $_POST['nombre'] ?? '',
			'whatsapp' => $_POST['whatsapp'] ?? '',
			'tema' => $_POST['tema'] ?? '',
			'fecha' => $_POST['fecha'] ?? '',
			'hora' => $_POST['hora'] ?? '',
			'nonce' => $_POST['nonce'] ?? '',
		);

		// Validar nonce CSRF
		if (!isset($post_data['nonce']) || !wp_verify_nonce($post_data['nonce'], 'form_agendamiento')) {
			wp_send_json_error(['message' => 'Error de seguridad: token inválido.'], 400);
		}

		// Validar nombre
		$nombre = trim($post_data['nombre'] ?? '');
		if (empty($nombre)) {
			wp_send_json_error(['message' => 'El nombre es requerido.'], 400);
		}
		if (strlen($nombre) < 3) {
			wp_send_json_error(['message' => 'El nombre debe tener mínimo 3 caracteres.'], 400);
		}
		if (strlen($nombre) > 100) {
			wp_send_json_error(['message' => 'El nombre no puede exceder 100 caracteres.'], 400);
		}

		// Validar whatsapp
		$whatsapp = trim($post_data['whatsapp'] ?? '');
		if (empty($whatsapp)) {
			wp_send_json_error(['message' => 'El WhatsApp es requerido.'], 400);
		}
		$whatsapp_clean = preg_replace('/[^0-9+]/', '', $whatsapp);
		$digits_only = preg_replace('/[^0-9]/', '', $whatsapp_clean);
		if (strlen($digits_only) < 9 || strlen($digits_only) > 15) {
			wp_send_json_error(['message' => 'WhatsApp debe tener entre 9 y 15 dígitos.'], 400);
		}

		// Validar tema
		$tema = trim($post_data['tema'] ?? '');
		if (empty($tema)) {
			wp_send_json_error(['message' => 'El tema es requerido.'], 400);
		}
		if (strlen($tema) < 3) {
			wp_send_json_error(['message' => 'El tema debe tener mínimo 3 caracteres.'], 400);
		}

		// Validar fecha
		$fecha = trim($post_data['fecha'] ?? '');
		if (empty($fecha)) {
			wp_send_json_error(['message' => 'La fecha es requerida.'], 400);
		}
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
			wp_send_json_error(['message' => 'Formato de fecha inválido.'], 400);
		}

		// Validar hora
		$hora = trim($post_data['hora'] ?? '');
		if (empty($hora)) {
			wp_send_json_error(['message' => 'La hora es requerida.'], 400);
		}

		// Validar duplicados por WhatsApp (máximo 1 agendamiento activo por número)
		if (Avance_Agendamiento_Contacto_DB::check_duplicate_whatsapp_today($whatsapp) > 0) {
			wp_send_json_error(['message' => 'Este número de WhatsApp ya tiene un agendamiento registrado hoy. Intenta mañana.'], 409);
		}

		// Rate limiting (5 por hora por IP)
		$ip = $this->get_client_ip();
		if (!$this->check_rate_limit($ip)) {
			wp_send_json_error(['message' => 'Demasiados intentos. Espera un momento.'], 429);
		}

		// Detectar spam en tema
		if (class_exists('Avance_Spam_Detector')) {
			$spam_result = Avance_Spam_Detector::analyze_message($tema, $nombre, '');
			if ($spam_result['is_spam']) {
				wp_send_json_error(['message' => 'Tu mensaje fue identificado como spam.'], 400);
			}
		}

		// Verificar disponibilidad en calendario
		if (!Avance_Calendario_Reservas_DB::is_hora_disponible($fecha, $hora)) {
			wp_send_json_error(['message' => 'Esta hora ya no está disponible. Selecciona otra.'], 409);
		}

		// Guardar en calendario_reservas
		$cal_result = Avance_Calendario_Reservas_DB::insert($fecha, $hora);
		if (!$cal_result) {
			wp_send_json_error(['message' => 'Error al guardar fecha/hora. Intenta de nuevo.'], 500);
		}
		$calendario_reserva_id = $GLOBALS['wpdb']->insert_id;

		// Preparar datos para agendamiento_contacto
		$data_for_db = array(
			'nombre' => $nombre,
			'whatsapp' => $whatsapp,
			'tema' => $tema,
			'calendario_reserva_id' => $calendario_reserva_id,
			'estado' => 'pendiente'
		);

		// Guardar en agendamiento_contacto
		$result = Avance_Agendamiento_Contacto_DB::insert($data_for_db);
		if (!$result) {
			wp_send_json_error(['message' => 'Error al guardar. Intenta de nuevo.'], 500);
		}

		// Marcar como enviado y obtener ID
		$inserted_id = $GLOBALS['wpdb']->insert_id;
		Avance_Agendamiento_Contacto_DB::mark_wsp_sent($inserted_id);

		// Respuesta exitosa
		wp_send_json_success([
			'message' => 'Mensaje guardado correctamente. Abriendo WhatsApp...',
			'id' => $inserted_id
		]);
	}

	private function get_client_ip() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
		}
		return sanitize_text_field($ip);
	}


	private function check_rate_limit($ip) {
		global $wpdb;
		$table = $wpdb->prefix . 'avance_form_attempts';

		$attempts = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM $table WHERE ip_address = %s AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
			$ip
		));

		if ($attempts >= 5) {
			return false;
		}

		$wpdb->insert($table, [
			'ip_address' => $ip,
			'created_at' => current_time('mysql'),
			'last_attempt' => current_time('mysql'),
		], ['%s', '%s', '%s']);

		return true;
	}

	public function get_available_hours() {
		$fecha = $_GET['fecha'] ?? '';

		if (empty($fecha) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
			wp_send_json_error(['message' => 'Fecha inválida'], 400);
		}

		$booked_hours = Avance_Calendario_Reservas_DB::get_booked_hours($fecha);

		wp_send_json_success([
			'fecha' => $fecha,
			'booked_hours' => $booked_hours
		]);
	}
}

new Avance_Handler_Agendamiento_Contacto();
