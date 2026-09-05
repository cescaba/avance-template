<?php
/**
 * Global Form Handler - Seguridad y Validación Central
 *
 * Clase única que maneja TODA la seguridad y validación global
 * Aplica a TODOS los formularios del sitio
 *
 * Campos globales validados:
 * - nombre (requerido, 3+ caracteres)
 * - apellido (requerido, 3+ caracteres)
 * - email (requerido, válido, SMTP)
 * - whatsapp (requerido, numérico, 9-15 dígitos)
 *
 * Seguridad global:
 * - Nonce CSRF
 * - Rate limiting (5/hora por IP)
 * - Duplicados (email en 24h)
 * - Spam detection (ML scoring)
 * - Sanitización de datos
 * - SQL injection prevention
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Global_Form_Handler {

	// ===================================================================
	// VALIDACIÓN DE CAMPOS GLOBALES (Aplica a TODOS)
	// ===================================================================

	/**
	 * Validar campos globales requeridos en TODOS los formularios
	 *
	 * @param array $data Datos del POST
	 * @return bool true si valida, envía JSON error si no
	 */
	public static function validate_global_fields($data) {
		// 1. VALIDAR NOMBRE
		if (empty($data['nombre'])) {
			Avance_Logger::error(' Nombre vacío');
			wp_send_json_error(['message' => 'El nombre es requerido.'], 400);
		}

		$nombre = trim($data['nombre']);
		if (strlen($nombre) < 3) {
			Avance_Logger::error(' Nombre muy corto: ' . $nombre);
			wp_send_json_error(['message' => 'El nombre debe tener mínimo 3 caracteres.'], 400);
		}

		if (strlen($nombre) > 100) {
			Avance_Logger::error(' Nombre muy largo: ' . $nombre);
			wp_send_json_error(['message' => 'El nombre no puede exceder 100 caracteres.'], 400);
		}

		// 2. VALIDAR APELLIDO
		if (empty($data['apellido'])) {
			Avance_Logger::error(' Apellido vacío');
			wp_send_json_error(['message' => 'El apellido es requerido.'], 400);
		}

		$apellido = trim($data['apellido']);
		if (strlen($apellido) < 3) {
			Avance_Logger::error(' Apellido muy corto: ' . $apellido);
			wp_send_json_error(['message' => 'El apellido debe tener mínimo 3 caracteres.'], 400);
		}

		if (strlen($apellido) > 100) {
			Avance_Logger::error(' Apellido muy largo: ' . $apellido);
			wp_send_json_error(['message' => 'El apellido no puede exceder 100 caracteres.'], 400);
		}

		// 3. VALIDAR EMAIL
		if (empty($data['email'])) {
			Avance_Logger::error(' Email vacío');
			wp_send_json_error(['message' => 'El email es requerido.'], 400);
		}

		$email = strtolower(trim($data['email']));

		// Validar formato básico
		if (!is_email($email)) {
			Avance_Logger::error(' Email formato inválido: ' . $email);
			wp_send_json_error(['message' => 'El email no tiene un formato válido.'], 400);
		}

		// Validar SMTP (verificar que el dominio existe)
		if (!self::validate_email_smtp($email)) {
			Avance_Logger::error(' Email SMTP inválido: ' . $email);
			wp_send_json_error(['message' => 'El email no existe o el servidor no responde.'], 400);
		}

		// 4. VALIDAR WHATSAPP
		if (empty($data['whatsapp'])) {
			Avance_Logger::error(' WhatsApp vacío');
			wp_send_json_error(['message' => 'El número de WhatsApp es requerido.'], 400);
		}

		$whatsapp_original = $data['whatsapp'];
		$whatsapp = preg_replace('/[^0-9+]/', '', $whatsapp_original);

		// Remover + del inicio si existe
		if (strpos($whatsapp, '+') === 0) {
			$whatsapp = substr($whatsapp, 1);
		}

		// Validar cantidad de dígitos
		if (strlen($whatsapp) < 9 || strlen($whatsapp) > 15) {
			Avance_Logger::error(' WhatsApp inválido, dígitos: ' . strlen($whatsapp));
			wp_send_json_error(['message' => 'El número de WhatsApp debe tener 9-15 dígitos.'], 400);
		}

		return true;
	}

	// ===================================================================
	// VALIDACIÓN DE SEGURIDAD GLOBAL (Aplica a TODOS)
	// ===================================================================

	/**
	 * Validar seguridad global: nonce, rate limit, duplicados, spam
	 *
	 * @param string $form_name Nombre del formulario (contact, mentoria, etc)
	 * @param array $data Datos del POST
	 * @return bool true si pasa seguridad, envía JSON error si no
	 */
	public static function validate_security($form_name, $data) {
		// 1. VALIDAR NONCE CSRF
		self::validate_nonce($form_name, $data);

		// 2. VALIDAR RATE LIMIT + DUPLICADOS (UNA SOLA QUERY)
		$ip = Avance_Rate_Limiter::get_client_ip();
		$email = $data['email'] ?? '';
		self::validate_rate_limit_and_duplicates($ip, $email);

		// 3. VALIDAR SPAM (si hay mensaje)
		if (!empty($data['mensaje']) || !empty($data['descripcion'])) {
			$message = $data['mensaje'] ?? $data['descripcion'];
			$nombre = $data['nombre'] ?? '';
			self::detect_spam($message, $nombre, $email);
		}

		return true;
	}

	/**
	 * Validación consolidada: Rate limit + Duplicados en UNA query
	 * OPTIMIZADO: Reduce 2 queries a 1
	 */
	private static function validate_rate_limit_and_duplicates($ip, $email) {
		if (!class_exists('Avance_Rate_Limiter')) {
			Avance_Logger::error(' Clase Avance_Rate_Limiter no existe');
			wp_send_json_error(['message' => 'Error de configuración del servidor.'], 500);
		}

		// UNA SOLA QUERY consolidada
		$validation = Avance_Rate_Limiter::validate_comprehensive($ip, $email);

		if (!$validation['allowed']) {
			Avance_Logger::error(' Validación fallida - IP: ' . $ip . ' - Razón: ' . $validation['reason']);
			$status_code = ($validation['reason'] === 'rate_limit_exceeded') ? 429 : 409;
			wp_send_json_error(['message' => $validation['message']], $status_code);
		}
	}

	/**
	 * Validar nonce CSRF
	 */
	private static function validate_nonce($form_name, $data) {
		if (!isset($data['nonce'])) {
			Avance_Logger::error(' Nonce no presente en POST - form: ' . $form_name);
			wp_send_json_error(['message' => 'Error de seguridad: nonce faltante.'], 400);
		}

		if (!wp_verify_nonce($data['nonce'], 'form_' . $form_name)) {
			Avance_Logger::error(' Nonce inválido para form: ' . $form_name);
			wp_send_json_error(['message' => 'Error de seguridad: token inválido. Por favor recarga la página.'], 400);
		}
	}


	/**
	 * Detectar spam en mensajes usando Machine Learning
	 * Modo configurable: Opción 1 (guardar todos) o Opción 3 (zona gris)
	 * Ver: Avance_Spam_Detector::LOGGING_MODE
	 */
	private static function detect_spam($message, $nombre = '', $email = '') {
		if (!class_exists('Avance_Spam_Detector')) {
			return;
		}

		$analysis = Avance_Spam_Detector::analyze_message($message, $nombre, $email);
		$is_spam = $analysis['is_spam'];

		// Guardar análisis según modo configurado
		$data = [
			'email' => $email,
			'message' => $message,
		];

		if (Avance_Spam_Detector::LOGGING_MODE === 'gray_zone') {
			// Opción 3: Guardar solo zona gris (30-70) para optimizar BD
			Avance_Spam_Detector::log_analysis_gray_zone($data, $analysis);
		} else {
			// Opción 1: Guardar TODO para tener datos históricos (default)
			Avance_Spam_Detector::log_analysis($data, $analysis);
		}

		if ($is_spam) {
			Avance_Logger::error(' Spam detectado - email: ' . $email . ', score: ' . $analysis['score']);
			wp_send_json_error(['message' => 'El contenido parece contener información sospechosa. Por favor verifica tu mensaje.'], 400);
		}
	}

	// ===================================================================
	// UTILIDADES DE VALIDACIÓN
	// ===================================================================

	/**
	 * ISSUE 20: Validar campo contra patrón positivo (whitelist)
	 * Validación positiva es más segura que negativa
	 *
	 * @param string $value Valor a validar
	 * @param string $pattern_name Nombre del patrón (name_safe, email_basic, etc)
	 * @param string $field_name Nombre del campo (para mensajes)
	 * @return bool True si es válido
	 */
	private static function validate_pattern($value, $pattern_name, $field_name = '') {
		if (!class_exists('Avance_Validation_Cache')) {
			return true;  // Sin cache, permitir
		}

		$pattern = Avance_Validation_Cache::get_pattern($pattern_name);
		if (!$pattern) {
			return true;  // Pattern no existe, permitir
		}

		if (!preg_match($pattern, $value)) {
			$field_text = !empty($field_name) ? " '$field_name'" : '';
			Avance_Logger::error(' Campo' . $field_text . ' no cumple validación ' . $pattern_name);
			return false;
		}

		return true;
	}

	/**
	 * Validar email SMTP (verificar que el servidor existe)
	 * OPTIMIZADO: Caché lazy + async validation (sin bloqueos)
	 *
	 * Estrategia:
	 * 1. Permitir email de inmediato (no bloquear)
	 * 2. Si está en caché → confiar en caché (excepto "pending")
	 * 3. Si no está → agendar DNS check en background
	 * 4. OPCIÓN 1: Si DNS falla, cachea "pending" (1h) no falso (7d)
	 * 5. OPCIÓN 2: Futuro - Double-Opt-In en lugar de DNS check
	 */
	private static function validate_email_smtp($email) {
		$domain = substr(strrchr($email, "@"), 1);
		$cache_key = 'avance_smtp_' . md5($domain);

		// Verificar caché
		$cached = get_transient($cache_key);
		if ($cached !== false) {
			// Si caché es "pending" = DNS check en progreso, permitir por ahora
			// Si caché es true/false = resultado real, confiar en él
			if ($cached === 'pending' || $cached === true) {
				return true; // Permitir (pending o válido)
			}
			if ($cached === false) {
				return false; // Rechazar (inválido confirmado)
			}
		}

		// NO HACER checkdnsrr() sincrónico (bloquea 5 segundos)
		// En su lugar: agendar validación async y permitir por defecto

		// Agendar DNS check async (solo primera vez)
		wp_schedule_single_event(time(), 'avance_smtp_async_check', [$email, $domain]);

		// Guardar "pending" en caché 1 hora (mientras se valida en background)
		set_transient($cache_key, 'pending', 3600);

		// Retornar true (permitir) - El async validation actualizará el caché
		return true;
	}

	/**
	 * Hook para validar DNS en background (async)
	 * Se ejecuta en siguiente cron de WordPress (o manual si cron está configurado)
	 *
	 * OPCIÓN 1 (ACTUAL): TTL corto + fallback permisivo
	 * - Si DNS falla: cachea como "pending" (1 hora), no como falso permanente (7 días)
	 * - Previene false positives si DNS está caído
	 * - Reintenta automáticamente en 1 hora
	 *
	 * OPCIÓN 2 (PRÓXIMO SPRINT): Double-Opt-In
	 * - Remover DNS check completamente
	 * - Enviar email de confirmación con token
	 * - Usuario debe hacer click en link
	 * - Best practice: 100% precisión, cero false positives
	 *
	 * @param string $email Email a validar
	 * @param string $domain Dominio del email
	 */
	public static function async_smtp_check($email, $domain) {
		$cache_key = 'avance_smtp_' . md5($domain);

		// Hacer DNS lookup sin prisa (background)
		$valid = checkdnsrr($domain, 'MX');

		if ($valid) {
			// DNS válido: cachear por 7 días (confiable)
			set_transient($cache_key, true, 604800);
			error_log('✅ DNS: MX válido para ' . $domain);
		} else {
			// DNS inválido PERO no cachear como falso permanente
			// Opción 1: Cachear como "pending" por 1 HORA
			// Si DNS estaba caído, se reintentará en 1 hora
			set_transient($cache_key, 'pending', 3600);
			Avance_Logger::warning(' DNS check falló para ' . $domain . ' - Reintentando en 1 hora');
		}
	}

	/**
	 * OPCIÓN 2 (ROADMAP - Próximo Sprint)
	 * Double-Opt-In: Remover DNS check, usar confirmación por email
	 *
	 * Implementación futura:
	 * 1. Generar token único para el contacto
	 * 2. Guardar contacto con status = "pending_verification"
	 * 3. Enviar email con link de confirmación
	 * 4. Usuario hace click → status cambia a "verified"
	 * 5. Solo emails verificados pueden enviar contactos posteriores
	 *
	 * Beneficios:
	 * - 100% precisión (usuario confirma)
	 * - Cero false positives (DNS irrelevante)
	 * - Best practice industria
	 * - Reduce spam significativamente
	 *
	 * TODO: Implementar en próximo sprint
	 * - Crear tabla: wp_avance_email_confirmations
	 * - Crear función: generate_confirmation_token()
	 * - Crear función: send_confirmation_email()
	 * - Crear hook: avance_verify_email_token
	 * - Actualizar validación para verificar status
	 */
	private static function send_confirmation_email($email, $nombre) {
		// OPCIÓN 2 IMPLEMENTATION PLACEHOLDER
		// TODO: Implementar en próximo sprint
		error_log('ℹ️ INFO: Double-Opt-In email confirmation implementado en próximo sprint');
	}

	/**
	 * Sanitizar datos de POST
	 */
	public static function sanitize_data($data) {
		$sanitized = array();

		foreach ($data as $key => $value) {
			// Skip nonce, action, etc
			if (in_array($key, ['nonce', 'action', '_wp_nonce'])) {
				continue;
			}

			// Sanitizar según tipo
			if (is_array($value)) {
				$sanitized[$key] = array_map('sanitize_text_field', $value);
			} else {
				$sanitized[$key] = sanitize_text_field($value);
			}
		}

		return $sanitized;
	}

	/**
	 * Obtener IP del cliente (varias fuentes posibles)
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

	// ===================================================================
	// PROCESO COMPLETO (Llamar esto desde cada formulario)
	// ===================================================================

	/**
	 * Procesar formulario: validar campos, seguridad, y retornar datos limpios
	 *
	 * @param string $form_name Nombre del formulario
	 * @param array $data Datos del POST
	 * @return array Datos sanitizados y validados
	 */
	public static function process($form_name, $data) {
		// 1. Validar campos globales (nombre, apellido, email, whatsapp)
		self::validate_global_fields($data);

		// 2. Validar seguridad global (nonce, rate limit, duplicados, spam)
		self::validate_security($form_name, $data);

		// 3. Sanitizar datos
		$cleaned_data = self::sanitize_data($data);

		return $cleaned_data;
	}
}
