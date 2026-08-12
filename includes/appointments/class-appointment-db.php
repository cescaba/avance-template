<?php
/**
 * Appointment Database Handler
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Appointment_DB {

	const TABLE_NAME = 'avance_appointments';

	/**
	 * Crear tabla en la BD
	 */
	public static function create_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			nombre VARCHAR(255) NOT NULL,
			whatsapp VARCHAR(20) NOT NULL,
			servicio VARCHAR(255) NOT NULL,
			fecha DATE NOT NULL,
			hora TIME NOT NULL,
			notas LONGTEXT,
			status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			ip_address VARCHAR(45),
			PRIMARY KEY (id),
			INDEX status_idx (status),
			INDEX fecha_idx (fecha),
			INDEX created_idx (created_at)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
	}

	/**
	 * Guardar cita
	 */
	public static function save_appointment($data) {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$insert_data = array(
			'nombre' => $data['nombre'],
			'whatsapp' => $data['whatsapp'],
			'servicio' => $data['servicio'],
			'fecha' => $data['fecha'],
			'hora' => $data['hora'],
			'notas' => $data['notas'] ?? '',
			'status' => 'pendiente',
			'ip_address' => self::get_client_ip(),
		);

		$insert_format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');

		$result = $wpdb->insert($table_name, $insert_data, $insert_format);

		if ($result === false) {
			error_log('Error guardando cita: ' . $wpdb->last_error);
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Actualizar estado de la cita
	 */
	public static function update_status($appointment_id, $status) {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$result = $wpdb->update(
			$table_name,
			array('status' => $status),
			array('id' => $appointment_id),
			array('%s'),
			array('%d')
		);

		return $result;
	}

	/**
	 * Obtener cita por ID
	 */
	public static function get_appointment($appointment_id) {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table_name} WHERE id = %d",
				$appointment_id
			)
		);
	}

	/**
	 * Obtener IP del cliente
	 */
	private static function get_client_ip() {
		$ip = '';

		if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
		} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return sanitize_text_field($ip);
	}
}
