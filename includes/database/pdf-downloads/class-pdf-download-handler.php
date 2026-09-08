<?php
/**
 * PDF Download Handler
 * Validación independiente siguiendo patrón de agendamiento
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_PDF_Download_Handler {

	public function __construct() {
		add_action('wp_ajax_nopriv_avance_submit_pdf_download', array($this, 'handle_ajax_submit'));
		add_action('wp_ajax_avance_submit_pdf_download', array($this, 'handle_ajax_submit'));
		add_action('wp_ajax_nopriv_avance_download_pdf', array($this, 'handle_pdf_download'));
		add_action('wp_ajax_avance_download_pdf', array($this, 'handle_pdf_download'));
	}

	public function handle_ajax_submit() {
		// 0. ASEGURAR QUE LA TABLA EXISTS (on-demand creation)
		if (!Avance_PDF_Download_DB::table_exists()) {
			Avance_PDF_Download_DB::ensure_table_exists();
		}

		$post_data = array(
			'nombre' => $_POST['nombre'] ?? '',
			'email' => $_POST['email'] ?? '',
			'telefono' => $_POST['telefono'] ?? '',
			'empresa' => $_POST['empresa'] ?? '',
			'empleados' => $_POST['empleados'] ?? '',
			'industria' => $_POST['industria'] ?? '',
			'nonce' => $_POST['nonce'] ?? '',
		);

		// 1. VALIDAR NONCE CSRF
		if (!isset($post_data['nonce']) || !wp_verify_nonce($post_data['nonce'], 'form_pdf_download')) {
			wp_send_json_error(['message' => 'Error de seguridad: token inválido.'], 400);
		}

		// 2. VALIDAR NOMBRE
		$nombre = trim($post_data['nombre'] ?? '');
		if (empty($nombre)) {
			wp_send_json_error(['message' => 'El nombre es requerido.'], 400);
		}
		if (strlen($nombre) < 3) {
			wp_send_json_error(['message' => 'El nombre debe tener mínimo 3 caracteres.'], 400);
		}
		if (strlen($nombre) > 100) {
			wp_send_json_error(['message' => 'El nombre no puede exceder 100 caracteres.'], 400);
		}
		$nombre = sanitize_text_field($nombre);

		// 3. VALIDAR EMAIL
		$email = trim($post_data['email'] ?? '');
		if (empty($email)) {
			wp_send_json_error(['message' => 'El correo electrónico es requerido.'], 400);
		}
		if (!is_email($email)) {
			wp_send_json_error(['message' => 'El correo electrónico no es válido.'], 400);
		}
		if (Avance_PDF_Download_DB::email_exists($email)) {
			wp_send_json_error(['message' => 'Este correo electrónico ya ha descargado el PDF.'], 400);
		}
		$email = sanitize_email($email);

		// 4. VALIDAR TELEFONO
		$telefono = trim($post_data['telefono'] ?? '');
		if (!empty($telefono)) {
			$telefono_clean = preg_replace('/[^0-9+]/', '', $telefono);
			$digits_only = preg_replace('/[^0-9]/', '', $telefono_clean);
			if (strlen($digits_only) < 9 || strlen($digits_only) > 15) {
				wp_send_json_error(['message' => 'El teléfono debe tener entre 9 y 15 dígitos.'], 400);
			}
			if (Avance_PDF_Download_DB::phone_exists($telefono_clean)) {
				wp_send_json_error(['message' => 'Este teléfono ya ha descargado el PDF.'], 400);
			}
			$telefono = sanitize_text_field($telefono_clean);
		}

		// 5. VALIDAR EMPRESA
		$empresa = trim($post_data['empresa'] ?? '');
		if (empty($empresa)) {
			wp_send_json_error(['message' => 'La empresa es requerida.'], 400);
		}
		if (strlen($empresa) < 2) {
			wp_send_json_error(['message' => 'La empresa debe tener mínimo 2 caracteres.'], 400);
		}
		if (strlen($empresa) > 100) {
			wp_send_json_error(['message' => 'La empresa no puede exceder 100 caracteres.'], 400);
		}
		$empresa = sanitize_text_field($empresa);

		// 6. VALIDAR EMPLEADOS
		$empleados = trim($post_data['empleados'] ?? '');
		if (empty($empleados)) {
			wp_send_json_error(['message' => 'El número de empleados es requerido.'], 400);
		}
		$valid_empleados = array('1-10', '11-50', '51-200', '201-1000', '1000+');
		if (!in_array($empleados, $valid_empleados, true)) {
			wp_send_json_error(['message' => 'El número de empleados no es válido.'], 400);
		}
		$empleados = sanitize_text_field($empleados);

		// 7. VALIDAR INDUSTRIA
		$industria = trim($post_data['industria'] ?? '');
		if (empty($industria)) {
			wp_send_json_error(['message' => 'La industria es requerida.'], 400);
		}
		$valid_industrias = array('tecnologia', 'retail', 'finanzas', 'salud', 'manufactura', 'educacion', 'otro');
		if (!in_array($industria, $valid_industrias, true)) {
			wp_send_json_error(['message' => 'La industria no es válida.'], 400);
		}
		$industria = sanitize_text_field($industria);

		// 8. RATE LIMITING (10 intentos por hora por IP)
		$ip = $this->get_client_ip();
		if (!$this->check_rate_limit($ip)) {
			wp_send_json_error(['message' => 'Demasiados intentos. Espera un momento.'], 429);
		}

		// 9. DETECCIÓN DE SPAM
		if (class_exists('Avance_Spam_Detector')) {
			$spam_result = Avance_Spam_Detector::analyze_message($empresa . ' ' . $nombre, $nombre, '');
			if ($spam_result['is_spam']) {
				wp_send_json_error(['message' => 'Tu solicitud fue identificada como spam.'], 400);
			}
		}

		// 10. GUARDAR EN BD
		$download_data = array(
			'nombre' => $nombre,
			'email' => $email,
			'telefono' => $telefono,
			'empresa' => $empresa,
			'empleados' => $empleados,
			'industria' => $industria,
			'ip_address' => $ip,
		);

		$download_id = Avance_PDF_Download_DB::save_download($download_data);
		if (!$download_id) {
			error_log('PDF Download DB Error: No se pudo guardar');
			wp_send_json_error(['message' => 'Error al procesar la solicitud. Intenta de nuevo.'], 500);
		}

		// 11. LOG DE INTENTO EXITOSO
		Avance_Rate_Limiter::log_attempt($ip, $email, 'success');

		// 12. GENERAR NONCE DE DESCARGA
		$download_nonce = wp_create_nonce('pdf_download_' . $download_id);
		$download_url = add_query_arg(array(
			'action' => 'avance_download_pdf',
			'download_id' => $download_id,
			'nonce' => $download_nonce,
		), admin_url('admin-ajax.php'));

		wp_send_json_success(array(
			'message' => 'Descargando PDF...',
			'download_url' => $download_url,
			'download_id' => $download_id,
		));
	}

	private function get_client_ip() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
		}
		return sanitize_text_field($ip);
	}

	private function check_rate_limit($ip) {
		if (!class_exists('Avance_Rate_Limiter')) {
			return true;
		}

		$result = Avance_Rate_Limiter::check_ip($ip);
		return $result['allowed'];
	}

	private function get_pdf_url() {
		$pdf_filename = 'Las 7 claves para transformar su gestión comercial.pdf';
		return get_template_directory_uri() . '/assets/ebooks/' . $pdf_filename;
	}

	private function get_pdf_path() {
		$pdf_filename = 'Las 7 claves para transformar su gestión comercial.pdf';
		return get_template_directory() . '/assets/ebooks/' . $pdf_filename;
	}

	public function handle_pdf_download() {
		$download_id = isset($_GET['download_id']) ? intval($_GET['download_id']) : 0;
		$nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : '';

		if (!$download_id || !$nonce) {
			wp_send_json_error(['message' => 'Parámetros inválidos.'], 400);
		}

		if (!wp_verify_nonce($nonce, 'pdf_download_' . $download_id)) {
			wp_send_json_error(['message' => 'Token inválido.'], 403);
		}

		$pdf_path = $this->get_pdf_path();

		if (!file_exists($pdf_path)) {
			error_log('PDF no encontrado: ' . $pdf_path);
			wp_send_json_error(['message' => 'El archivo PDF no está disponible.'], 404);
		}

		$file_size = filesize($pdf_path);

		if ($file_size === false) {
			wp_send_json_error(['message' => 'Error al leer el archivo.'], 500);
		}

		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="Las 7 claves para transformar su gestión comercial.pdf"');
		header('Content-Length: ' . $file_size);
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: 0');
		header('Pragma: public');

		readfile($pdf_path);
		wp_die();
	}
}

new Avance_PDF_Download_Handler();
