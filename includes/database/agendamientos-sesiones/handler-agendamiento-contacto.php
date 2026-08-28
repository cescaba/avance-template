<?php
/**
 * AJAX Handler - Agendamiento Contacto
 *
 * Procesa el formulario de agendamiento desde la sección contacto
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

require_once get_template_directory() . '/includes/database/agendamientos-sesiones/class-agendamiento-contacto-db.php';

class Avance_Handler_Agendamiento_Contacto {

	public function __construct() {
		add_action('wp_ajax_nopriv_avance_agendamiento_contacto', [$this, 'handle_request']);
		add_action('wp_ajax_avance_agendamiento_contacto', [$this, 'handle_request']);
	}

	public function handle_request() {
		// Verificar nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'avance_agendamiento_contacto')) {
			wp_send_json_error(['mensaje' => 'Verifica tu identidad e intenta de nuevo.']);
		}

		// Obtener y sanitizar datos
		$nombre = sanitize_text_field($_POST['nombre'] ?? '');
		$whatsapp = sanitize_text_field($_POST['whatsapp'] ?? '');
		$tema = sanitize_text_field($_POST['tema'] ?? '');
		$fecha = sanitize_text_field($_POST['fecha'] ?? '');
		$hora = sanitize_text_field($_POST['hora'] ?? '');

		// Validar campos requeridos
		if (empty($nombre) || empty($whatsapp) || empty($tema) || empty($fecha) || empty($hora)) {
			wp_send_json_error(['mensaje' => 'Completa todos los campos requeridos.']);
		}

		// Validar nombre (mínimo 3 caracteres)
		if (strlen($nombre) < 3) {
			wp_send_json_error(['mensaje' => 'El nombre debe tener al menos 3 caracteres.']);
		}

		// Validar número de WhatsApp (formato básico)
		if (!$this->validate_whatsapp($whatsapp)) {
			wp_send_json_error(['mensaje' => 'Número de WhatsApp inválido.']);
		}

		// Prevenir spam: máximo 5 agendamientos por número en 24 horas
		$count_today = Avance_Agendamiento_Contacto_DB::get_by_whatsapp_today($whatsapp);
		if ($count_today >= 5) {
			wp_send_json_error(['mensaje' => 'Has alcanzado el límite de agendamientos hoy. Intenta mañana.']);
		}

		// Validar que no haya duplicado (mismo número y fecha)
		$duplicate = Avance_Agendamiento_Contacto_DB::check_duplicate($whatsapp, $fecha);
		if ($duplicate) {
			wp_send_json_error(['mensaje' => 'Ya tienes un agendamiento para esa fecha. Selecciona otra.']);
		}

		// Rate limiting por IP (máximo 3 intentos por minuto)
		if (!$this->check_rate_limit()) {
			wp_send_json_error(['mensaje' => 'Demasiados intentos. Espera un momento.']);
		}

		// Guardar en base de datos
		$result = Avance_Agendamiento_Contacto_DB::insert([
			'nombre'     => $nombre,
			'whatsapp'   => $whatsapp,
			'tema'       => $tema,
			'fecha'      => $fecha,
			'hora'       => $hora,
			'estado'     => 'pendiente'
		]);

		if (!$result) {
			wp_send_json_error(['mensaje' => 'Error al guardar. Intenta de nuevo.']);
		}

		// Enviar mensaje por WhatsApp
		$this->send_whatsapp_message($nombre, $whatsapp, $tema, $fecha, $hora);

		// Marcar como enviado
		$inserted_id = $GLOBALS['wpdb']->insert_id;
		Avance_Agendamiento_Contacto_DB::mark_wsp_sent($inserted_id);

		wp_send_json_success([
			'mensaje' => '¡Agendamiento confirmado! Te enviaremos un mensaje por WhatsApp.',
			'id' => $inserted_id
		]);
	}

	private function validate_whatsapp($whatsapp) {
		// Validar formato básico de WhatsApp (números y +)
		$whatsapp_clean = preg_replace('/[^0-9+]/', '', $whatsapp);

		// Debe ser al menos 7 dígitos
		$digits_only = preg_replace('/[^0-9]/', '', $whatsapp_clean);

		return strlen($digits_only) >= 7 && strlen($digits_only) <= 15;
	}

	private function check_rate_limit() {
		$ip = $this->get_client_ip();
		$transient_key = 'avance_agendamiento_limit_' . $ip;
		$attempts = get_transient($transient_key) ?? 0;

		if ($attempts >= 3) {
			return false;
		}

		set_transient($transient_key, $attempts + 1, 60); // 1 minuto
		return true;
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

	private function send_whatsapp_message($nombre, $whatsapp, $tema, $fecha, $hora) {
		// Preparar mensaje
		$mensaje = "¡Hola $nombre!%0A%0A";
		$mensaje .= "Tu sesión de diagnóstico ha sido confirmada.%0A%0A";
		$mensaje .= "*Detalles de tu cita:*%0A";
		$mensaje .= "📅 Fecha: $fecha%0A";
		$mensaje .= "🕐 Hora: $hora%0A";
		$mensaje .= "📋 Tema: $tema%0A%0A";
		$mensaje .= "Te enviaremos el enlace de Google Meet 15 minutos antes de la sesión.%0A%0A";
		$mensaje .= "¿Preguntas? Estamos disponibles.";

		// URL de WhatsApp
		$whatsapp_url = "https://api.whatsapp.com/send?phone=" . preg_replace('/[^0-9]/', '', $whatsapp) . "&text=" . $mensaje;

		// Log o enviar a través de API si está configurado
		// Por ahora es solo un registro
		do_action('avance_whatsapp_mensaje', [
			'numero' => $whatsapp,
			'mensaje' => $mensaje,
			'url' => $whatsapp_url
		]);

		return true;
	}
}

new Avance_Handler_Agendamiento_Contacto();
