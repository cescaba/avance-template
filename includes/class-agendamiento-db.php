<?php
/**
 * Agendamiento Database Handler
 *
 * Manejo de tabla custom para agendamientos
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Agendamiento_DB {

	const TABLE_NAME = 'avance_agendamientos';

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
			numero VARCHAR(20) NOT NULL,
			tema VARCHAR(255) NOT NULL,
			fecha_agendada DATE NOT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
			validation_reason VARCHAR(100),
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			ip_address VARCHAR(45),
			user_agent TEXT,
			PRIMARY KEY (id),
			INDEX status_idx (status),
			INDEX created_idx (created_at),
			INDEX fecha_idx (fecha_agendada)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
	}

	/**
	 * Guardar agendamiento en la BD
	 *
	 * @param array $data Datos del agendamiento
	 * @return int|false ID del agendamiento o false si falló
	 */
	public static function save_agendamiento($data) {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$insert_data = array(
			'nombre' => $data['nombre'],
			'numero' => $data['numero'],
			'tema' => $data['tema'],
			'fecha_agendada' => $data['fecha_agendada'],
			'status' => 'pendiente',
			'ip_address' => self::get_client_ip(),
			'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
		);

		$insert_format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s');

		$result = $wpdb->insert($table_name, $insert_data, $insert_format);

		if ($result === false) {
			error_log('Error guardando agendamiento: ' . $wpdb->last_error);
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Actualizar estado del agendamiento
	 *
	 * @param int $agendamiento_id ID del agendamiento
	 * @param string $status Estado (pendiente, confirmado, cancelado)
	 * @param string $reason Razón del cambio (opcional)
	 */
	public static function update_status($agendamiento_id, $status, $reason = '') {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$update_data = array(
			'status' => $status,
		);

		if (!empty($reason)) {
			$update_data['validation_reason'] = $reason;
		}

		$result = $wpdb->update(
			$table_name,
			$update_data,
			array('id' => $agendamiento_id),
			array('%s', '%s'),
			array('%d')
		);

		if ($result === false) {
			error_log('Error updating agendamiento status: ' . $wpdb->last_error);
		}

		return $result;
	}

	/**
	 * Obtener agendamiento por ID
	 */
	public static function get_agendamiento($agendamiento_id) {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table_name} WHERE id = %d",
				$agendamiento_id
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
