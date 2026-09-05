<?php
/**
 * Servicio Empresa Form Handler
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

require_once get_template_directory() . '/includes/security/handlers/class-global-form-handler.php';
require_once get_template_directory() . '/includes/database/servicios/class-servicio-empresa-db.php';

class Avance_Servicio_Empresa_Handler {
	public function process() {
		// Verificar nonce
		$nonce = $_POST['nonce'] ?? '';
		if (!wp_verify_nonce($nonce, 'form_servicio_empresa')) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => 'Nonce inválido']]);
			wp_die();
		}

		// Recolectar datos crudos del POST
		$data = array(
			'nonce' => $nonce,
			'nombre' => trim($_POST['nombre'] ?? ''),
			'cargo' => trim($_POST['cargo'] ?? ''),
			'empresa' => trim($_POST['empresa'] ?? ''),
			'tamaño_equipo' => trim($_POST['tamaño_equipo'] ?? ''),
			'email' => trim($_POST['email'] ?? ''),
			'whatsapp' => trim($_POST['whatsapp'] ?? ''),
			'servicio_interes' => trim($_POST['servicio_interes'] ?? ''),
			'desafio_comercial' => trim($_POST['desafio_comercial'] ?? ''),
		);

		// Validar datos básicos
		if (strlen($data['nombre']) < 3 || strlen($data['nombre']) > 100) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => 'Nombre inválido (3-100 caracteres)']]);
			wp_die();
		}

		if (!is_email($data['email'])) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => 'Email inválido']]);
			wp_die();
		}

		if (strlen($data['empresa']) < 2) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => 'Empresa inválida']]);
			wp_die();
		}

		if (strlen($data['servicio_interes']) < 3) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => 'Servicio de interés inválido']]);
			wp_die();
		}

		// Aplicar seguridad global (rate limiting, spam detection, duplicate check)
		Global_Form_Handler::validate_security('servicio_empresa', $data);

		// Guardar en BD
		Avance_Servicio_Empresa_DB::init();
		$insert_id = Avance_Servicio_Empresa_DB::insert($data);

		if (!$insert_id) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => 'Error al guardar los datos']]);
			wp_die();
		}

		// Construir mensaje para WhatsApp
		$mensaje = "*[NUEVA SOLICITUD DE SERVICIO]*\n\n";
		$mensaje .= "*Nombre:* " . $data['nombre'] . "\n";
		$mensaje .= "*Cargo:* " . ($data['cargo'] ?: '—') . "\n";
		$mensaje .= "*Empresa:* " . $data['empresa'] . "\n";
		$mensaje .= "*Tamaño Equipo:* " . ($data['tamaño_equipo'] ?: '—') . "\n";
		$mensaje .= "*Email:* " . $data['email'] . "\n";
		$mensaje .= "*WhatsApp:* " . $data['whatsapp'] . "\n";
		$mensaje .= "\n═════════════════════════\n";
		$mensaje .= "*SOLICITUD*\n";
		$mensaje .= "═════════════════════════\n\n";
		$mensaje .= "*Servicio de Interés:*\n" . $data['servicio_interes'] . "\n\n";
		$mensaje .= "*Desafío Comercial:*\n" . $data['desafio_comercial'] . "\n";

		// Normalizar número de WhatsApp del usuario
		$whatsapp_normalizado = preg_replace('/[^0-9]/', '', $data['whatsapp']);
		if (strlen($whatsapp_normalizado) === 9) {
			// Si es 9 dígitos (sin código país), agregar 51
			$whatsapp_normalizado = '51' . $whatsapp_normalizado;
		} elseif (strlen($whatsapp_normalizado) === 10 && substr($whatsapp_normalizado, 0, 1) !== '5') {
			// Si es 10 dígitos pero no empieza con 5, agregar 51
			$whatsapp_normalizado = '51' . $whatsapp_normalizado;
		}

		// URL de WhatsApp dirigido al número del usuario
		$url = 'https://wa.me/' . $whatsapp_normalizado . '?text=' . urlencode($mensaje);

		// Respuesta exitosa
		header('Content-Type: application/json');
		echo json_encode(['success' => true, 'data' => ['url' => $url]]);
		wp_die();
	}
}
