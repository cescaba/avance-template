<?php
/**
 * Diagnostico Handler
 * Procesa la solicitud AJAX del formulario de diagnóstico
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Diagnostico_Handler {
	public function process() {
		// Verificar nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'avance_diagnostico_nonce')) {
			wp_send_json_error(array('message' => 'Verificación de seguridad fallida'));
			wp_die();
		}

		// Recopilar datos
		$data = array(
			'nombreCompleto' => $_POST['nombreCompleto'] ?? '',
			'email'          => $_POST['email'] ?? '',
			'whatsapp'       => $_POST['whatsapp'] ?? '',
			'respuestas'     => isset($_POST['respuestas']) ? array_values((array)$_POST['respuestas']) : array(),
		);

		// Validar
		$validator = new Avance_Diagnostico_Validator();
		if (!$validator->validate($data)) {
			wp_send_json_error(array('message' => $validator->get_error_message()));
			wp_die();
		}

		// Guardar en BD
		$table = new Avance_Diagnostico_Table();
		$inserted_id = $table->insert_diagnostico($data);

		if (!$inserted_id) {
			wp_send_json_error(array('message' => 'Error al guardar los datos'));
			wp_die();
		}

		// Generar mensaje y URL de WhatsApp
		$whatsapp_service = new Avance_WhatsApp_Service();
		$encoded_message = $whatsapp_service->generate_message($data);
		$whatsapp_url = $whatsapp_service->generate_whatsapp_url($encoded_message);

		// Respuesta exitosa
		wp_send_json_success(array(
			'message' => 'Diagnóstico guardado correctamente',
			'url'     => $whatsapp_url,
		));

		wp_die();
	}
}
