<?php
/**
 * Spam Detector
 * Machine Learning básico para detección de spam
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Spam_Detector {

	const TABLE_NAME = 'avance_spam_analytics';

	/**
	 * Modo de logging de spam
	 * 'all' = Opción 1: Guardar todos (para tener datos históricos)
	 * 'gray_zone' = Opción 3: Guardar solo zona gris (30-70) (para optimizar BD)
	 */
	const LOGGING_MODE = 'all'; // Cambiar a 'gray_zone' cuando BD sea muy grande

	/**
	 * Crear tabla de analytics
	 */
	public static function create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			ip_address VARCHAR(45),
			email VARCHAR(255),
			message_length INT,
			word_count INT,
			has_urls BOOLEAN,
			has_html BOOLEAN,
			repeated_chars BOOLEAN,
			is_spam BOOLEAN DEFAULT FALSE,
			spam_score FLOAT DEFAULT 0,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			INDEX spam_idx (is_spam),
			INDEX score_idx (spam_score),
			INDEX email_idx (email)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
	}

	/**
	 * Analizar mensaje y calcular spam score
	 */
	public static function analyze_message($message, $nombre, $email) {
		$score = 0;
		$factors = [];

		// 1. Análisis de longitud
		$msg_length = strlen($message);
		$word_count = str_word_count($message);

		if ($msg_length < 10) {
			$score += 20;
			$factors['too_short'] = true;
		}

		if ($word_count < 3) {
			$score += 15;
			$factors['few_words'] = true;
		}

		// 2. Detectar URLs
		if (preg_match('/(http|https|ftp):\/\//', $message)) {
			$score += 30;
			$factors['has_urls'] = true;
		}

		// 3. Detectar HTML
		if ($message !== strip_tags($message)) {
			$score += 25;
			$factors['has_html'] = true;
		}

		// 4. Caracteres repetidos
		if (preg_match('/(.)\1{3,}/', $message)) {
			$score += 20;
			$factors['repeated_chars'] = true;
		}

		// 5. Palabras spam conocidas
		$spam_words = ['viagra', 'casino', 'bitcoin', 'forex', 'lottery', 'dating', 'xxx'];
		$msg_lower = strtolower($message . ' ' . $nombre);
		foreach ($spam_words as $word) {
			if (stripos($msg_lower, $word) !== false) {
				$score += 40;
				$factors['spam_keyword'] = $word;
				break;
			}
		}

		// 6. CAPS LOCK excesivo
		$upper_count = strlen(preg_replace('/[^A-Z]/', '', $message));
		$cap_ratio = ($msg_length > 0) ? ($upper_count / $msg_length) : 0;
		if ($cap_ratio > 0.5) {
			$score += 15;
			$factors['excessive_caps'] = true;
		}

		// 7. Email sospechoso
		if (self::is_suspicious_email($email)) {
			$score += 25;
			$factors['suspicious_email'] = true;
		}

		// Limitar score a 100
		$score = min($score, 100);

		// Clasificar como spam si score > 50
		$is_spam = ($score > 50);

		return [
			'score' => $score,
			'is_spam' => $is_spam,
			'factors' => $factors,
			'message_length' => $msg_length,
			'word_count' => $word_count,
		];
	}

	/**
	 * Verificar si email es sospechoso
	 */
	private static function is_suspicious_email($email) {
		$domain = substr($email, strpos($email, '@') + 1);

		// Dominios temporales conocidos
		$temp_domains = ['tempmail.com', 'guerrillamail.com', '10minutemail.com', 'throwaway.email'];
		foreach ($temp_domains as $temp) {
			if (stripos($domain, $temp) !== false) {
				return true;
			}
		}

		// Email con números excesivos (typical spam)
		if (preg_match('/\d{5,}/', $email)) {
			return true;
		}

		return false;
	}

	/**
	 * Guardar análisis
	 * OPCIÓN 1: Guardar TODO para tener datos históricos
	 */
	public static function log_analysis($data, $analysis_result) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$wpdb->insert(
			$table_name,
			[
				'ip_address' => Avance_Rate_Limiter::get_client_ip(),
				'email' => $data['email'],
				'message_length' => $analysis_result['message_length'],
				'word_count' => $analysis_result['word_count'],
				'has_urls' => isset($analysis_result['factors']['has_urls']) ? 1 : 0,
				'has_html' => isset($analysis_result['factors']['has_html']) ? 1 : 0,
				'repeated_chars' => isset($analysis_result['factors']['repeated_chars']) ? 1 : 0,
				'is_spam' => $analysis_result['is_spam'] ? 1 : 0,
				'spam_score' => $analysis_result['score'],
			]
		);
	}

	/**
	 * Guardar análisis SOLO si está en "zona gris" (casos dudosos)
	 * OPCIÓN 3: Guardar solo scores 30-70 para optimizar BD
	 *
	 * Casos no guardados:
	 * - 0-29: Muy probable legítimo (no hay duda)
	 * - 71-100: Muy probable spam (no hay duda)
	 *
	 * Casos guardados:
	 * - 30-70: Zona gris (casos dudosos que valen la pena revisar)
	 */
	public static function log_analysis_gray_zone($data, $analysis_result) {
		$score = $analysis_result['score'];

		// Solo guardar si está en zona gris (30-70)
		if ($score >= 30 && $score <= 70) {
			self::log_analysis($data, $analysis_result);
		}
	}

	/**
	 * Obtener estadísticas de spam
	 */
	public static function get_stats() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;

		return [
			'total_analyzed' => $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}"),
			'total_spam_blocked' => $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE is_spam = 1"),
			'avg_score' => $wpdb->get_var("SELECT AVG(spam_score) FROM {$table_name}"),
			'spam_rate' => $wpdb->get_var("SELECT ROUND(COUNT(*) * 100 / (SELECT COUNT(*) FROM {$table_name})) FROM {$table_name} WHERE is_spam = 1") . '%',
		];
	}
}

// Crear tabla al cargar
add_action('wp_loaded', ['Avance_Spam_Detector', 'create_table']);
