<?php
/**
 * Appointment Validator - Con protecciones contra spam y ataques
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Appointment_Validator {

	/**
	 * Patrones de SQL injection y ataques
	 */
	private static $spam_patterns = array(
		'sql_injection' => "/(union|select|insert|update|delete|drop|create|alter|exec|script|javascript|--|\*|;)/i",
		'html_tags' => '/<[^>]+>/i',
		'urls' => '/(http|https|ftp):\/\//',
	);

	/**
	 * Palabras prohibidas (spam/trolling)
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
		'porn',
	);

	/**
	 * Validar datos del agendamiento
	 */
	public static function validate($data) {
		$errors = array();
		$reason = '';

		// 1️⃣ VALIDAR CAMPOS REQUERIDOS
		if (empty($data['nombre'])) {
			$errors[] = 'El nombre es requerido';
		} elseif (strlen(trim($data['nombre'])) < 3) {
			$errors[] = 'El nombre debe tener al menos 3 caracteres';
		} elseif (strlen(trim($data['nombre'])) > 100) {
			$errors[] = 'El nombre es demasiado largo';
		}

		if (empty($data['whatsapp'])) {
			$errors[] = 'El WhatsApp es requerido';
		} elseif (!preg_match('/^[0-9\+\-\(\)]{10,20}$/', $data['whatsapp'])) {
			$errors[] = 'El número de WhatsApp no es válido';
		}

		if (empty($data['servicio'])) {
			$errors[] = 'Debe seleccionar un servicio';
		} elseif (strlen($data['servicio']) > 100) {
			$errors[] = 'El servicio es inválido';
		}

		if (empty($data['fecha'])) {
			$errors[] = 'La fecha es requerida';
		} elseif (!self::is_valid_date($data['fecha'])) {
			$errors[] = 'La fecha no es válida';
		} elseif (!self::is_future_date($data['fecha'])) {
			$errors[] = 'La fecha debe ser futura';
		}

		if (empty($data['hora'])) {
			$errors[] = 'La hora es requerida';
		} elseif (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $data['hora'])) {
			$errors[] = 'La hora no es válida';
		}

		// Si hay errores de validación, retornar antes de verificar seguridad
		if (!empty($errors)) {
			return array(
				'valid' => false,
				'errors' => $errors,
				'reason' => 'validation_error',
			);
		}

		// 2️⃣ VERIFICAR SEGURIDAD (SQL injection, XSS, HTML)
		$security_check = self::security_check($data);
		if (!$security_check['valid']) {
			return $security_check;
		}

		// 3️⃣ VERIFICAR SPAM/TROLLING
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
		$suspicious_fields = array('nombre', 'servicio', 'whatsapp');

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
						'reason' => 'security_threat_' . $pattern_name,
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
		$message = strtolower($data['nombre'] . ' ' . $data['servicio']);

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

		// Detectar spam por repetición de caracteres
		if (self::is_repetitive_spam($message)) {
			return array(
				'valid' => false,
				'errors' => array('Patrón de spam detectado'),
				'reason' => 'repetitive_spam',
			);
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
	 * Verificar si la fecha es válida (formato Y-m-d)
	 */
	private static function is_valid_date($date) {
		$d = DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') === $date;
	}

	/**
	 * Verificar si la fecha es futura
	 */
	private static function is_future_date($date) {
		return strtotime($date) > time();
	}

	/**
	 * Sanitizar datos
	 */
	public static function sanitize($data) {
		return array(
			'nombre' => sanitize_text_field($data['nombre'] ?? ''),
			'whatsapp' => preg_replace('/[^0-9\+]/', '', $data['whatsapp'] ?? ''),
			'servicio' => sanitize_text_field($data['servicio'] ?? ''),
			'fecha' => sanitize_text_field($data['fecha'] ?? ''),
			'hora' => sanitize_text_field($data['hora'] ?? ''),
			'notas' => sanitize_textarea_field($data['notas'] ?? ''),
		);
	}
}
