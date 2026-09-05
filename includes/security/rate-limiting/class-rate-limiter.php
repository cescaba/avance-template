<?php
/**
 * Rate Limiter
 * Controla el número de envíos por IP
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Rate_Limiter {

	const MAX_ATTEMPTS = 5;
	const TIMEOUT = HOUR_IN_SECONDS;
	const TABLE_NAME = 'avance_form_attempts';

	/**
	 * Crear tabla de intentos si no existe
	 */
	public static function create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			ip_address VARCHAR(45) NOT NULL,
			hour_window VARCHAR(13) NOT NULL COMMENT 'Formato YYYY-MM-DD-HH para agrupar por hora',
			email VARCHAR(255),
			attempts INT DEFAULT 1,
			first_attempt DATETIME DEFAULT CURRENT_TIMESTAMP,
			last_attempt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			blocked BOOLEAN DEFAULT FALSE,
			reason VARCHAR(100),
			PRIMARY KEY (id),
			UNIQUE KEY unique_ip_hour (ip_address, hour_window),
			INDEX ip_idx (ip_address),
			INDEX email_idx (email),
			INDEX blocked_idx (blocked)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
	}

	/**
	 * Validar en UNA sola query: IP bloqueada + intentos + duplicados
	 * OPTIMIZADO: Consolida 3 queries en 1 para máxima performance
	 */
	public static function validate_comprehensive($ip, $email) {
		global $wpdb;
		$attempts_table = $wpdb->prefix . self::TABLE_NAME;
		$contacts_table = $wpdb->prefix . 'avance_contacts';

		// UNA SOLA QUERY: Obtiene todo lo que necesitas
		$result = $wpdb->get_row($wpdb->prepare(
			"SELECT
				MAX(CASE WHEN a.blocked = 1 THEN 1 ELSE 0 END) as is_blocked,
				COUNT(DISTINCT a.id) as attempt_count,
				(SELECT COUNT(*) FROM {$contacts_table}
				 WHERE (email = %s)
				 AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)) as duplicate_count
			FROM {$attempts_table} a
			WHERE a.ip_address = %s
			AND a.last_attempt > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
			$email,
			$ip
		));

		// Validar IP bloqueada
		if ($result && $result->is_blocked) {
			return [
				'allowed' => false,
				'reason' => 'ip_blocked',
				'message' => 'Tu IP ha sido bloqueada temporalmente.',
			];
		}

		// Validar rate limit (intentos)
		$attempt_count = ($result) ? intval($result->attempt_count) : 0;
		if ($attempt_count >= self::MAX_ATTEMPTS) {
			self::block_ip($ip, 'rate_limit_exceeded');
			return [
				'allowed' => false,
				'reason' => 'rate_limit_exceeded',
				'message' => 'Demasiados intentos. Por favor intenta en 1 hora.',
			];
		}

		// Validar duplicados (email 24h)
		$duplicate_count = ($result) ? intval($result->duplicate_count) : 0;
		if ($duplicate_count > 0) {
			return [
				'allowed' => false,
				'reason' => 'duplicate_email',
				'message' => 'Este email fue registrado hace poco. Intenta en 24 horas.',
			];
		}

		return [
			'allowed' => true,
			'remaining' => self::MAX_ATTEMPTS - $attempt_count,
		];
	}

	/**
	 * Verificar si IP está dentro del límite (mantener para compatibilidad)
	 */
	public static function check_ip($ip) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		// Verificar si está bloqueada
		$blocked = $wpdb->get_var($wpdb->prepare(
			"SELECT blocked FROM {$table_name} WHERE ip_address = %s AND blocked = 1",
			$ip
		));

		if ($blocked) {
			return [
				'allowed' => false,
				'reason' => 'ip_blocked',
				'message' => 'Tu IP ha sido bloqueada temporalmente.',
			];
		}

		// Contar intentos en última hora
		$count = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM {$table_name}
			WHERE ip_address = %s
			AND last_attempt > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
			$ip
		));

		if ($count >= self::MAX_ATTEMPTS) {
			self::block_ip($ip, 'rate_limit_exceeded');
			return [
				'allowed' => false,
				'reason' => 'rate_limit_exceeded',
				'message' => 'Demasiados intentos. Intenta en 1 hora.',
			];
		}

		return [
			'allowed' => true,
			'remaining' => self::MAX_ATTEMPTS - $count,
		];
	}

	/**
	 * Registrar intento (Atómico - sin race condition)
	 * Usa ON DUPLICATE KEY UPDATE para evitar duplicados simultáneos
	 * Un solo INSERT que puede crear O actualizar, sin hueco de carrera
	 */
	public static function log_attempt($ip, $email, $status = 'success', $reason = '') {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$hour_window = date('Y-m-d-H');

		$wpdb->query($wpdb->prepare(
			"INSERT INTO {$table_name} (ip_address, hour_window, email, attempts, reason, first_attempt, last_attempt)
			VALUES (%s, %s, %s, 1, %s, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
			ON DUPLICATE KEY UPDATE
				attempts = attempts + 1,
				last_attempt = CURRENT_TIMESTAMP,
				email = IF(email IS NULL, %s, email)",
			$ip,
			$hour_window,
			$email,
			$reason,
			$email
		));
	}

	/**
	 * Verificar duplicados en 24h
	 */
	public static function check_duplicate($email, $numero) {
		global $wpdb;
		$contacts_table = $wpdb->prefix . 'avance_contacts';

		$duplicate = $wpdb->get_row($wpdb->prepare(
			"SELECT id FROM {$contacts_table}
			WHERE (email = %s OR numero = %s)
			AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)",
			$email,
			$numero
		));

		if ($duplicate) {
			return [
				'is_duplicate' => true,
				'message' => 'Ya enviaste un contacto hace poco. Intenta más tarde.',
			];
		}

		return ['is_duplicate' => false];
	}

	/**
	 * Bloquear IP
	 */
	private static function block_ip($ip, $reason) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$wpdb->update(
			$table_name,
			[
				'blocked' => 1,
				'reason' => $reason,
			],
			['ip_address' => $ip]
		);
	}

	/**
	 * Obtener IP del cliente
	 */
	public static function get_client_ip() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
		}
		return sanitize_text_field($ip);
	}
}

// Crear tabla al activar
add_action('wp_loaded', ['Avance_Rate_Limiter', 'create_table']);
