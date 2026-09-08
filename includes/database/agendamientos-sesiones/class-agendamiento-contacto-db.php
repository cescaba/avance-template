<?php
/**
 * Agendamiento Contacto Database
 *
 * Tabla para guardar agendamientos de la sección contacto
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Agendamiento_Contacto_DB {

	private static $table_name = 'avance_agendamiento_contacto';

	public static function create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`nombre` varchar(255) NOT NULL,
			`whatsapp` varchar(20) NOT NULL,
			`tema` varchar(255) NOT NULL,
			`calendario_reserva_id` bigint(20) NOT NULL,
			`estado` varchar(50) DEFAULT 'pendiente',
			`mensaje_wsp_enviado` int(1) DEFAULT 0,
			`fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
			`fecha_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			UNIQUE KEY `unique_calendario_reserva` (`calendario_reserva_id`),
			KEY `idx_estado` (`estado`),
			KEY `idx_whatsapp` (`whatsapp`),
			KEY `idx_fecha_creacion` (`fecha_creacion`)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	public static function insert($data) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		return $wpdb->insert($table_name, $data, ['%s', '%s', '%s', '%d', '%s', '%d']);
	}

	public static function get_all($limit = 50, $offset = 0) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		return $wpdb->get_results($wpdb->prepare(
			"SELECT * FROM $table_name ORDER BY fecha_creacion DESC LIMIT %d OFFSET %d",
			$limit,
			$offset
		));
	}

	public static function get_by_id($id) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		return $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table_name WHERE id = %d",
			$id
		));
	}

	public static function update_status($id, $status) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		return $wpdb->update($table_name, ['estado' => $status], ['id' => $id], ['%s'], ['%d']);
	}

	public static function mark_wsp_sent($id) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		return $wpdb->update($table_name, ['mensaje_wsp_enviado' => 1], ['id' => $id], ['%d'], ['%d']);
	}

	public static function check_duplicate_whatsapp_today($whatsapp) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		return $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM $table_name WHERE whatsapp = %s AND DATE(fecha_creacion) = CURDATE()",
			$whatsapp
		));
	}

	public static function table_name() {
		global $wpdb;
		return $wpdb->prefix . self::$table_name;
	}

	public static function get_available_hours($fecha) {
		if (class_exists('Avance_Calendario_Reservas_DB')) {
			return Avance_Calendario_Reservas_DB::get_booked_hours($fecha, 'agendamiento');
		}
		return [];
	}
}
