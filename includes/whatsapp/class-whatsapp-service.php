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
	private $phone_number = '+51993508652';
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
		$nombre = $data['nombre_completo'];
		$email = $data['email'];
		$respuestas = (array)($data['respuestas'] ?? []);

		// Usar texto simple en lugar de emojis para mejor compatibilidad
		$message = "*[NUEVO DIAGNOSTICO COMPLETADO]*\n\n";
		$message .= "*Nombre:* $nombre\n";
		$message .= "*Email:* $email\n";
		$message .= "\n";
		$message .= "═════════════════════════\n";
		$message .= "*RESPUESTAS DEL DIAGNOSTICO*\n";
		$message .= "═════════════════════════\n\n";

		foreach ($respuestas as $index => $respuesta) {
			$pregunta = $this->questions[$index + 1] ?? '';
			if ($pregunta) {
				$message .= "*P" . ($index + 1) . ":* $pregunta\n";
				$message .= "→ $respuesta\n\n";
			}
		}

		$message .= "═════════════════════════\n";

		return $message;
	}

	/**
	 * Encodificar mensaje para URL (ya hace rawurlencode internamente)
	 */
	private function encode_message($message) {
		// Ya viene formateado, solo retornar
		return $message;
	}

	public function generate_whatsapp_url($encoded_message) {
		$phone = preg_replace('/[^0-9]/', '', $this->phone_number);
		if (strlen($phone) === 9) {
			$phone = '51' . $phone;
		}
		if ($phone[0] === '0') {
			$phone = substr($phone, 1);
		}
		return "https://wa.me/$phone?text=" . rawurlencode($encoded_message);
	}

	public function get_phone_number() {
		return $this->phone_number;
	}
}
