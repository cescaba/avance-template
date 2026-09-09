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

		if (strlen($data['desafio_comercial']) < 10) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => 'Describe tu desafío comercial con más detalle (mínimo 10 caracteres)']]);
			wp_die();
		}

		if (!empty($data['whatsapp']) && strlen(preg_replace('/[^0-9]/', '', $data['whatsapp'])) < 9) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => 'WhatsApp no tiene suficientes dígitos']]);
			wp_die();
		}

		// Aplicar seguridad global (rate limiting, spam detection, duplicate check)
		Global_Form_Handler::validate_security('servicio_empresa', $data);

		// Guardar en BD
		Avance_Servicio_Empresa_DB::init();
		$insert_id = Avance_Servicio_Empresa_DB::insert($data);

		if (!$insert_id) {
			global $wpdb;
			$error_message = 'No se pudo guardar tu solicitud. Por favor intenta de nuevo.';

			// Log del error para debugging en producción
			$last_error = $wpdb->last_error ?: 'Unknown error';
			error_log('Avance_Servicio_Empresa_Handler::insert error - ' . $last_error);

			// Detectar errores específicos
			if (!empty($last_error)) {
				if (stripos($last_error, 'Duplicate') !== false || stripos($last_error, 'UNIQUE') !== false) {
					if (stripos($last_error, 'email') !== false) {
						$error_message = 'Este email ya fue registrado. Usa otro email o intenta con otro nombre de empresa.';
					} else {
						$error_message = 'Estos datos ya fueron registrados. Por favor usa información diferente.';
					}
				} elseif (stripos($last_error, 'Incorrect') !== false || stripos($last_error, 'column') !== false) {
					$error_message = 'Error en los datos. Por favor verifica que todos los campos sean válidos.';
				} elseif (stripos($last_error, 'connection') !== false || stripos($last_error, 'timeout') !== false) {
					$error_message = 'Error de conexión con la base de datos. Intenta de nuevo en un momento.';
				}
			}

			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => $error_message]]);
			wp_die();
		}

		// Construir mensaje para WhatsApp
		$mensaje = "Hola, buenos días.\n";
		$mensaje .= "He completado el formulario de solicitud de propuesta y les comparto mis datos:\n\n";
		$mensaje .= "Nombre: " . $data['nombre'] . "\n";
		if ($data['cargo']) {
			$mensaje .= "Cargo: " . $data['cargo'] . "\n";
		}
		$mensaje .= "Empresa: " . $data['empresa'] . "\n";
		if ($data['tamaño_equipo']) {
			$mensaje .= "Tamaño Equipo: " . $data['tamaño_equipo'] . "\n";
		}
		$mensaje .= "Email: " . $data['email'] . "\n";
		if ($data['whatsapp']) {
			$mensaje .= "WhatsApp: " . $data['whatsapp'] . "\n";
		}
		$mensaje .= "\nServicio de Interés:\n" . $data['servicio_interes'] . "\n\n";
		$mensaje .= "Desafío Comercial:\n" . $data['desafio_comercial'] . "\n\n";
		$mensaje .= "Quedo atento a sus comentarios.";

		// Normalizar número de WhatsApp del admin (AVANCE_WHATSAPP_OWNER)
		$owner_phone = defined('AVANCE_WHATSAPP_OWNER') ? AVANCE_WHATSAPP_OWNER : '';
		if (empty($owner_phone)) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'data' => ['message' => 'Configuración de WhatsApp no disponible']]);
			wp_die();
		}

		$whatsapp_normalizado = preg_replace('/[^0-9]/', '', $owner_phone);
		if (strlen($whatsapp_normalizado) === 9) {
			$whatsapp_normalizado = '51' . $whatsapp_normalizado;
		} elseif (strlen($whatsapp_normalizado) === 10 && substr($whatsapp_normalizado, 0, 1) !== '5') {
			$whatsapp_normalizado = '51' . $whatsapp_normalizado;
		}

		// URL de WhatsApp dirigido al número del admin
		$url = 'https://wa.me/' . $whatsapp_normalizado . '?text=' . urlencode($mensaje);

		// Respuesta exitosa
		header('Content-Type: application/json');
		echo json_encode(['success' => true, 'data' => ['url' => $url]]);
		wp_die();
	}
}
