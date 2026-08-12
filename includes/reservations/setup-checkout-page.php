<?php
/**
 * SETUP CHECKOUT PAGE - Crear página de checkout automáticamente
 *
 * Este archivo crea la página de checkout de WooCommerce si no existe
 * y la configura correctamente.
 *
 * @package Avance_Template
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Crear página de checkout automáticamente
 */
add_action('wp_loaded', function() {
	// Solo si WooCommerce está activo
	if (!class_exists('WooCommerce')) {
		return;
	}

	// Verificar si ya se ejecutó
	if (get_option('avance_checkout_page_created')) {
		return;
	}

	// Crear página de checkout
	$checkout_page = wp_insert_post(array(
		'post_type' => 'page',
		'post_title' => 'Finalizar Compra',
		'post_name' => 'finalizar-compra',
		'post_content' => '[woocommerce_checkout]',
		'post_status' => 'publish',
		'post_author' => 1,
	), true);

	// Si se creó exitosamente
	if (!is_wp_error($checkout_page) && $checkout_page) {
		// Configurar como página de checkout en WooCommerce
		update_option('woocommerce_checkout_page_id', $checkout_page);

		// Marcar como creado
		update_option('avance_checkout_page_created', true);

		error_log('✅ Página de checkout creada: ID ' . $checkout_page);
	} else {
		// Si ya existe, actualizarla y publicarla
		$existing = get_page_by_path('finalizar-compra');
		if ($existing) {
			// Asegurarse de que esté en estado "publish"
			wp_update_post(array(
				'ID' => $existing->ID,
				'post_status' => 'publish',
				'post_content' => '[woocommerce_checkout]',
			));

			// Configurar como página de checkout en WooCommerce
			update_option('woocommerce_checkout_page_id', $existing->ID);
			update_option('avance_checkout_page_created', true);

			error_log('✅ Página de checkout existente actualizada a publish: ID ' . $existing->ID);
		}
	}
});

/**
 * Verificar que los métodos de pago estén disponibles
 */
add_action('wp_loaded', function() {
	if (!class_exists('WooCommerce')) {
		return;
	}

	// Si no hay métodos de pago, crear mensaje de alerta
	$payment_gateways = WC()->payment_gateways()->payment_gateways();

	if (empty($payment_gateways) || !array_filter($payment_gateways, function($gateway) {
		return $gateway->is_available();
	})) {
		error_log('⚠️ ADVERTENCIA: No hay métodos de pago configurados en WooCommerce. Ve a WooCommerce → Ajustes → Pagos');
	}
}, 999);
