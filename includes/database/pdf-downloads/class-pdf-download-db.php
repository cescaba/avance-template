<?php
/**
 * PDF Download Database Handler
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_PDF_Download_DB {

	const TABLE_NAME = 'avance_pdf_downloads';

	public static function table_exists() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;
		$result = $wpdb->get_var($wpdb->prepare(
			"SELECT 1 FROM information_schema.tables WHERE table_schema = %s AND table_name = %s LIMIT 1",
			DB_NAME,
			$table_name
		));
		return !empty($result);
	}

	public static function ensure_table_exists() {
		if (!self::table_exists()) {
			self::create_table();
		}
	}

	public static function create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			email VARCHAR(255) NOT NULL,
			telefono VARCHAR(20),
			nombre VARCHAR(255) NOT NULL,
			empresa VARCHAR(255) NOT NULL,
			empleados VARCHAR(50) NOT NULL,
			industria VARCHAR(100) NOT NULL,
			ip_address VARCHAR(45),
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			UNIQUE KEY unique_email (email),
			UNIQUE KEY unique_phone (telefono),
			KEY email_idx (email),
			KEY phone_idx (telefono),
			KEY created_idx (created_at)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
	}

	public static function save_download($data) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$result = $wpdb->insert(
			$table_name,
			array(
				'email' => $data['email'],
				'telefono' => $data['telefono'] ?? '',
				'nombre' => $data['nombre'],
				'empresa' => $data['empresa'],
				'empleados' => $data['empleados'],
				'industria' => $data['industria'],
				'ip_address' => $data['ip_address'] ?? '',
			),
			array('%s', '%s', '%s', '%s', '%s', '%s', '%s')
		);

		if (!$result) {
			error_log('PDF Download DB Error: ' . $wpdb->last_error);
			return false;
		}

		return $wpdb->insert_id;
	}

	public static function email_exists($email) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$result = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM $table_name WHERE email = %s LIMIT 1",
				$email
			)
		);

		return !empty($result);
	}

	public static function phone_exists($phone) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		if (empty($phone)) {
			return false;
		}

		$result = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM $table_name WHERE telefono = %s LIMIT 1",
				$phone
			)
		);

		return !empty($result);
	}
}
