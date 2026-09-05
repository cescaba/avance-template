<?php
/**
 * IP Blacklist
 * Detecta y bloquea IPs de bots conocidos
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_IP_Blacklist {

	const TABLE_NAME = 'avance_ip_blacklist';
	const CACHE_KEY = 'avance_blacklist_ips';
	const CACHE_TIMEOUT = DAY_IN_SECONDS;

	/**
	 * Crear tabla de blacklist
	 */
	public static function create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			ip_address VARCHAR(45) NOT NULL UNIQUE,
			reason VARCHAR(100),
			country VARCHAR(2),
			is_bot BOOLEAN DEFAULT TRUE,
			added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			INDEX ip_idx (ip_address),
			INDEX bot_idx (is_bot)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		// Cargar blacklist conocida
		self::load_default_blacklist();
	}

	/**
	 * Verificar si IP está en blacklist
	 */
	public static function is_blocked($ip) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		// Intentar caché primero
		$blacklist = get_transient(self::CACHE_KEY);
		if ($blacklist === false) {
			$blacklist = $wpdb->get_col("SELECT ip_address FROM {$table_name} WHERE is_bot = 1");
			set_transient(self::CACHE_KEY, $blacklist, self::CACHE_TIMEOUT);
		}

		return in_array($ip, $blacklist);
	}

	/**
	 * Agregar IP a blacklist
	 */
	public static function block_ip($ip, $reason = 'suspicious_activity', $country = null) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$wpdb->insert(
			$table_name,
			[
				'ip_address' => $ip,
				'reason' => $reason,
				'country' => $country,
				'is_bot' => true,
			],
			['%s', '%s', '%s', '%d']
		);

		// Limpiar caché
		delete_transient(self::CACHE_KEY);
	}

	/**
	 * Cargar blacklist de bots conocidos
	 */
	private static function load_default_blacklist() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		// Verificar si ya existe
		$count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
		if ($count > 0) {
			return;
		}

		// Bots conocidos y atacantes frecuentes
		$known_bots = [
			'45.142.120.0' => 'Yandex Bot',
			'44.55.86.0' => 'Amazon AWS Bot',
			'34.64.0.0' => 'Google Cloud Bot',
			'207.46.13.0' => 'Bing Bot',
			'66.249.64.0' => 'Google Bot',
			'40.77.167.0' => 'Bing Crawler',
			'192.0.2.0' => 'TEST-NET-1',
			'198.51.100.0' => 'TEST-NET-2',
			'203.0.113.0' => 'TEST-NET-3',
		];

		foreach ($known_bots as $ip => $reason) {
			$wpdb->insert(
				$table_name,
				[
					'ip_address' => $ip,
					'reason' => $reason,
					'is_bot' => true,
				]
			);
		}
	}

	/**
	 * Detectar patrón de ataque (muchos intentos fallidos rápido)
	 */
	public static function detect_attack($ip) {
		global $wpdb;
		$attempts_table = $wpdb->prefix . 'avance_form_attempts';

		// Contar intentos fallidos en últimos 5 minutos
		$failed_attempts = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM {$attempts_table}
			WHERE ip_address = %s
			AND last_attempt > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
			AND attempts > 3",
			$ip
		));

		// Si hay más de 10 intentos fallidos en 5 min, es probable ataque
		if ($failed_attempts > 10) {
			self::block_ip($ip, 'detected_attack_pattern');
			return true;
		}

		return false;
	}

	/**
	 * Obtener información de IP (geolocalización básica)
	 */
	public static function get_ip_info($ip) {
		// API gratuita de geolocalización
		$response = wp_remote_get("https://ipapi.co/{$ip}/json/", [
			'timeout' => 5,
		]);

		if (is_wp_error($response)) {
			return ['country' => 'Unknown'];
		}

		$body = json_decode(wp_remote_retrieve_body($response), true);
		return [
			'country' => $body['country_code'] ?? 'Unknown',
			'city' => $body['city'] ?? '',
			'isp' => $body['org'] ?? '',
		];
	}
}

// Crear tabla al cargar
add_action('wp_loaded', ['Avance_IP_Blacklist', 'create_table']);
