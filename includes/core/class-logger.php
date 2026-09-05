<?php
/**
 * Logger - Sistema de logging con niveles
 * ISSUE 18: Estructura de logging con severidad
 *
 * Previene logging de INFO/DEBUG en error_log
 * Solo registra WARNING, ERROR y CRITICAL
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Logger {

	// Log levels
	const LEVEL_DEBUG = 0;      // Detalles internos (no se loga)
	const LEVEL_INFO = 1;       // Info normal (no se loga)
	const LEVEL_WARNING = 2;    // Advertencias (SE LOGA)
	const LEVEL_ERROR = 3;      // Errores (SE LOGA)
	const LEVEL_CRITICAL = 4;   // Fallos críticos (SE LOGA)

	/**
	 * Obtener nombre del nivel
	 */
	private static function level_name($level) {
		$names = [
			self::LEVEL_DEBUG => 'DEBUG',
			self::LEVEL_INFO => 'INFO',
			self::LEVEL_WARNING => 'WARNING',
			self::LEVEL_ERROR => 'ERROR',
			self::LEVEL_CRITICAL => 'CRITICAL',
		];
		return $names[$level] ?? 'UNKNOWN';
	}

	/**
	 * Obtener emoji según nivel
	 */
	private static function level_emoji($level) {
		$emojis = [
			self::LEVEL_DEBUG => '🔍',
			self::LEVEL_INFO => 'ℹ️',
			self::LEVEL_WARNING => '⚠️',
			self::LEVEL_ERROR => '❌',
			self::LEVEL_CRITICAL => '🚨',
		];
		return $emojis[$level] ?? '';
	}

	/**
	 * Log principal
	 * Solo registra WARNING+ (evita log spam)
	 *
	 * @param string $message Mensaje
	 * @param int $level Nivel de severidad (use constantes LEVEL_*)
	 * @param array $context Contexto adicional (opcional)
	 */
	public static function log($message, $level = self::LEVEL_INFO, $context = []) {
		// Solo loguear WARNING y superiores
		if ($level < self::LEVEL_WARNING) {
			return;  // Ignorar DEBUG e INFO
		}

		$level_name = self::level_name($level);
		$emoji = self::level_emoji($level);
		$timestamp = current_time('Y-m-d H:i:s');

		// Construir mensaje formateado
		$formatted = sprintf(
			'%s [%s] %s: %s',
			$emoji,
			$timestamp,
			$level_name,
			$message
		);

		// Agregar contexto si existe
		if (!empty($context)) {
			$formatted .= ' | Context: ' . json_encode($context);
		}

		// Registrar en error_log
		error_log($formatted);
	}

	/**
	 * Log DEBUG (no se registra en error_log)
	 * Usado para detalles internos durante desarrollo
	 */
	public static function debug($message, $context = []) {
		self::log($message, self::LEVEL_DEBUG, $context);
	}

	/**
	 * Log INFO (no se registra en error_log)
	 * Usado para información normal
	 */
	public static function info($message, $context = []) {
		self::log($message, self::LEVEL_INFO, $context);
	}

	/**
	 * Log WARNING (SE REGISTRA)
	 * Usado para situaciones no ideales pero recuperables
	 */
	public static function warning($message, $context = []) {
		self::log($message, self::LEVEL_WARNING, $context);
	}

	/**
	 * Log ERROR (SE REGISTRA)
	 * Usado para errores
	 */
	public static function error($message, $context = []) {
		self::log($message, self::LEVEL_ERROR, $context);
	}

	/**
	 * Log CRITICAL (SE REGISTRA)
	 * Usado para fallos críticos
	 */
	public static function critical($message, $context = []) {
		self::log($message, self::LEVEL_CRITICAL, $context);
	}

	/**
	 * Log de Exception
	 * Captura mensaje + stacktrace
	 */
	public static function exception(Exception $e, $level = self::LEVEL_ERROR) {
		$message = $e->getMessage();
		$context = [
			'exception_class' => get_class($e),
			'file' => $e->getFile(),
			'line' => $e->getLine(),
			'trace' => $e->getTraceAsString(),
		];
		self::log($message, $level, $context);
	}
}
