<?php
/**
 * Contact Form Validator
 *
 * Validaciones de seguridad para detectar spam, trolls y ataques
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Contact_Validator {

	/**
	 * Patrones de spam/ataque
	 */
	private static $spam_patterns = array(
		'urls' => '/(http|https|ftp):\/\//',
		'sql_injection' => "/(union|select|insert|update|delete|drop|create|alter|exec|script|javascript)/i",
		'html_tags' => '/<[^>]+>/i',
		'excessive_links' => '/(\S+:\/\/\S+){2,}/',
	);

	/**
	 * Palabras prohibidas (trolling/spam)
	 */
	private static $blocked_keywords = array(
		'viagra',
		'casino',
		'bitcoin',
		'crypto',
		'forex',
		'lottery',
		'dating',
		'xxx',
	);

	/**
	 * Validar datos del formulario
	 *
	 * @param array $data Datos del formulario
	 * @return array ['valid' => bool, 'errors' => array, 'reason' => string]
	 */
	public static function validate($data) {
		$errors = array();
		$reason = '';

		// Validar campos requeridos
		if (empty($data['nombre'])) {
			$errors[] = 'El nombre es requerido';
		}

		if (empty($data['email'])) {
			$errors[] = 'El email es requerido';
		} elseif (!is_email($data['email'])) {
			$errors[] = 'El email no es válido';
		}

		if (empty($data['numero'])) {
			$errors[] = 'El número de WhatsApp es requerido';
		}

		if (empty($data['asunto'])) {
			$errors[] = 'El asunto es requerido';
		}

		// Si hay errores de campos requeridos, retornar
		if (!empty($errors)) {
			return array(
				'valid' => false,
				'errors' => $errors,
				'reason' => 'validation_error',
			);
		}

		// Validaciones de seguridad
		$security_check = self::security_check($data);
		if (!$security_check['valid']) {
			return $security_check;
		}

		// Validaciones de contenido
		$content_check = self::content_check($data);
		if (!$content_check['valid']) {
			return $content_check;
		}

		return array(
			'valid' => true,
			'errors' => array(),
			'reason' => 'passed',
		);
	}

	/**
	 * Verificaciones de seguridad (inyecciones, scripts, etc)
	 */
	private static function security_check($data) {
		$suspicious_fields = array('nombre', 'email', 'asunto', 'mensaje');

		foreach ($suspicious_fields as $field) {
			if (empty($data[$field])) {
				continue;
			}

			$value = $data[$field];

			// Detectar patrones maliciosos
			foreach (self::$spam_patterns as $pattern_name => $pattern) {
				if (preg_match($pattern, $value)) {
					return array(
						'valid' => false,
						'errors' => array('Contenido sospechoso detectado'),
						'reason' => 'security_threat',
					);
				}
			}

			// Detectar HTML/Scripts
			if ($value !== strip_tags($value)) {
				return array(
					'valid' => false,
					'errors' => array('No se permiten etiquetas HTML'),
					'reason' => 'html_injection',
				);
			}
		}

		return array('valid' => true);
	}

	/**
	 * Verificaciones de contenido (spam, trolling, etc)
	 */
	private static function content_check($data) {
		$message = strtolower($data['nombre'] . ' ' . $data['mensaje']);

		// Detectar palabras bloqueadas
		foreach (self::$blocked_keywords as $keyword) {
			if (strpos($message, $keyword) !== false) {
				return array(
					'valid' => false,
					'errors' => array('Contenido no permitido'),
					'reason' => 'spam_keyword',
				);
			}
		}

		// Detectar spam por repetición
		if (self::is_repetitive_spam($message)) {
			return array(
				'valid' => false,
				'errors' => array('Patrón de spam detectado'),
				'reason' => 'repetitive_spam',
			);
		}

		// Detectar troll behavior (mensajes muy cortos o vacíos)
		if (!empty($data['mensaje']) && strlen(trim($data['mensaje'])) < 5) {
			// No es tan restrictivo, pero se podría mejorar con ML
		}

		return array('valid' => true);
	}

	/**
	 * Detectar spam por repetición de caracteres
	 */
	private static function is_repetitive_spam($text) {
		// Detectar más de 3 repeticiones seguidas del mismo carácter
		if (preg_match('/(.)\1{3,}/', $text)) {
			return true;
		}

		return false;
	}

	/**
	 * Sanitizar datos antes de guardar
	 */
	public static function sanitize($data) {
		return array(
			'nombre' => sanitize_text_field($data['nombre'] ?? ''),
			'email' => sanitize_email($data['email'] ?? ''),
			'numero' => preg_replace('/[^0-9+]/', '', $data['numero'] ?? ''),
			'asunto' => sanitize_text_field($data['asunto'] ?? ''),
			'mensaje' => sanitize_textarea_field($data['mensaje'] ?? ''),
		);
	}
}
