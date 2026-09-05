<?php
if (!defined('ABSPATH')) exit;

class Avance_Diagnostico_Handler {
	public function process() {
		$post_data = $this->get_form_data($_POST);

		$nombre_partes = explode(' ', trim($post_data['nombreCompleto']), 2);
		$data_for_global = [
			'nombre' => $nombre_partes[0] ?? '',
			'apellido' => $nombre_partes[1] ?? $nombre_partes[0] ?? '',
			'email' => $post_data['email'] ?? '',
			'whatsapp' => $post_data['whatsapp'] ?? '',
			'nonce' => $post_data['nonce'] ?? '',
		];

		$validated = Global_Form_Handler::process('diagnostico', $data_for_global);

		$respuestas = (array)($post_data['respuestas'] ?? []);
		if (count($respuestas) < 4) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => 'Completa el quiz']]);
			wp_die();
		}

		global $wpdb;
		$wpdb->insert($wpdb->prefix . 'avance_diagnosticos', [
			'nombre_completo' => $validated['nombre'] . ' ' . $validated['apellido'],
			'email'           => $validated['email'],
			'whatsapp'        => $validated['whatsapp'],
			'respuestas'      => wp_json_encode($respuestas),
		], ['%s', '%s', '%s', '%s']);

		Avance_Rate_Limiter::log_attempt(
			Avance_Rate_Limiter::get_client_ip(),
			$validated['email'],
			'success'
		);
		$preguntas = [
			1 => '¿Cuál es tu mayor desafío comercial ahora mismo?',
			2 => '¿Cuántos vendedores tiene tu equipo actualmente?',
			3 => '¿Tienes definida tu propuesta de valor?',
			4 => '¿Cuánto inviertes en formación comercial?'
		];

		$nombre_completo = $validated['nombre'] . ' ' . $validated['apellido'];
		$mensaje = "*[NUEVO DIAGNÓSTICO COMPLETADO]*\n\n";
		$mensaje .= "*Nombre:* " . $nombre_completo . "\n";
		$mensaje .= "*Email:* " . $validated['email'] . "\n";
		$mensaje .= "*WhatsApp:* " . $validated['whatsapp'] . "\n";
		$mensaje .= "\n═════════════════════════\n";
		$mensaje .= "*RESPUESTAS DEL DIAGNÓSTICO*\n";
		$mensaje .= "═════════════════════════\n\n";

		foreach ($respuestas as $idx => $respuesta) {
			$num = $idx + 1;
			$mensaje .= "*P$num:* " . ($preguntas[$num] ?? '') . "\n";
			$mensaje .= "→ $respuesta\n\n";
		}

		$url = avance_get_whatsapp_link($mensaje);
		header('Content-Type: application/json');
		echo json_encode(['success' => true, 'data' => ['url' => $url]]);
		wp_die();
	}

	private function get_form_data($post_data) {
		return [
			'nombreCompleto' => $post_data['nombreCompleto'] ?? '',
			'email' => $post_data['email'] ?? '',
			'whatsapp' => $post_data['whatsapp'] ?? '',
			'respuestas' => $post_data['respuestas'] ?? [],
			'nonce' => $post_data['nonce'] ?? '',
		];
	}
}
