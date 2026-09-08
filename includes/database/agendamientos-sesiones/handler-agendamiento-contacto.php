<?php
/**
 * AJAX Handler - Agendamiento Contacto
 * Procesa agendamientos: nombre, whatsapp, tema, fecha, hora
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Handler_Agendamiento_Contacto {

	public function __construct() {
		add_action('wp_ajax_nopriv_avance_submit_agendamiento', [$this, 'handle_request']);
		add_action('wp_ajax_avance_submit_agendamiento', [$this, 'handle_request']);
	}

	public function handle_request() {
		$post_data = array(
			'nombre' => $_POST['nombre'] ?? '',
			'whatsapp' => $_POST['whatsapp'] ?? '',
			'tema' => $_POST['tema'] ?? '',
			'fecha' => $_POST['fecha'] ?? '',
			'hora' => $_POST['hora'] ?? '',
			'nonce' => $_POST['nonce'] ?? '',
		);

		if (!isset($post_data['nonce']) || !wp_verify_nonce($post_data['nonce'], 'form_agendamiento')) {
			wp_send_json_error(['message' => 'Error de seguridad: token inválido.'], 400);
		}

		$nombre = trim($post_data['nombre'] ?? '');
		if (empty($nombre) || strlen($nombre) < 3) {
			wp_send_json_error(['message' => 'El nombre debe tener mínimo 3 caracteres.'], 400);
		}

		$whatsapp = trim($post_data['whatsapp'] ?? '');
		if (empty($whatsapp)) {
			wp_send_json_error(['message' => 'El WhatsApp es requerido.'], 400);
		}
		$digits_only = preg_replace('/[^0-9]/', '', $whatsapp);
		if (strlen($digits_only) < 9 || strlen($digits_only) > 15) {
			wp_send_json_error(['message' => 'WhatsApp inválido.'], 400);
		}

		$tema = trim($post_data['tema'] ?? '');
		if (empty($tema) || strlen($tema) < 3) {
			wp_send_json_error(['message' => 'El tema es requerido.'], 400);
		}

		$fecha = trim($post_data['fecha'] ?? '');
		if (empty($fecha) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
			wp_send_json_error(['message' => 'Fecha inválida.'], 400);
		}

		$hora = trim($post_data['hora'] ?? '');
		if (empty($hora) || !preg_match('/^\d{2}:\d{2}/', $hora)) {
			wp_send_json_error(['message' => 'Hora inválida.'], 400);
		}

		$ip = $this->get_client_ip();
		if (!$this->check_rate_limit($ip)) {
			wp_send_json_error(['message' => 'Demasiados intentos.'], 429);
		}

		if ($this->is_whatsapp_duplicate_today($whatsapp)) {
			wp_send_json_error(['message' => 'Este número de WhatsApp ya tiene un agendamiento. Intenta mañana.'], 409);
		}

		if (class_exists('Avance_Spam_Detector')) {
			$spam_result = Avance_Spam_Detector::analyze_message($tema, $nombre, '');
			if ($spam_result['is_spam']) {
				wp_send_json_error(['message' => 'Mensaje identificado como spam.'], 400);
			}
		}

		if ($this->is_hora_ocupada($fecha, $hora)) {
			wp_send_json_error(['message' => 'Hora no disponible. Selecciona otra.'], 409);
		}

		$data_for_db = array(
			'nombre' => $nombre,
			'whatsapp' => $whatsapp,
			'tema' => $tema,
			'fecha' => $fecha,
			'hora' => $hora,
			'estado' => 'pendiente'
		);

		$result = Avance_Agendamiento_Contacto_DB::insert($data_for_db);
		if (!$result) {
			wp_send_json_error(['message' => 'Error al guardar agendamiento.'], 500);
			return;
		}

		$inserted_id = $GLOBALS['wpdb']->insert_id;

		wp_send_json_success([
			'message' => 'Agendamiento guardado correctamente.',
			'id' => $inserted_id
		]);
	}

	private function is_hora_ocupada($fecha, $hora) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'avance_agendamiento_contacto';

		$hora_normalized = substr($hora, 0, 5);

		$count = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM $table_name
			 WHERE fecha = %s AND SUBSTR(hora, 1, 5) = %s AND estado = 'pendiente'",
			$fecha,
			$hora_normalized
		));

		return $count > 0;
	}

	private function is_whatsapp_duplicate_today($whatsapp) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'avance_agendamiento_contacto';

		$count = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM $table_name
			 WHERE whatsapp = %s AND DATE(fecha_creacion) = CURDATE() AND estado = 'pendiente'",
			$whatsapp
		));

		return $count > 0;
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

		try {
			$table_exists = $wpdb->get_var($wpdb->prepare(
				"SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s",
				DB_NAME,
				'avance_form_attempts'
			));

			if (!$table_exists) {
				return true;
			}

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
		} catch (Exception $e) {
			return true;
		}
	}
}

new Avance_Handler_Agendamiento_Contacto();
