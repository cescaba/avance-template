<?php
/**
 * Contact Form Database Handler
 *
 * Manejo de tabla custom para contactos
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Contact_DB {

	const TABLE_NAME = 'avance_contacts';

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
			email VARCHAR(255) NOT NULL,
			numero VARCHAR(20) NOT NULL,
			asunto VARCHAR(255) NOT NULL,
			mensaje LONGTEXT,
			status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
			validation_reason VARCHAR(100),
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			ip_address VARCHAR(45),
			user_agent TEXT,
			PRIMARY KEY (id),
			INDEX status_idx (status),
			INDEX created_idx (created_at),
			INDEX email_idx (email)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
	}

	/**
	 * Guardar contacto en la BD
	 *
	 * @param array $data Datos del contacto
	 * @return int|false ID del contacto o false si falló
	 */
	public static function save_contact($data) {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$insert_data = array(
			'nombre' => $data['nombre'],
			'email' => $data['email'],
			'numero' => $data['numero'],
			'asunto' => $data['asunto'],
			'mensaje' => $data['mensaje'] ?? '',
			'status' => 'pendiente',
			'ip_address' => self::get_client_ip(),
			'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
		);

		$insert_format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');

		$result = $wpdb->insert($table_name, $insert_data, $insert_format);

		if ($result === false) {
			error_log('Error guardando contacto: ' . $wpdb->last_error);
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Actualizar estado del contacto
	 *
	 * @param int $contact_id ID del contacto
	 * @param string $status Estado (pendiente, enviado, bloqueado, spam)
	 * @param string $reason Razón del cambio (opcional)
	 */
	public static function update_status($contact_id, $status, $reason = '') {
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
			array('id' => $contact_id),
			array('%s', '%s'),
			array('%d')
		);

		if ($result === false) {
			error_log('Error updating contact status: ' . $wpdb->last_error);
		}

		return $result;
	}

	/**
	 * Obtener contacto por ID
	 */
	public static function get_contact($contact_id) {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table_name} WHERE id = %d",
				$contact_id
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
