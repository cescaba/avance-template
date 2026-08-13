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

		$this->validate_nombre($data['nombreCompleto'] ?? '');
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

		if (strlen($nombre) < 3) {
			$this->errors[] = 'El nombre debe tener al menos 3 caracteres';
			return;
		}

		if (strlen($nombre) > 255) {
			$this->errors[] = 'El nombre es muy largo';
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

		// Aceptar formato con o sin +51
		$cleaned = preg_replace('/[^0-9+]/', '', $whatsapp);

		if (!preg_match('/^\+?\d{6,15}$/', $cleaned)) {
			$this->errors[] = 'El número de WhatsApp no es válido';
		}
	}

	private function validate_respuestas($respuestas) {
		if (empty($respuestas) || !is_array($respuestas)) {
			$this->errors[] = 'Las respuestas son requeridas';
			return;
		}

		if (count($respuestas) < 4) {
			$this->errors[] = 'Debe completar todas las preguntas';
		}
	}

	public function get_errors() {
		return $this->errors;
	}

	public function get_error_message() {
		return implode(', ', $this->errors);
	}
}
