<?php
if (!defined('ABSPATH')) exit;

class Avance_Diagnostico_Handler {
	public function process() {
		// Nonce rápido
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'form_diagnostico')) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => 'Nonce inválido']]);
			wp_die();
		}

		$nombre = trim($_POST['nombreCompleto'] ?? '');
		$email = trim($_POST['email'] ?? '');
		$whatsapp = trim($_POST['whatsapp'] ?? '');
		$respuestas = (array)($_POST['respuestas'] ?? []);

		// Validar
		if (strlen($nombre) < 3) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => 'Nombre inválido']]);
			wp_die();
		}

		if (!is_email($email)) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => 'Email inválido']]);
			wp_die();
		}

		if (count($respuestas) < 4) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => 'Completa el quiz']]);
			wp_die();
		}

		// Guardar en BD
		global $wpdb;
		$wpdb->insert($wpdb->prefix . 'avance_diagnosticos', [
			'nombre_completo' => $nombre,
			'email'           => $email,
			'whatsapp'        => $whatsapp,
			'respuestas'      => wp_json_encode($respuestas),
		], ['%s', '%s', '%s', '%s']);

		// Construir mensaje COMPLETO para WhatsApp
		$preguntas = [
			1 => '¿Cuál es tu mayor desafío comercial ahora mismo?',
			2 => '¿Cuántos vendedores tiene tu equipo actualmente?',
			3 => '¿Tienes definida tu propuesta de valor?',
			4 => '¿Cuánto inviertes en formación comercial?'
		];

		$mensaje = "*[NUEVO DIAGNÓSTICO COMPLETADO]*\n\n";
		$mensaje .= "*Nombre:* $nombre\n";
		$mensaje .= "*Email:* $email\n";
		$mensaje .= "*WhatsApp:* $whatsapp\n";
		$mensaje .= "\n═════════════════════════\n";
		$mensaje .= "*RESPUESTAS DEL DIAGNÓSTICO*\n";
		$mensaje .= "═════════════════════════\n\n";

		foreach ($respuestas as $idx => $respuesta) {
			$num = $idx + 1;
			$mensaje .= "*P$num:* " . ($preguntas[$num] ?? '') . "\n";
			$mensaje .= "→ $respuesta\n\n";
		}

		// WhatsApp URL con mensaje (CENTRALIZADO - usa AVANCE_WHATSAPP)
		$url = avance_get_whatsapp_link($mensaje);

		// Respuesta instantánea
		header('Content-Type: application/json');
		echo json_encode(['success' => true, 'data' => ['url' => $url]]);
		wp_die();
	}
}
