<?php
/**
 * Libro Database Class
 *
 * Tabla para guardar datos de compra del libro
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Libro_DB {

	private static $table_name = 'avance_libro_compras';

	public static function create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`nombre` varchar(100) NOT NULL,
			`email` varchar(100) NOT NULL,
			`whatsapp` varchar(20),
			`producto_woo` int(11),
			`estado` varchar(50) DEFAULT 'pendiente',
			`fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			KEY `idx_email` (`email`),
			KEY `idx_estado` (`estado`)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	public static function insert($nombre, $email, $whatsapp = '', $producto_woo = null) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		return $wpdb->insert($table_name, [
			'nombre' => $nombre,
			'email' => $email,
			'whatsapp' => $whatsapp,
			'producto_woo' => $producto_woo,
			'estado' => 'pendiente',
			'fecha_creacion' => current_time('mysql')
		], ['%s', '%s', '%s', '%d', '%s', '%s']);
	}

	public static function table_name() {
		global $wpdb;
		return $wpdb->prefix . self::$table_name;
	}
}
