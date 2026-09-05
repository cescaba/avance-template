<?php
/**
 * Diagnostico Validator
 * Valida datos del formulario de diagnóstico
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Diagnostico_Validator {
	private $errors = array();

	public function validate($data) {
		$this->errors = array();

		$this->validate_nombre($data['nombre_completo'] ?? '');
		$this->validate_email($data['email'] ?? '');
		$this->validate_whatsapp($data['whatsapp'] ?? '');
		$this->validate_respuestas($data['respuestas'] ?? array());

		return empty($this->errors);
	}

	private function validate_nombre($nombre) {
		if (empty($nombre)) {
			$this->errors[] = 'El nombre completo es requerido';
			return;
		}

		$len = strlen($nombre);
		if ($len < 3 || $len > 100) {
			$this->errors[] = 'El nombre debe tener entre 3 y 100 caracteres';
		}
	}

	private function validate_email($email) {
		if (empty($email)) {
			$this->errors[] = 'El email es requerido';
			return;
		}

		if (!is_email($email)) {
			$this->errors[] = 'El email no es válido';
		}
	}

	private function validate_whatsapp($whatsapp) {
		if (empty($whatsapp)) {
			$this->errors[] = 'El WhatsApp es requerido';
			return;
		}

		$digits_only = preg_replace('/[^0-9]/', '', $whatsapp);
		if (strlen($digits_only) < 9 || strlen($digits_only) > 15) {
			$this->errors[] = 'El número de WhatsApp no es válido';
		}
	}

	private function validate_respuestas($respuestas) {
		if (!is_array($respuestas) || count($respuestas) < 4) {
			$this->errors[] = 'Debes completar todas las preguntas del quiz';
		}
	}

	public function get_errors() {
		return $this->errors;
	}

	public function get_error_message() {
		return implode(', ', $this->errors);
	}
}
