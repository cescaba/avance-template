<?php
/**
 * FIX CHECKOUT STATUS - Forzar publicación de página checkout
 */

if (!defined('ABSPATH')) {
	exit;
}

// Este script se ejecuta UNA SOLA VEZ en wp_loaded
add_action('wp_loaded', function() {
	// Solo si WooCommerce está activo
	if (!class_exists('WooCommerce')) {
		return;
	}

	// Verificar si ya se ejecutó este fix
	if (get_option('avance_checkout_status_fixed')) {
		return;
	}

	// Buscar la página
	$checkout_page = get_page_by_path('finalizar-compra');

	if ($checkout_page) {
		// FORZAR estado a "publish" sin importar en qué estado esté ahora
		wp_update_post(array(
			'ID' => $checkout_page->ID,
			'post_status' => 'publish',
			'post_content' => '[woocommerce_checkout]',
		));

		// Asegurar que WooCommerce sepa cuál es la página de checkout
		update_option('woocommerce_checkout_page_id', $checkout_page->ID);

		// Marcar como reparado
		update_option('avance_checkout_status_fixed', true);

		error_log('✅ REPARADO: Página checkout (ID: ' . $checkout_page->ID . ') cambiada a PUBLISH');
	}
}, 999);
