<?php
/**
 * Reservation Cart Handler - AJAX + WooCommerce
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Reservation_Cart_Handler {

	public function __construct() {
		add_action('wp_ajax_nopriv_avance_add_reservation', array($this, 'handle_add_reservation'));
		add_action('wp_ajax_avance_add_reservation', array($this, 'handle_add_reservation'));
	}

	/**
	 * Manejar adición de reserva al carrito
	 */
	public function handle_add_reservation() {
		// Verificar nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'avance_reservation_form')) {
			wp_send_json_error(array('message' => 'Error de seguridad'));
		}

		// Obtener datos
		$data = array(
			'product_id' => $_POST['product_id'] ?? '',
			'fecha_reserva' => $_POST['fecha_reserva'] ?? '',
			'nombre' => $_POST['nombre'] ?? '',
			'email' => $_POST['email'] ?? '',
			'whatsapp' => $_POST['whatsapp'] ?? '',
			'notas' => $_POST['notas'] ?? '',
		);

		// Sanitizar
		$data = Avance_Reservation_Validator::sanitize($data);

		// Validar
		$validation = Avance_Reservation_Validator::validate($data);
		if (!$validation['valid']) {
			wp_send_json_error(array('errors' => $validation['errors']));
		}

		// Preparar meta data para el carrito
		$cart_item_data = array(
			'fecha_reserva' => $data['fecha_reserva'],
			'nombre_reserva' => $data['nombre'],
			'email_reserva' => $data['email'],
			'whatsapp_reserva' => $data['whatsapp'],
			'notas_reserva' => $data['notas'],
		);

		// Agregar al carrito
		if (WC()->cart) {
			WC()->cart->add_to_cart(
				$data['product_id'],
				1,
				0,
				array(),
				$cart_item_data
			);

			// Guardar datos en sesión (para pre-llenar checkout)
			WC()->session->set('avance_reservation_data', $data);
		}

		// Retornar URL de checkout
		wp_send_json_success(array(
			'message' => 'Reserva agregada. Redirigiendo al pago...',
			'checkout_url' => wc_get_checkout_url(),
		));
	}
}
