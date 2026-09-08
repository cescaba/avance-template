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
		add_action('wp_ajax_nopriv_avance_get_booked_hours_batch', [$this, 'get_booked_hours_batch']);
		add_action('wp_ajax_avance_get_booked_hours_batch', [$this, 'get_booked_hours_batch']);
	}

	public function handle_request() {
		// Log de inicio para debugging
		error_log('Agendamiento POST recibido: ' . json_encode($_POST));

		// Asegurar que las tablas existan (fail-safe para producción)
		try {
			Avance_Calendario_Reservas_DB::create_table();
			Avance_Agendamiento_Contacto_DB::create_table();
		} catch (Exception $e) {
			error_log('Error creando tablas en handle_request: ' . $e->getMessage());
			wp_send_json_error(['message' => 'Error de configuración del servidor. Contacta al administrador.'], 500);
			return;
		}

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

		// 1. Guardar en calendario_reservas
		$cal_result = Avance_Calendario_Reservas_DB::insert($fecha, $hora);
		if (!$cal_result) {
			error_log('Error inserting calendario_reservas: ' . $GLOBALS['wpdb']->last_error);
			wp_send_json_error(['message' => 'Error al guardar fecha/hora. Intenta de nuevo.'], 500);
			return;
		}
		$calendario_reserva_id = $GLOBALS['wpdb']->insert_id;
		error_log('Calendario reserva insertada: ID ' . $calendario_reserva_id);

		// 2. Guardar en agendamiento_contacto
		$data_for_db = array(
			'nombre' => $nombre,
			'whatsapp' => $whatsapp,
			'tema' => $tema,
			'calendario_reserva_id' => $calendario_reserva_id,
			'estado' => 'pendiente'
		);

		error_log('Intentando insertar agendamiento: ' . json_encode($data_for_db));

		$result = Avance_Agendamiento_Contacto_DB::insert($data_for_db);
		if (!$result) {
			error_log('Error inserting agendamiento_contacto: ' . $GLOBALS['wpdb']->last_error);
			// Si falla agendamiento, borrar la hora que se guardó
			Avance_Calendario_Reservas_DB::delete_reserva($fecha, $hora);
			wp_send_json_error(['message' => 'Error al guardar. Intenta de nuevo.'], 500);
			return;
		}

		$inserted_id = $GLOBALS['wpdb']->insert_id;
		error_log('Agendamiento insertado: ID ' . $inserted_id);

		try {
			Avance_Agendamiento_Contacto_DB::mark_wsp_sent($inserted_id);
		} catch (Exception $e) {
			error_log('Error marking wsp sent: ' . $e->getMessage());
		}

		// Invalidar caché de horas ocupadas (nueva reserva agregada)
		try {
			Avance_Calendario_Reservas_DB::invalidate_cache($fecha);
		} catch (Exception $e) {
			error_log('Error invalidating cache: ' . $e->getMessage());
		}

		// Respuesta exitosa
		wp_send_json_success([
			'message' => 'Mensaje guardado correctamente. Abriendo WhatsApp...',
			'id' => $inserted_id,
			'invalidate_cache' => true
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

		try {
			// Verificar si la tabla existe
			$table_exists = $wpdb->get_var($wpdb->prepare(
				"SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s",
				DB_NAME,
				'avance_form_attempts'
			));

			if (!$table_exists) {
				// Tabla no existe, permitir sin rate limiting
				error_log('Tabla avance_form_attempts no existe, rate limiting deshabilitado');
				return true;
			}

			$attempts = $wpdb->get_var($wpdb->prepare(
				"SELECT COUNT(*) FROM $table WHERE ip_address = %s AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
				$ip
			));

			if ($attempts >= 5) {
				error_log('Rate limit excedido para IP: ' . $ip . ' - Intentos: ' . $attempts);
				return false;
			}

			$wpdb->insert($table, [
				'ip_address' => $ip,
				'created_at' => current_time('mysql'),
				'last_attempt' => current_time('mysql'),
			], ['%s', '%s', '%s']);

			return true;
		} catch (Exception $e) {
			// Si hay error en rate limiting, permitir (no bloquear por error en tabla)
			error_log('Error en check_rate_limit: ' . $e->getMessage());
			return true;
		}
	}

	public function get_available_hours() {
		$fecha = isset($_GET['fecha']) ? sanitize_text_field($_GET['fecha']) : '';

		if (empty($fecha) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
			wp_send_json_error(['message' => 'Fecha inválida'], 400);
			return;
		}

		// Validar fecha no sea pasada
		$fecha_timestamp = strtotime($fecha);
		$today_timestamp = strtotime(current_time('Y-m-d'));

		if ($fecha_timestamp < $today_timestamp) {
			wp_send_json_error(['message' => 'Fecha pasada'], 400);
			return;
		}

		$booked_hours = Avance_Calendario_Reservas_DB::get_booked_hours($fecha);

		// Si hay error en BD, retornar error explícito
		if ($booked_hours === null) {
			wp_send_json_error(['message' => 'Error al cargar disponibilidad'], 500);
			return;
		}

		// Respuesta con caché info (para debugging)
		wp_send_json_success([
			'fecha' => $fecha,
			'booked_hours' => $booked_hours,
			'cached' => true,
			'timestamp' => current_time('timestamp')
		]);
	}

	public function get_booked_hours_batch() {
		// Batch prefetch: obtener horas de múltiples fechas en una sola query
		$fechas_param = isset($_GET['fechas']) ? sanitize_text_field($_GET['fechas']) : '';

		if (empty($fechas_param)) {
			wp_send_json_error(['message' => 'Fechas requeridas'], 400);
			return;
		}

		// Parsear fechas (ej: "2025-09-01,2025-09-02,2025-09-03")
		$fechas = array_filter(array_map('trim', explode(',', $fechas_param)));

		if (empty($fechas) || count($fechas) > 60) {
			wp_send_json_error(['message' => 'Cantidad de fechas inválida'], 400);
			return;
		}

		// Validar formato de fechas
		foreach ($fechas as $fecha) {
			if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
				wp_send_json_error(['message' => 'Formato de fecha inválido'], 400);
				return;
			}
		}

		// Obtener horas de múltiples fechas (con caché)
		$booked_hours_by_fecha = Avance_Calendario_Reservas_DB::get_booked_hours_batch($fechas);

		wp_send_json_success([
			'booked_hours' => $booked_hours_by_fecha,
			'count' => count($booked_hours_by_fecha),
			'timestamp' => current_time('timestamp')
		]);
	}
}

new Avance_Handler_Agendamiento_Contacto();
