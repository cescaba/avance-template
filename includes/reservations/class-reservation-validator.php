<?php
/**
 * Reservation Validator
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Reservation_Validator {

	/**
	 * Validar datos de reserva
	 */
	public static function validate($data) {
		$errors = array();

		// Validar producto
		if (empty($data['product_id'])) {
			$errors[] = 'Debe seleccionar un tipo de mentoría';
		} else {
			$product = wc_get_product($data['product_id']);
			if (!$product || !$product->exists()) {
				$errors[] = 'El producto seleccionado no existe';
			}
		}

		// Validar fecha
		if (empty($data['fecha_reserva'])) {
			$errors[] = 'Debe seleccionar una fecha';
		} elseif (!self::is_valid_date($data['fecha_reserva'])) {
			$errors[] = 'La fecha no es válida';
		} elseif (!self::is_future_date($data['fecha_reserva'])) {
			$errors[] = 'La fecha debe ser futura';
		}

		// Validar nombre
		if (empty($data['nombre'])) {
			$errors[] = 'El nombre es requerido';
		} elseif (strlen(trim($data['nombre'])) < 3) {
			$errors[] = 'El nombre debe tener al menos 3 caracteres';
		} elseif (strlen(trim($data['nombre'])) > 100) {
			$errors[] = 'El nombre es demasiado largo';
		}

		// Validar email
		if (empty($data['email'])) {
			$errors[] = 'El email es requerido';
		} elseif (!is_email($data['email'])) {
			$errors[] = 'El email no es válido';
		}

		// Validar WhatsApp
		if (empty($data['whatsapp'])) {
			$errors[] = 'El WhatsApp es requerido';
		} else {
			// Remover espacios, guiones, paréntesis para validar
			$whatsapp_clean = preg_replace('/[\s\-\(\)]/', '', $data['whatsapp']);
			if (!preg_match('/^\+?[0-9]{10,20}$/', $whatsapp_clean)) {
				$errors[] = 'El número de WhatsApp no es válido (mínimo 10 dígitos)';
			}
		}

		if (!empty($errors)) {
			return array(
				'valid' => false,
				'errors' => $errors,
			);
		}

		return array('valid' => true);
	}

	/**
	 * Verificar si la fecha es válida
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
			'product_id' => intval($data['product_id'] ?? 0),
			'fecha_reserva' => sanitize_text_field($data['fecha_reserva'] ?? ''),
			'nombre' => sanitize_text_field($data['nombre'] ?? ''),
			'email' => sanitize_email($data['email'] ?? ''),
			// Remover espacios, guiones, paréntesis del WhatsApp, mantener solo números y +
			'whatsapp' => preg_replace('/[\s\-\(\)]/', '', preg_replace('/[^0-9\+\-\(\)\s]/', '', $data['whatsapp'] ?? '')),
			'notas' => sanitize_textarea_field($data['notas'] ?? ''),
		);
	}
}
