<?php
/**
 * PDF Download Validator - Helper utilities
 *
 * Nota: Las validaciones principales están en el handler
 * Esta clase mantiene helper methods para futuras extensiones
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_PDF_Download_Validator {

	/**
	 * Validar formato de email
	 */
	public static function is_valid_email($email) {
		return is_email($email);
	}

	/**
	 * Validar formato de teléfono (9-15 dígitos)
	 */
	public static function is_valid_phone($phone) {
		if (empty($phone)) {
			return true;
		}
		$digits_only = preg_replace('/[^0-9]/', '', $phone);
		return strlen($digits_only) >= 9 && strlen($digits_only) <= 15;
	}

	/**
	 * Validar nombre (3-100 caracteres)
	 */
	public static function is_valid_name($name) {
		$length = strlen(trim($name));
		return $length >= 3 && $length <= 100;
	}

	/**
	 * Validar número de empleados
	 */
	public static function is_valid_empleados($empleados) {
		$valid = array('1-10', '11-50', '51-200', '201-1000', '1000+');
		return in_array($empleados, $valid, true);
	}

	/**
	 * Validar industria
	 */
	public static function is_valid_industria($industria) {
		$valid = array('tecnologia', 'retail', 'finanzas', 'salud', 'manufactura', 'educacion', 'otro');
		return in_array($industria, $valid, true);
	}
}
