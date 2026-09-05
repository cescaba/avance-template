<?php
/**
 * Mentoria Checkout Handler
 * Crea órdenes en WooCommerce desde el formulario de reserva
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

class Avance_Mentoria_Checkout {

	private static $plan_to_product = [
		'entrada' => 'Entrada',
		'pro' => 'Pro',
		'puntual' => 'Sesión puntual',
	];

	public static function get_product_id($plan) {
		if (!isset(self::$plan_to_product[$plan])) {
			return null;
		}

		$product_name = self::$plan_to_product[$plan];
		$args = [
			'post_type' => 'product',
			'title' => $product_name,
			'posts_per_page' => 1,
		];

		$products = get_posts($args);
		return !empty($products) ? $products[0]->ID : null;
	}

	public static function create_order($data) {
		// Validar que WooCommerce esté activo
		if (!class_exists('WooCommerce')) {
			return [
				'success' => false,
				'message' => 'WooCommerce no está activado'
			];
		}

		// Obtener ID del producto
		$product_id = self::get_product_id($data['plan']);
		if (!$product_id) {
			return [
				'success' => false,
				'message' => 'Plan no encontrado'
			];
		}

		// Crear orden
		$order = wc_create_order();
		if (!$order) {
			return [
				'success' => false,
				'message' => 'Error al crear la orden'
			];
		}

		// Agregar producto
		$order->add_product(wc_get_product($product_id), 1);

		// Datos de facturación
		$order->set_billing_first_name($data['first_name']);
		$order->set_billing_last_name($data['last_name']);
		$order->set_billing_email($data['email']);
		$order->set_billing_phone($data['whatsapp']);

		// Guardar datos de la mentoría como metadatos
		$order->update_meta_data('_mentoria_plan', $data['plan']);
		$order->update_meta_data('_mentoria_fecha', $data['fecha']);
		$order->update_meta_data('_mentoria_hora', $data['hora']);
		$order->update_meta_data('_mentoria_desafio', $data['desafio']);
		$order->update_meta_data('_mentoria_whatsapp', $data['whatsapp']);

		// Calcular totales y guardar
		$order->calculate_totals();
		$order->save();

		return [
			'success' => true,
			'order_id' => $order->get_id(),
			'checkout_url' => $order->get_checkout_payment_url()
		];
	}
}
