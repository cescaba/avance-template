<?php
/**
 * WhatsApp Service
 * Genera URLs de WhatsApp y mensajes formateados
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_WhatsApp_Service {
	private $phone_number = '+51936975214';
	private $questions = array(
		1 => '¿Cuál es tu mayor desafío comercial ahora mismo?',
		2 => '¿Cuántos vendedores tiene tu equipo actualmente?',
		3 => '¿Qué tan clara es tu estrategia comercial?',
		4 => '¿Cuánto inviertes en formación comercial?',
	);

	public function generate_message($data) {
		$message = $this->build_message($data);
		return $this->encode_message($message);
	}

	private function build_message($data) {
		$nombre = sanitize_text_field($data['nombreCompleto']);
		$email = sanitize_email($data['email']);
		$respuestas = is_array($data['respuestas']) ? array_values($data['respuestas']) : array();

		// Usar texto simple en lugar de emojis para mejor compatibilidad
		$message = "*[NUEVO DIAGNOSTICO COMPLETADO]*\n\n";
		$message .= "*Nombre:* $nombre\n";
		$message .= "*Email:* $email\n";
		$message .= "\n";
		$message .= "═════════════════════════\n";
		$message .= "*RESPUESTAS DEL DIAGNOSTICO*\n";
		$message .= "═════════════════════════\n\n";

		foreach ($respuestas as $index => $respuesta) {
			$numero_pregunta = $index + 1;
			$pregunta = $this->questions[$numero_pregunta] ?? '';

			if (!empty($pregunta)) {
				$message .= "*P$numero_pregunta:* $pregunta\n";
				// Asegurar que respuesta es string
				$respuesta_texto = is_string($respuesta) ? $respuesta : '';
				$message .= "→ $respuesta_texto\n\n";
			}
		}

		$message .= "═════════════════════════\n";

		return $message;
	}

	private function encode_message($message) {
		return urlencode($message);
	}

	public function generate_whatsapp_url($encoded_message) {
		// Formatear número para WhatsApp (sin +, solo dígitos)
		$phone = preg_replace('/[^0-9]/', '', $this->phone_number);
		// Asegurar que tiene el formato correcto para WhatsApp API
		if (substr($phone, 0, 1) === '0') {
			$phone = substr($phone, 1);
		}
		return "https://wa.me/$phone?text=$encoded_message";
	}

	public function get_phone_number() {
		return $this->phone_number;
	}
}
