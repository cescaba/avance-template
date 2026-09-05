<?php
/**
 * Validation Cache
 * Caché compilado de patrones regex para optimización
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Validation_Cache {

	const CACHE_KEY = 'avance_validation_patterns';
	const CACHE_TIMEOUT = WEEK_IN_SECONDS;

	/**
	 * Patrones compilados (pre-compilados para mejor rendimiento)
	 */
	private static $patterns = null;

	/**
	 * Inicializar caché de patrones
	 * OPCIÓN 1: Mutex Lock previene race condition
	 * OPCIÓN 4: Pre-compilación en activation previene caché vacío
	 */
	public static function init() {
		// Intentar obtener del caché
		self::$patterns = get_transient(self::CACHE_KEY);

		if (self::$patterns === false) {
			// MUTEX LOCK: Solo 1 request compila, otros esperan (máximo 5s)
			$lock_key = 'lock_' . self::CACHE_KEY;
			$lock_acquired = false;

			// Intentar adquirir lock (máximo 5 intentos de 1 segundo)
			for ($attempt = 0; $attempt < 5; $attempt++) {
				if (wp_cache_add($lock_key, 1, '', 5)) {
					// Lock adquirido - este request compila
					$lock_acquired = true;
					break;
				}
				// Lock no adquirido, esperar 1 segundo y reintentar
				sleep(1);
			}

			if ($lock_acquired) {
				// Compilar patrones (solo este request)
				self::$patterns = self::compile_patterns();
				set_transient(self::CACHE_KEY, self::$patterns, self::CACHE_TIMEOUT);

				// Liberar lock
				wp_cache_delete($lock_key);
			} else {
				// No se pudo adquirir lock, obtener del caché (debe estar fresco)
				self::$patterns = get_transient(self::CACHE_KEY);

				// Fallback: Si aún es false, compilar de todas formas
				if (self::$patterns === false) {
					self::$patterns = self::compile_patterns();
					set_transient(self::CACHE_KEY, self::$patterns, self::CACHE_TIMEOUT);
				}
			}
		}
	}

	/**
	 * Compilar patrones regex
	 * Público: necesario para pre-compilación en activation (Opción 4)
	 * ISSUE 20: Validación positiva (whitelist) en lugar de negativa (blacklist)
	 * Blacklist es fácil de evadir. Whitelist es imposible de evadir.
	 */
	public static function compile_patterns() {
		return [
			// URLs - Validación positiva: debe empezar con protocolo válido
			'urls' => '/^(http|https|ftp):\/\//',

			// SQL Injection - NO usar blacklist
			// En su lugar, validar positivamente en cada campo específico
			// Ver: validate_field_name(), validate_field_email(), etc.
			// 'sql_injection' => '/(union|select|insert|...)/i',  ❌ Evadible

			// HTML Tags - Validación positiva: detectar < seguido de cualquier char
			'html_tags' => '/<[a-zA-Z\/!][^>]*>/i',

			// Excessive links - Validación positiva: URL pattern
			'excessive_links' => '/https?:\/\/[^\s]+\s+https?:\/\/[^\s]+/',

			// Repetitive chars - Validación positiva: 4+ caracteres iguales
			'repetitive_chars' => '/(.)\1{3,}/',

			// Email - Validación positiva (RFC 5322 simplificado)
			// local-part@domain.extension
			'email_basic' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',

			// Phone - Validación positiva: solo números, +, -, espacios, paréntesis
			'phone_basic' => '/^[\d\+\-\s\(\)]{9,}$/',

			// Nombres - ISSUE 20: Validación positiva para nombres (solo letras, espacios, acentos)
			'name_safe' => '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-\.]{3,150}$/',

			// Company - ISSUE 20: Validación positiva para empresa (letras, números, algunos símbolos)
			'company_safe' => '/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.&]{3,150}$/',

			// Description - ISSUE 20: Validación positiva (permitir solo caracteres seguros)
			'description_safe' => '/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.,:;!?()]+$/u',
		];
	}

	/**
	 * Obtener patrón compilado
	 */
	public static function get_pattern($pattern_name) {
		if (self::$patterns === null) {
			self::init();
		}

		return self::$patterns[$pattern_name] ?? null;
	}

	/**
	 * Validar contra patrón compilado
	 */
	public static function validate_pattern($pattern_name, $value) {
		$pattern = self::get_pattern($pattern_name);

		if ($pattern === null) {
			return false;
		}

		return preg_match($pattern, $value) > 0;
	}

	/**
	 * Limpiar caché (por ejemplo, después de actualizar patrones)
	 */
	public static function clear_cache() {
		delete_transient(self::CACHE_KEY);
		self::$patterns = null;
	}

	/**
	 * Obtener estadísticas de rendimiento
	 */
	public static function get_stats() {
		return [
			'patterns_cached' => count(self::$patterns ?? []),
			'cache_timeout' => self::CACHE_TIMEOUT,
			'cache_key' => self::CACHE_KEY,
		];
	}
}

/**
 * OPCIÓN 4: Pre-compilar patrones al activar tema
 * Garantiza que caché NUNCA esté vacío
 */
add_action('after_setup_theme', function() {
	// Verificar si patrones están en caché
	if (get_transient('avance_validation_patterns') === false) {
		// Pre-compilar patrones
		$patterns = Avance_Validation_Cache::compile_patterns();
		set_transient('avance_validation_patterns', $patterns, WEEK_IN_SECONDS);
	}
});

// Inicializar caché al cargar
add_action('wp_loaded', ['Avance_Validation_Cache', 'init']);
