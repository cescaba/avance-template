<?php
/**
 * Proposal Database Handler
 *
 * Manejo de tabla custom para solicitudes de propuestas
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Proposal_DB {

	const TABLE_NAME = 'avance_proposals';

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
			cargo VARCHAR(255),
			empresa VARCHAR(255) NOT NULL,
			tamaño_equipo VARCHAR(100),
			email VARCHAR(255) NOT NULL,
			whatsapp VARCHAR(20),
			servicio_interes VARCHAR(255) NOT NULL,
			desafio_comercial LONGTEXT,
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
	 * Guardar propuesta en la BD
	 *
	 * @param array $data Datos de la propuesta
	 * @return int|false ID de la propuesta o false si falló
	 */
	public static function save_proposal($data) {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$insert_data = array(
			'nombre' => $data['nombre'],
			'cargo' => $data['cargo'] ?? '',
			'empresa' => $data['empresa'],
			'tamaño_equipo' => $data['tamaño_equipo'] ?? '',
			'email' => $data['email'],
			'whatsapp' => $data['whatsapp'] ?? '',
			'servicio_interes' => $data['servicio_interes'],
			'desafio_comercial' => $data['desafio_comercial'] ?? '',
			'status' => 'pendiente',
			'ip_address' => self::get_client_ip(),
			'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
		);

		$insert_format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');

		$result = $wpdb->insert($table_name, $insert_data, $insert_format);

		if ($result === false) {
			error_log('Error guardando propuesta: ' . $wpdb->last_error);
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Actualizar status de propuesta
	 *
	 * @param int $proposal_id ID de la propuesta
	 * @param string $status Nuevo status
	 * @return bool
	 */
	public static function update_status($proposal_id, $status) {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$result = $wpdb->update(
			$table_name,
			array('status' => $status),
			array('id' => $proposal_id),
			array('%s'),
			array('%d')
		);

		return $result !== false;
	}

	/**
	 * Obtener propuesta por ID
	 *
	 * @param int $proposal_id ID de la propuesta
	 * @return object|null
	 */
	public static function get_proposal($proposal_id) {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		return $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE id = %d",
			$proposal_id
		));
	}

	/**
	 * Obtener propuestas por status
	 *
	 * @param string $status Status a filtrar
	 * @param int $limit Límite de resultados
	 * @return array
	 */
	public static function get_by_status($status, $limit = 50) {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		return $wpdb->get_results($wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE status = %s ORDER BY created_at DESC LIMIT %d",
			$status,
			$limit
		));
	}

	/**
	 * Obtener IP del cliente
	 *
	 * @return string
	 */
	private static function get_client_ip() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'] ?? '';
		}
		return sanitize_text_field($ip);
	}
}
