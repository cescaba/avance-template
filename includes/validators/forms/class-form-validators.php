<?php
/**
 * Form Validators - Validaciones Personalizadas por Formulario
 *
 * Cada formulario tiene validaciones ESPECÍFICAS (adicionales a las globales)
 * Las validaciones globales se aplican automáticamente en Global_Form_Handler
 * Estas validaciones son SOLO para campos específicos de cada form
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Form_Validators {

	// ===================================================================
	// CONTACT FORM - Validar: mensaje (10-5000 caracteres)
	// ===================================================================

	/**
	 * Validar formulario Contact (form-section.php)
	 * Validaciones específicas para form-section
	 */
	public static function validate_contact($data) {
		Avance_Logger::debug('=== VALIDACIÓN CUSTOM: CONTACT (form-section.php) ===');

		// Mensaje es OPCIONAL en form-section
		if (!empty($data['mensaje'])) {
			$mensaje = trim($data['mensaje']);

			if (strlen($mensaje) < 10) {
				Avance_Logger::error(' Mensaje muy corto: ' . strlen($mensaje) . ' caracteres');
				wp_send_json_error(['message' => 'El mensaje debe tener mínimo 10 caracteres.'], 400);
			}

			if (strlen($mensaje) > 5000) {
				Avance_Logger::error(' Mensaje muy largo: ' . strlen($mensaje) . ' caracteres');
				wp_send_json_error(['message' => 'El mensaje no puede exceder 5000 caracteres.'], 400);
			}

			Avance_Logger::info('Mensaje válido: ' . strlen($mensaje) . ' caracteres');
		}

		Avance_Logger::info('Validaciones contact completadas');
	}

	// ===================================================================
	// MENTORIA FORM - Validar: horario, plan, presupuesto
	// ===================================================================

	/**
	 * Validar formulario Mentoria
	 * Campos específicos: horario, plan, presupuesto
	 */
	public static function validate_mentoria($data) {
		Avance_Logger::debug('=== VALIDACIÓN CUSTOM: MENTORIA ===');

		// VALIDAR HORARIO
		$horarios_validos = ['9:00', '10:00', '11:00', '14:00', '15:00', '16:00'];

		if (empty($data['horario'])) {
			Avance_Logger::error(' Horario vacío');
			wp_send_json_error(['message' => 'El horario es requerido.'], 400);
		}

		if (!in_array($data['horario'], $horarios_validos)) {
			Avance_Logger::error(' Horario inválido: ' . $data['horario']);
			wp_send_json_error(['message' => 'El horario seleccionado no es válido.'], 400);
		}

		Avance_Logger::info('Horario válido: ' . $data['horario']);

		// VALIDAR PLAN
		$planes_validos = ['entrada', 'pro', 'premium'];

		if (empty($data['plan'])) {
			Avance_Logger::error(' Plan vacío');
			wp_send_json_error(['message' => 'El plan es requerido.'], 400);
		}

		if (!in_array($data['plan'], $planes_validos)) {
			Avance_Logger::error(' Plan inválido: ' . $data['plan']);
			wp_send_json_error(['message' => 'El plan seleccionado no es válido.'], 400);
		}

		Avance_Logger::info('Plan válido: ' . $data['plan']);

		// VALIDAR PRESUPUESTO
		if (empty($data['presupuesto'])) {
			Avance_Logger::error(' Presupuesto vacío');
			wp_send_json_error(['message' => 'El presupuesto es requerido.'], 400);
		}

		$presupuesto = floatval($data['presupuesto']);

		if ($presupuesto <= 0) {
			Avance_Logger::error(' Presupuesto inválido: ' . $presupuesto);
			wp_send_json_error(['message' => 'El presupuesto debe ser mayor a 0.'], 400);
		}

		if ($presupuesto > 1000000) {
			Avance_Logger::error(' Presupuesto demasiado alto: ' . $presupuesto);
			wp_send_json_error(['message' => 'El presupuesto no puede exceder $1,000,000.'], 400);
		}

		Avance_Logger::info('Presupuesto válido: ' . $presupuesto);
	}

	// ===================================================================
	// PROPUESTA FORM - Validar: DNI, empresa, descripción, presupuesto
	// ===================================================================

	/**
	 * Validar formulario Propuesta
	 * Campos específicos: DNI, empresa, descripción, presupuesto
	 */
	public static function validate_propuesta($data) {
		Avance_Logger::debug('=== VALIDACIÓN CUSTOM: PROPUESTA ===');

		// VALIDAR DNI (8 caracteres numéricos)
		if (empty($data['dni'])) {
			Avance_Logger::error(' DNI vacío');
			wp_send_json_error(['message' => 'El DNI es requerido.'], 400);
		}

		$dni = preg_replace('/[^0-9]/', '', $data['dni']);

		if (strlen($dni) < 8) {
			Avance_Logger::error(' DNI inválido: ' . $dni);
			wp_send_json_error(['message' => 'El DNI debe tener mínimo 8 dígitos.'], 400);
		}

		Avance_Logger::info('DNI válido: ' . $dni);

		// VALIDAR EMPRESA
		if (empty($data['empresa'])) {
			Avance_Logger::error(' Empresa vacía');
			wp_send_json_error(['message' => 'El nombre de la empresa es requerido.'], 400);
		}

		$empresa = trim($data['empresa']);

		if (strlen($empresa) < 3) {
			Avance_Logger::error(' Empresa muy corta: ' . $empresa);
			wp_send_json_error(['message' => 'El nombre de la empresa debe tener mínimo 3 caracteres.'], 400);
		}

		if (strlen($empresa) > 150) {
			Avance_Logger::error(' Empresa muy larga: ' . $empresa);
			wp_send_json_error(['message' => 'El nombre de la empresa no puede exceder 150 caracteres.'], 400);
		}

		Avance_Logger::info('Empresa válida: ' . $empresa);

		// VALIDAR DESCRIPCIÓN
		if (empty($data['descripcion'])) {
			Avance_Logger::error(' Descripción vacía');
			wp_send_json_error(['message' => 'La descripción del proyecto es requerida.'], 400);
		}

		$descripcion = trim($data['descripcion']);

		if (strlen($descripcion) < 20) {
			Avance_Logger::error(' Descripción muy corta: ' . strlen($descripcion));
			wp_send_json_error(['message' => 'La descripción debe tener mínimo 20 caracteres.'], 400);
		}

		if (strlen($descripcion) > 10000) {
			Avance_Logger::error(' Descripción muy larga: ' . strlen($descripcion));
			wp_send_json_error(['message' => 'La descripción no puede exceder 10000 caracteres.'], 400);
		}

		Avance_Logger::info('Descripción válida: ' . strlen($descripcion) . ' caracteres');

		// VALIDAR PRESUPUESTO
		if (empty($data['presupuesto'])) {
			Avance_Logger::error(' Presupuesto vacío');
			wp_send_json_error(['message' => 'El presupuesto es requerido.'], 400);
		}

		$presupuesto = floatval($data['presupuesto']);

		if ($presupuesto < 1000) {
			Avance_Logger::error(' Presupuesto mínimo no alcanzado: ' . $presupuesto);
			wp_send_json_error(['message' => 'El presupuesto mínimo es $1000.'], 400);
		}

		if ($presupuesto > 5000000) {
			Avance_Logger::error(' Presupuesto demasiado alto: ' . $presupuesto);
			wp_send_json_error(['message' => 'El presupuesto no puede exceder $5,000,000.'], 400);
		}

		Avance_Logger::info('Presupuesto válido: $' . number_format($presupuesto, 2));
	}

	// ===================================================================
	// NEWSLETTER FORM - Sin validaciones custom
	// (Solo campos globales: email)
	// ===================================================================

	/**
	 * Validar formulario Newsletter
	 * No tiene validaciones custom (email ya validado en global)
	 */
	public static function validate_newsletter($data) {
		Avance_Logger::debug('=== VALIDACIÓN CUSTOM: NEWSLETTER ===');
		error_log('Sin validaciones custom (email ya validado en global)');
	}

	// ===================================================================
	// BOOKING FORM - Validar: fecha, hora, tipo_sesión
	// ===================================================================

	/**
	 * Validar formulario Booking
	 * Campos específicos: fecha, hora, tipo_sesión
	 */
	public static function validate_booking($data) {
		Avance_Logger::debug('=== VALIDACIÓN CUSTOM: BOOKING ===');

		// VALIDAR FECHA (formato Y-m-d, no en el pasado)
		if (empty($data['fecha'])) {
			Avance_Logger::error(' Fecha vacía');
			wp_send_json_error(['message' => 'La fecha es requerida.'], 400);
		}

		// Validar formato de fecha
		$fecha = DateTime::createFromFormat('Y-m-d', $data['fecha']);
		if (!$fecha || $fecha->format('Y-m-d') !== $data['fecha']) {
			Avance_Logger::error(' Fecha en formato inválido: ' . $data['fecha']);
			wp_send_json_error(['message' => 'La fecha debe estar en formato YYYY-MM-DD.'], 400);
		}

		// Validar que no sea fecha pasada
		$ahora = new DateTime();
		if ($fecha < $ahora) {
			Avance_Logger::error(' Fecha en el pasado: ' . $data['fecha']);
			wp_send_json_error(['message' => 'No puedes agendar para fechas pasadas.'], 400);
		}

		// ISSUE 15: Validar límite máximo (no agendar demasiado lejos en el futuro)
		// Previene bots que agendan años en el futuro o fechas aleatorias
		$max_date = (clone $ahora)->modify('+' . AVANCE_MAX_BOOKING_DAYS . ' days');
		if ($fecha > $max_date) {
			$max_days = AVANCE_MAX_BOOKING_DAYS;
			error_log("Error: Fecha muy lejana (máximo {$max_days} días): " . $data['fecha']);
			wp_send_json_error([
				'message' => "No puedes agendar más de {$max_days} días en el futuro."
			], 400);
		}

		Avance_Logger::info('Fecha válida: ' . $data['fecha'] . ' (dentro de ' . AVANCE_MAX_BOOKING_DAYS . ' días)');

		// VALIDAR HORA
		$horas_validas = ['9:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'];

		if (empty($data['hora'])) {
			Avance_Logger::error(' Hora vacía');
			wp_send_json_error(['message' => 'La hora es requerida.'], 400);
		}

		if (!in_array($data['hora'], $horas_validas)) {
			Avance_Logger::error(' Hora inválida: ' . $data['hora']);
			wp_send_json_error(['message' => 'La hora seleccionada no es válida.'], 400);
		}

		Avance_Logger::info('Hora válida: ' . $data['hora']);

		// VALIDAR TIPO DE SESIÓN
		$tipos_sesion = ['video_call', 'presencial', 'hibrida'];

		if (empty($data['tipo_sesion'])) {
			Avance_Logger::error(' Tipo de sesión vacío');
			wp_send_json_error(['message' => 'El tipo de sesión es requerido.'], 400);
		}

		if (!in_array($data['tipo_sesion'], $tipos_sesion)) {
			Avance_Logger::error(' Tipo de sesión inválido: ' . $data['tipo_sesion']);
			wp_send_json_error(['message' => 'El tipo de sesión no es válido.'], 400);
		}

		Avance_Logger::info('Tipo de sesión válido: ' . $data['tipo_sesion']);
	}

	// ===================================================================
	// COTIZACIÓN FORM - Validar: tipo_servicio, detalles, presupuesto_aprox
	// ===================================================================

	/**
	 * Validar formulario Cotización
	 * Campos específicos: tipo_servicio, detalles, presupuesto_aprox
	 */
	public static function validate_cotizacion($data) {
		Avance_Logger::debug('=== VALIDACIÓN CUSTOM: COTIZACIÓN ===');

		// VALIDAR TIPO DE SERVICIO
		$servicios_validos = ['desarrollo', 'diseño', 'consultoría', 'mantenimiento', 'otro'];

		if (empty($data['tipo_servicio'])) {
			Avance_Logger::error(' Tipo de servicio vacío');
			wp_send_json_error(['message' => 'El tipo de servicio es requerido.'], 400);
		}

		if (!in_array($data['tipo_servicio'], $servicios_validos)) {
			Avance_Logger::error(' Tipo de servicio inválido: ' . $data['tipo_servicio']);
			wp_send_json_error(['message' => 'El tipo de servicio no es válido.'], 400);
		}

		Avance_Logger::info('Tipo de servicio válido: ' . $data['tipo_servicio']);

		// VALIDAR DETALLES
		if (empty($data['detalles'])) {
			Avance_Logger::error(' Detalles vacíos');
			wp_send_json_error(['message' => 'Los detalles del proyecto son requeridos.'], 400);
		}

		$detalles = trim($data['detalles']);

		if (strlen($detalles) < 15) {
			Avance_Logger::error(' Detalles muy cortos: ' . strlen($detalles));
			wp_send_json_error(['message' => 'Los detalles deben tener mínimo 15 caracteres.'], 400);
		}

		if (strlen($detalles) > 5000) {
			Avance_Logger::error(' Detalles muy largos: ' . strlen($detalles));
			wp_send_json_error(['message' => 'Los detalles no pueden exceder 5000 caracteres.'], 400);
		}

		Avance_Logger::info('Detalles válidos: ' . strlen($detalles) . ' caracteres');

		// VALIDAR PRESUPUESTO APROXIMADO (opcional pero si se envía, debe ser válido)
		if (!empty($data['presupuesto_aprox'])) {
			$presupuesto = floatval($data['presupuesto_aprox']);

			if ($presupuesto < 100) {
				Avance_Logger::error(' Presupuesto muy bajo: ' . $presupuesto);
				wp_send_json_error(['message' => 'El presupuesto mínimo es $100.'], 400);
			}

			if ($presupuesto > 10000000) {
				Avance_Logger::error(' Presupuesto demasiado alto: ' . $presupuesto);
				wp_send_json_error(['message' => 'El presupuesto no puede exceder $10,000,000.'], 400);
			}

			Avance_Logger::info('Presupuesto válido: $' . number_format($presupuesto, 2));
		}
	}
}

Avance_Logger::info('Form_Validators cargado exitosamente');
