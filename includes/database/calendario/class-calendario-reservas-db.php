<?php
/**
 * Calendario Reservas Database
 *
 * Tabla para disponibilidad de agendamiento (contacto)
 * Sin tipo_reserva: dos tablas separadas (una para agendamiento, otra para mentoría)
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Calendario_Reservas_DB {

	private static $table_name = 'avance_calendario_reservas';

	public static function create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`fecha` date NOT NULL,
			`hora` time NOT NULL,
			`estado` varchar(50) DEFAULT 'ocupado',
			`fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			UNIQUE KEY `unique_fecha_hora` (`fecha`, `hora`),
			KEY `idx_fecha` (`fecha`),
			KEY `idx_estado` (`estado`)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	public static function insert($fecha, $hora) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		return $wpdb->insert($table_name, [
			'fecha' => $fecha,
			'hora' => $hora,
			'estado' => 'ocupado',
			'fecha_creacion' => current_time('mysql')
		], ['%s', '%s', '%s', '%s']);
	}

	public static function get_booked_hours($fecha) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		return $wpdb->get_col($wpdb->prepare(
			"SELECT LEFT(hora, 5) FROM $table_name
			 WHERE fecha = %s AND estado = 'ocupado'
			 ORDER BY hora",
			$fecha
		));
	}

	public static function is_hora_disponible($fecha, $hora) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		$count = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM $table_name
			 WHERE fecha = %s AND hora = %s AND estado = 'ocupado'",
			$fecha,
			$hora
		));

		return $count === 0 || $count === '0';
	}

	public static function delete_reserva($fecha, $hora) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		return $wpdb->delete($table_name, [
			'fecha' => $fecha,
			'hora' => $hora
		], ['%s', '%s']);
	}

	public static function table_name() {
		global $wpdb;
		return $wpdb->prefix . self::$table_name;
	}
}
