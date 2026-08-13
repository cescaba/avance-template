<?php
/**
 * Agendamiento Handler
 *
 * Validación y procesamiento de agendamientos
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Agendamiento_Handler {

	/**
	 * Validar datos del agendamiento
	 *
	 * @param array $data Datos del formulario
	 * @return array Array con ['success' => bool, 'errors' => array, 'data' => array]
	 */
	public static function validate($data) {
		$errors = array();
		$validated_data = array();

		// Validar nombre
		if (empty($data['nombre']) || !is_string($data['nombre'])) {
			$errors[] = 'El nombre es requerido';
		} else {
			$validated_data['nombre'] = sanitize_text_field($data['nombre']);
			if (strlen($validated_data['nombre']) < 3) {
				$errors[] = 'El nombre debe tener al menos 3 caracteres';
			}
		}

		// Validar número de teléfono
		if (empty($data['numero']) || !is_string($data['numero'])) {
			$errors[] = 'El número de teléfono es requerido';
		} else {
			$validated_data['numero'] = sanitize_text_field($data['numero']);
			if (!preg_match('/^[+\d\s\-\(\)]+$/', $validated_data['numero'])) {
				$errors[] = 'El número de teléfono tiene un formato inválido';
			}
		}

		// Validar tema
		if (empty($data['tema']) || !is_string($data['tema'])) {
			$errors[] = 'El tema es requerido';
		} else {
			$validated_data['tema'] = sanitize_text_field($data['tema']);
		}

		// Validar fecha
		if (empty($data['fecha']) || !is_string($data['fecha'])) {
			$errors[] = 'La fecha es requerida';
		} else {
			$validated_data['fecha_agendada'] = sanitize_text_field($data['fecha']);
			// Validar formato de fecha (YYYY-M-D)
			if (!self::is_valid_date($validated_data['fecha_agendada'])) {
				$errors[] = 'La fecha tiene un formato inválido';
			}
		}

		return array(
			'success' => empty($errors),
			'errors' => $errors,
			'data' => $validated_data,
		);
	}

	/**
	 * Verificar si la fecha es válida
	 *
	 * @param string $date Fecha en formato YYYY-M-D
	 * @return bool
	 */
	private static function is_valid_date($date) {
		$parts = explode('-', $date);
		if (count($parts) !== 3) {
			return false;
		}

		$year = (int) $parts[0];
		$month = (int) $parts[1];
		$day = (int) $parts[2];

		return checkdate($month, $day, $year);
	}

	/**
	 * Procesar agendamiento
	 *
	 * @param array $data Datos validados
	 * @return array Array con ['success' => bool, 'message' => string, 'id' => int]
	 */
	public static function process($data) {
		// Guardar en la BD
		$agendamiento_id = Avance_Agendamiento_DB::save_agendamiento($data);

		if (!$agendamiento_id) {
			return array(
				'success' => false,
				'message' => 'Error al guardar el agendamiento',
				'id' => null,
			);
		}

		// Cambiar estado a confirmado
		Avance_Agendamiento_DB::update_status($agendamiento_id, 'confirmado');

		return array(
			'success' => true,
			'message' => 'Agendamiento guardado exitosamente',
			'id' => $agendamiento_id,
		);
	}
}
