<?php
/**
 * Proposal Handler
 *
 * Validación, seguridad y procesamiento de solicitudes de propuestas
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Proposal_Handler {

	/**
	 * Servicios válidos permitidos
	 */
	private static $valid_services = array(
		'Coaching en Ventas',
		'Story Selling',
		'Gestión Estratégica de Equipos Online',
		'Técnicas para Presentaciones de Ventas',
		'Otro'
	);

	/**
	 * Validar datos de la propuesta
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
			if (strlen($validated_data['nombre']) > 255) {
				$errors[] = 'El nombre es demasiado largo';
			}
		}

		// Validar cargo (opcional)
		if (!empty($data['cargo'])) {
			$validated_data['cargo'] = sanitize_text_field($data['cargo']);
			if (strlen($validated_data['cargo']) > 255) {
				$errors[] = 'El cargo es demasiado largo';
			}
		} else {
			$validated_data['cargo'] = '';
		}

		// Validar empresa
		if (empty($data['empresa']) || !is_string($data['empresa'])) {
			$errors[] = 'El nombre de la empresa es requerido';
		} else {
			$validated_data['empresa'] = sanitize_text_field($data['empresa']);
			if (strlen($validated_data['empresa']) < 3) {
				$errors[] = 'El nombre de la empresa debe tener al menos 3 caracteres';
			}
			if (strlen($validated_data['empresa']) > 255) {
				$errors[] = 'El nombre de la empresa es demasiado largo';
			}
		}

		// Validar tamaño equipo (opcional)
		if (!empty($data['tamaño_equipo'])) {
			$validated_data['tamaño_equipo'] = sanitize_text_field($data['tamaño_equipo']);
			if (strlen($validated_data['tamaño_equipo']) > 100) {
				$errors[] = 'El tamaño del equipo es demasiado largo';
			}
		} else {
			$validated_data['tamaño_equipo'] = '';
		}

		// Validar email
		if (empty($data['email']) || !is_string($data['email'])) {
			$errors[] = 'El email es requerido';
		} else {
			$validated_data['email'] = sanitize_email($data['email']);
			if (!is_email($validated_data['email'])) {
				$errors[] = 'El email no tiene un formato válido';
			}
		}

		// Validar WhatsApp (opcional)
		if (!empty($data['whatsapp'])) {
			$validated_data['whatsapp'] = sanitize_text_field($data['whatsapp']);
			if (!preg_match('/^[+\d\s\-\(\)]+$/', $validated_data['whatsapp'])) {
				$errors[] = 'El número de teléfono tiene un formato inválido';
			}
		} else {
			$validated_data['whatsapp'] = '';
		}

		// Validar servicio de interés
		if (empty($data['servicio_interes']) || !is_string($data['servicio_interes'])) {
			$errors[] = 'El servicio de interés es requerido';
		} else {
			$validated_data['servicio_interes'] = sanitize_text_field($data['servicio_interes']);
			if (!in_array($validated_data['servicio_interes'], self::$valid_services)) {
				$errors[] = 'El servicio seleccionado no es válido';
			}
		}

		// Validar desafío comercial
		if (empty($data['desafio_comercial']) || !is_string($data['desafio_comercial'])) {
			$errors[] = 'El desafío comercial es requerido';
		} else {
			$validated_data['desafio_comercial'] = sanitize_textarea_field($data['desafio_comercial']);
			if (strlen($validated_data['desafio_comercial']) < 10) {
				$errors[] = 'Por favor describe tu desafío con más detalle (mínimo 10 caracteres)';
			}
			if (strlen($validated_data['desafio_comercial']) > 2000) {
				$errors[] = 'El desafío comercial es demasiado largo (máximo 2000 caracteres)';
			}
		}

		return array(
			'success' => empty($errors),
			'errors' => $errors,
			'data' => $validated_data,
		);
	}

	/**
	 * Procesar propuesta
	 *
	 * @param array $data Datos validados
	 * @return array Array con ['success' => bool, 'message' => string, 'id' => int]
	 */
	public static function process($data) {
		// Guardar en la BD
		$proposal_id = Avance_Proposal_DB::save_proposal($data);

		if (!$proposal_id) {
			return array(
				'success' => false,
				'message' => 'Error al guardar la propuesta',
				'id' => null,
			);
		}

		// Cambiar estado a confirmado
		Avance_Proposal_DB::update_status($proposal_id, 'confirmado');

		return array(
			'success' => true,
			'message' => 'Propuesta guardada exitosamente',
			'id' => $proposal_id,
		);
	}
}
