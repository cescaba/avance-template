<?php
/**
 * Servicio Empresa Database - Tabla para propuestas de servicios
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Servicio_Empresa_DB {
	private static $table_name = null;
	private static $charset_collate;

	public static function init() {
		global $wpdb;
		self::$table_name = $wpdb->prefix . 'avance_servicios_empresas';
		self::$charset_collate = $wpdb->get_charset_collate();
	}

	public static function table_name() {
		if (!self::$table_name) {
			self::init();
		}
		return self::$table_name;
	}

	public static function create_table() {
		global $wpdb;
		self::init();

		if ($wpdb->get_var("SHOW TABLES LIKE '" . self::$table_name . "'") === self::$table_name) {
			return;
		}

		$sql = "CREATE TABLE " . self::$table_name . " (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			nombre VARCHAR(100) NOT NULL,
			cargo VARCHAR(100),
			empresa VARCHAR(150) NOT NULL,
			tamaño_equipo VARCHAR(50),
			email VARCHAR(100) NOT NULL UNIQUE,
			whatsapp VARCHAR(20),
			servicio_interes VARCHAR(150) NOT NULL,
			desafio_comercial TEXT NOT NULL,
			estado VARCHAR(20) DEFAULT 'pendiente' COMMENT 'pendiente, contactado, propuesta_enviada, cerrado',
			fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY uk_email_empresa (email, empresa),
			KEY idx_empresa (empresa),
			KEY idx_estado (estado),
			KEY idx_fecha (fecha_creacion),
			KEY idx_email_estado (email, estado)
		) " . self::$charset_collate . ";";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
	}

	public static function insert($data) {
		global $wpdb;
		$table = self::table_name();

		$insert_data = [
			'nombre'              => sanitize_text_field($data['nombre']),
			'cargo'               => sanitize_text_field($data['cargo'] ?? ''),
			'empresa'             => sanitize_text_field($data['empresa']),
			'tamaño_equipo'       => sanitize_text_field($data['tamaño_equipo'] ?? ''),
			'email'               => sanitize_email($data['email']),
			'whatsapp'            => sanitize_text_field($data['whatsapp'] ?? ''),
			'servicio_interes'    => sanitize_text_field($data['servicio_interes']),
			'desafio_comercial'   => sanitize_textarea_field($data['desafio_comercial']),
		];

		$result = $wpdb->insert($table, $insert_data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);

		if (!$result) {
			error_log('Avance_Servicio_Empresa_DB::insert failed - Error: ' . $wpdb->last_error);
			error_log('Insert data - Email: ' . ($insert_data['email'] ?? 'N/A') . ', Empresa: ' . ($insert_data['empresa'] ?? 'N/A'));
		}

		return $result ? $wpdb->insert_id : false;
	}

	public static function update($id, $data) {
		global $wpdb;
		$table = self::table_name();

		return $wpdb->update(
			$table,
			['estado' => sanitize_text_field($data['estado'] ?? 'pendiente')],
			['id' => intval($id)],
			['%s'],
			['%d']
		);
	}

	public static function get_by_id($id) {
		global $wpdb;
		$table = self::table_name();

		return $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table WHERE id = %d",
			intval($id)
		));
	}

	public static function get_all($limit = 50, $offset = 0) {
		global $wpdb;
		$table = self::table_name();

		return $wpdb->get_results($wpdb->prepare(
			"SELECT * FROM $table ORDER BY fecha_creacion DESC LIMIT %d OFFSET %d",
			intval($limit),
			intval($offset)
		));
	}

	public static function delete($id) {
		global $wpdb;
		$table = self::table_name();

		return $wpdb->delete($table, ['id' => intval($id)], ['%d']);
	}
}
