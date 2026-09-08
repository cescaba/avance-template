<?php
/**
 * AJAX Handler - Disponibilidad Mentoría
 *
 * Devuelve horas disponibles para mentoría desde tabla separada
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

require_once get_template_directory() . '/includes/database/calendario/class-calendario-reservas-mentoria-db.php';

class Avance_Handler_Mentoria {

	public function __construct() {
		add_action('wp_ajax_nopriv_avance_get_mentoria_hours', [$this, 'get_available_hours']);
		add_action('wp_ajax_avance_get_mentoria_hours', [$this, 'get_available_hours']);
	}

	public function get_available_hours() {
		$fecha = $_GET['fecha'] ?? '';

		if (empty($fecha) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
			wp_send_json_error(['message' => 'Fecha inválida'], 400);
		}

		$booked_hours = Avance_Calendario_Reservas_Mentoria_DB::get_booked_hours($fecha);

		wp_send_json_success([
			'fecha' => $fecha,
			'booked_hours' => $booked_hours
		]);
	}
}

new Avance_Handler_Mentoria();
