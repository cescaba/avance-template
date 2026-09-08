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
			KEY `idx_fecha_estado` (`fecha`, `estado`),
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

		// Caché con 3 niveles: Transient (30 min) > wp_cache (memoria) > BD
		$cache_key = 'avance_booked_hours_' . sanitize_key($fecha);

		// L1: Transient (persiste 30 minutos, sobrevive recargas)
		$cached = get_transient($cache_key);
		if ($cached !== false) {
			return $cached;
		}

		$result = $wpdb->get_col($wpdb->prepare(
			"SELECT LEFT(hora, 5) FROM $table_name
			 WHERE fecha = %s AND estado = 'ocupado'
			 ORDER BY hora",
			$fecha
		));

		if ($wpdb->last_error) {
			error_log('Avance DB Error in get_booked_hours: ' . $wpdb->last_error);
			return [];
		}

		$hours = $result ? $result : [];

		// Guardar en Transient por 30 minutos (persiste en BD)
		set_transient($cache_key, $hours, 30 * MINUTE_IN_SECONDS);

		// También en wp_cache para acceso rápido en misma sesión
		wp_cache_set($cache_key, $hours, 'avance', 10 * MINUTE_IN_SECONDS);

		return $hours;
	}

	public static function get_booked_hours_batch($fechas) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		if (empty($fechas) || !is_array($fechas)) {
			return [];
		}

		// Sanitizar fechas
		$fechas_sanitized = array_map('sanitize_text_field', $fechas);
		$fechas_sanitized = array_filter($fechas_sanitized); // Remover vacíos

		if (empty($fechas_sanitized)) {
			return [];
		}

		// Resultado agrupado
		$grouped = [];

		// Verificar caché individual primero (rápido para fechas ya cacheadas)
		$fechas_sin_cache = [];
		foreach ($fechas_sanitized as $fecha) {
			$cache_key = 'avance_booked_hours_' . sanitize_key($fecha);
			$cached = get_transient($cache_key);

			if ($cached !== false) {
				$grouped[$fecha] = $cached;
			} else {
				$fechas_sin_cache[] = $fecha;
			}
		}

		// Si todas están en caché, retornar inmediatamente
		if (empty($fechas_sin_cache)) {
			return $grouped;
		}

		// Consultar BD solo para fechas sin caché
		$placeholders = implode(',', array_fill(0, count($fechas_sin_cache), '%s'));

		$results = $wpdb->get_results($wpdb->prepare(
			"SELECT fecha, LEFT(hora, 5) as hora FROM $table_name
			 WHERE fecha IN ($placeholders) AND estado = 'ocupado'
			 ORDER BY fecha, hora",
			$fechas_sin_cache
		));

		if ($wpdb->last_error) {
			error_log('Avance DB Error in get_booked_hours_batch: ' . $wpdb->last_error);
			return $grouped;
		}

		// Agrupar y cachear individual
		foreach ($results as $row) {
			if (!isset($grouped[$row->fecha])) {
				$grouped[$row->fecha] = [];
			}
			$grouped[$row->fecha][] = $row->hora;
		}

		// Guardar en caché transient (30 minutos)
		foreach ($grouped as $fecha => $hours) {
			$cache_key = 'avance_booked_hours_' . sanitize_key($fecha);
			set_transient($cache_key, $hours, 30 * MINUTE_IN_SECONDS);
		}

		return $grouped;
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

	public static function invalidate_cache($fecha) {
		// Invalidar caché individual por fecha
		$cache_key = 'avance_booked_hours_' . sanitize_key($fecha);
		wp_cache_delete($cache_key);

		// Nota: Batch cache se invalida automáticamente por TTL (10 minutos)
		// No es necesario invalidar manualmente
	}

	public static function table_name() {
		global $wpdb;
		return $wpdb->prefix . self::$table_name;
	}
}
